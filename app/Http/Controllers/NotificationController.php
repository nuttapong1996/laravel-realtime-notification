<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\RealtimeNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function sendNoti($userId)
    {
        $user = User::find($userId, 'id');

        if (!$user) {
            return "ไม่พบผู้ใช้";
        }

        $message = "มีคิวงานใหม่ถูกมอบหมายให้คุณ ณ เวลา " . now()->format('H:i:s');

        $user->notify(new RealtimeNotification($message));

        return "ส่งแจ้งเตือนให้คุณ {$user->name} สำเร็จแล้ว!";
    }

    // เปลี่ยนสถานะเป็น "อ่านแล้ว" (1 รายการ)
    public function markAsRead($id)
    {
        
        $notification = auth()->user()->notifications()->where('id', $id)->first();

        if ($notification && $notification->unread()) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 400);
    }

    // เปลี่ยนสถานะเป็น "อ่านแล้ว" (ทั้งหมด)
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }

    // ลบการแจ้งเตือน
    public function destroy($id)
    {
        $notification = auth()->user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->delete();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }
}
