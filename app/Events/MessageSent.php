<?php

namespace App\Events;

use App\Http\Resources\Api\Chat\MessageResource;
use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public MessageResource $message;

    public function __construct(Message $message) {
        $message->load('sender:id,name,avatar');
        $this->message = new MessageResource($message);
    }

    public function broadcastOn(): array {
        return [
            new PrivateChannel("chat.{$this->message->receiver_id}"),
            new PrivateChannel("chat.{$this->message->sender_id}"),
        ];
    }

    public function broadcastWith(): array {
        return ['message' => $this->message];
    }
}
