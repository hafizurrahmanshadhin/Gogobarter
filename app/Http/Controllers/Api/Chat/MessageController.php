<?php

namespace App\Http\Controllers\Api\Chat;

use App\Events\MessageSent;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Chat\MessageResource;
use App\Models\Message;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller {
    /**
     * Get messages between the authenticated user and another user
     *
     * @param User $user
     * @param Request $request
     * @return JsonResponse
     */
    public function GetMessages(User $user, Request $request): JsonResponse {
        try {
            $authUser = $request->user();

            if (!$authUser) {
                return Helper::jsonResponse(false, 'User not authenticated', 401);
            }

            $authUserId = $authUser->id;

            $messages = Message::where(function ($query) use ($authUserId, $user) {
                $query->where('sender_id', $authUserId)
                    ->where('receiver_id', $user->id);
            })
                ->orWhere(function ($query) use ($authUserId, $user) {
                    $query->where('sender_id', $user->id)
                        ->where('receiver_id', $authUserId);
                })
                ->with([
                    'sender:id,name,avatar',
                ])
                ->orderByDesc('id')
                ->get();

            return Helper::jsonResponse(true, 'Messages retrieved successfully', 200, MessageResource::collection($messages));
        } catch (Exception $e) {
            Log::error('Error retrieving messages: ' . $e->getMessage(), ['exception' => $e]);
            return Helper::jsonResponse(false, 'An error occurred while retrieving messages: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Send a message to another user
     *
     * @param User $user
     * @param Request $request
     * @return JsonResponse
     */
    public function SendMessage(User $user, Request $request): JsonResponse {
        try {
            // Log the incoming request data for debugging
            Log::info('SendMessage Request Data:', [
                'all_data'          => $request->all(),
                'files'             => $request->allFiles(),
                'has_attachments'   => $request->hasFile('attachments') || $request->hasFile('attachment'),
                'attachments_count' => $request->hasFile('attachments') ? count($request->file('attachments')) : ($request->hasFile('attachment') ? count($request->file('attachment')) : 0),
            ]);

            // Flexible validation to handle both 'attachment' and 'attachments'
            $validatedData = $request->validate([
                'message'       => 'nullable|string',
                'attachments'   => 'nullable|array',
                'attachments.*' => 'file|mimes:jpg,jpeg,png,gif,mp4,mp3,mov,avi,doc,docx,pdf,xls,xlsx,txt|max:102400',
                'attachment'    => 'nullable|array',
                'attachment.*'  => 'file|mimes:jpg,jpeg,png,gif,mp4,mp3,mov,avi,doc,docx,pdf,xls,xlsx,txt|max:102400',
            ]);

            // Check if both message and attachments are empty
            if (empty($validatedData['message']) && !$request->hasFile('attachments') && !$request->hasFile('attachment')) {
                return Helper::jsonResponse(false, 'Either message or attachments must be provided', 400);
            }

            $attachments = [];

            // Process file attachments - handle both 'attachments' and 'attachment'
            $fileKey = $request->hasFile('attachments') ? 'attachments' : ($request->hasFile('attachment') ? 'attachment' : null);

            if ($fileKey) {
                Log::info("Processing {$fileKey}...");

                $files = $request->file($fileKey);

                // Handle single file vs array of files
                if (!is_array($files)) {
                    $files = [$files];
                }

                foreach ($files as $index => $file) {
                    if ($file->isValid()) {
                        // Get file info before processing to avoid temp file issues
                        $originalName = $file->getClientOriginalName();
                        $mimeType     = $file->getMimeType();
                        $fileSize     = $file->getSize();

                        Log::info("Processing {$fileKey} {$index}:", [
                            'original_name' => $originalName,
                            'mime_type'     => $mimeType,
                            'size'          => $fileSize,
                        ]);

                        $type = $this->determineFileType($mimeType);
                        $path = Helper::fileUpload($file, 'attachments');

                        if ($path) {
                            $attachments[] = [
                                'url'       => url($path),
                                'name'      => $originalName,
                                'type'      => $type,
                                'size'      => $fileSize,
                                'mime_type' => $mimeType,
                            ];

                            Log::info("Attachment {$index} processed successfully:", [
                                'path' => $path,
                                'url'  => url($path),
                            ]);
                        } else {
                            Log::error("Failed to upload attachment {$index}");
                        }
                    } else {
                        Log::error("Invalid file at index {$index}:", [
                            'error' => $file->getErrorMessage(),
                        ]);
                    }
                }
            }

            Log::info('Final attachments array:', ['attachments' => $attachments]);

            $message = Message::create([
                'sender_id'   => $request->user()->id,
                'receiver_id' => $user->id,
                'text'        => $validatedData['message'] ?? null,
                'attachments' => $attachments,
            ]);

            Log::info('Message created:', [
                'id'                 => $message->id,
                'attachments_stored' => $message->attachments,
            ]);

            // Load the sender relationship before broadcasting
            $message->load('sender:id,name,avatar');

            broadcast(new MessageSent($message))->toOthers();

            return Helper::jsonResponse(true, 'Message sent successfully', 200, new MessageResource($message));
        } catch (Exception $e) {
            Log::error('Error sending message: ' . $e->getMessage(), [
                'exception' => $e,
                'trace'     => $e->getTraceAsString(),
            ]);
            return Helper::jsonResponse(false, 'An error occurred while sending the message: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Determine file type based on MIME type
     *
     * @param string $mimeType
     * @return string
     */
    private function determineFileType(string $mimeType): string {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        } elseif (str_starts_with($mimeType, 'video/')) {
            return 'video';
        } elseif (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        } else {
            return 'document';
        }
    }

    /**
     * Get users with the last message between them and the authenticated user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getUsersWithLastMessage(Request $request): JsonResponse {
        try {
            $authUser = $request->user();

            if (!$authUser) {
                return Helper::jsonResponse(false, 'User not authenticated', 401);
            }

            $userId = $authUser->id;

            $subQuery = Message::query()
                ->select('sender_id', DB::raw('MAX(id) as last_message_id'))
                ->where('receiver_id', $userId)
                ->where('sender_id', '!=', $userId)
                ->groupBy('sender_id');

            $messages = Message::query()
                ->joinSub($subQuery, 'latest_messages', function ($join) {
                    $join->on('messages.id', '=', 'latest_messages.last_message_id');
                })
                ->with('sender:id,name,avatar')
                ->orderByDesc('messages.id')
                ->get();

            return Helper::jsonResponse(true, 'Users with last message retrieved successfully', 200, MessageResource::collection($messages));
        } catch (Exception $e) {
            Log::error('Error retrieving users with last message: ' . $e->getMessage(), ['exception' => $e]);
            return Helper::jsonResponse(false, 'An error occurred while retrieving users with last message: ' . $e->getMessage(), 500);
        }
    }
}
