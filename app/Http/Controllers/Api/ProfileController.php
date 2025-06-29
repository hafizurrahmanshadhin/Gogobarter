<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdatePasswordRequest;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Http\Resources\Api\Profile\UpdateProfileResource;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller {
    /**
     * Update user profile.
     *
     * @param UpdateProfileRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse {
        try {
            $user = Auth::user();

            $data = $request->only(['name', 'phone_number', 'address', 'date_of_birth']);

            if ($request->hasFile('avatar')) {
                $currentAvatar = $user->getRawOriginal('avatar');
                if ($currentAvatar && !str_contains($currentAvatar, 'user-dummy-img.jpg')) {
                    Helper::fileDelete($currentAvatar);
                }

                $avatarPath = Helper::fileUpload($request->file('avatar'), 'avatars', $user->name);
                if ($avatarPath) {
                    $data['avatar'] = $avatarPath;
                }
            }

            $user->update($data);

            return Helper::jsonResponse(true, 'Profile updated successfully.', 200, [
                'user' => new UpdateProfileResource($user->fresh()),
            ]);
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'Failed to update profile.', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update the authenticated user's password.
     *
     * @param  UpdatePasswordRequest  $request
     * @return JsonResponse
     * @throws Exception
     */
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse {
        try {
            $user = auth()->user();

            $user->password = Hash::make($request->get('new_password'));
            $user->save();

            return Helper::jsonResponse(true, 'Password updated successfully.', 200);
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'An error occurred', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
