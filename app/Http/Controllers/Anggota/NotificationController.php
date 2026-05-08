<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('anggota.pages.notifications.index', compact('notifications'));
    }
    
    public function getUnreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();
            
        return response()->json(['count' => $count]);
    }
    
    public function getLatest()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        $unreadCount = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();
            
        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $notifications
        ]);
    }
    
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);
            
        $notification->markAsRead();
        
        return response()->json(['success' => true]);
    }
    
    public function markAllAsRead()
    {
        Notification::markAllAsRead(Auth::id());
        
        return response()->json(['success' => true]);
    }
    
    public function destroy($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);
            
        $notification->delete();
        
        return response()->json(['success' => true]);
    }
}