<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            $request->user()->notifications()->latest()->get()
        );
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['message' => 'Todas as notificações foram marcadas como lidas']);
    }


    public function markAsRead($id){
        $notification = DatabaseNotification::where('id', $id)
        ->where('notifiable_id', auth()->id())
        ->firstOrFail();

        if (!$notification->read_at) {
            $notification->markAsRead();
        }

        return response()->json(['message' => 'Notificação marcada como lida.']);
    }
}
