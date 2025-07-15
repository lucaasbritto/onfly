<?php

namespace App\Services;

use Illuminate\Notifications\DatabaseNotification;
use App\Models\User;

class NotificationService
{

    public function getAll(User $user){
        return $user->notifications()->latest()->get();
    }


    public function markAllAsRead(User $user): void{
        $user->unreadNotifications->markAsRead();
    }

    
    public function markAsRead(User $user, string $notificationId): void{
        $notification = DatabaseNotification::where('id', $notificationId)
            ->where('notifiable_id', $user->id)
            ->firstOrFail();

        if (!$notification->read_at) {
            $notification->markAsRead();
        }
    }
}
