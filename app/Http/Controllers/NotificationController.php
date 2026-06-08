<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['is_read' => true]);

        return $notification;
    }

    public function fetchAll(){
        $notifications=Notification::where('user_id',auth()->user()->id)->latest()->paginate(25);
        $notificationIds = $notifications->pluck('id');

        // Mark them as read
        Notification::whereIn('id', $notificationIds)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);
        return view('notification',compact('notifications'));
    }
}
