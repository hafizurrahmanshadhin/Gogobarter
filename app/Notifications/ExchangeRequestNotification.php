<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ExchangeRequestNotification extends Notification {
    use Queueable;

    public $type;
    public $message;
    public $data;

    public function __construct($type, $message, $data = []) {
        $this->type    = $type;
        $this->message = $message;
        $this->data    = $data;
    }

    public function via($notifiable) {
        return ['database'];
    }

    public function toDatabase($notifiable) {
        return [
            'type'    => $this->type,
            'message' => $this->message,
            'data'    => $this->data,
        ];
    }
}
