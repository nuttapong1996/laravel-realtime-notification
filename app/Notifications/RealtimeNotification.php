<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class RealtimeNotification extends Notification
{
    // use ShouldQueue;
    

    public function __construct(
        public string $message
    ) {}

    // กำหนดช่องทางในการส่ง: ลง Database และ Broadcast ผ่าน WebSocket
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    // ข้อมูลที่จะลง Database
    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->message,
            'url' => '#',
        ];
    }

    // ข้อมูลที่จะส่งออกไปหา Laravel Echo แบบ Real-time
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'message' => $this->message,
            'created_at' => now()->diffForHumans(),
        ]);
    }
}
