<?php

namespace App\Services;

use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Create notification for a user
     */
    public function createNotification($userId, $title, $content, $type = 'info', $link = null)
    {
        try {
            return Notifikasi::create([
                'user_id' => $userId,
                'judul' => $title,
                'isi' => $content,
                'type' => $type,
                'link' => $link,
                'read_at' => null
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create notification: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create notification for member
     */
    public function createMemberNotification($userId, $title, $content, $type = 'info', $link = null)
    {
        return $this->createNotification($userId, $title, $content, $type, $link);
    }

    /**
     * Create notification for admin
     */
    public function createAdminNotification($title, $content, $type = 'info', $link = null)
    {
        $admins = User::where('role', 'admin')->get();
        
        foreach ($admins as $admin) {
            $this->createNotification($admin->id, $title, $content, $type, $link);
        }
    }

    /**
     * Create notification for kepala pustaka
     */
    public function createKepalaPustakaNotification($title, $content, $type = 'info', $link = null)
    {
        $kepalaPustaka = User::where('role', 'kepala_pustaka')->first();
        
        if ($kepalaPustaka) {
            $this->createNotification($kepalaPustaka->id, $title, $content, $type, $link);
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId, $userId = null)
    {
        try {
            $query = Notifikasi::where('id', $notificationId);
            
            if ($userId) {
                $query->where('user_id', $userId);
            }
            
            return $query->update(['read_at' => now()]);
        } catch (\Exception $e) {
            Log::error('Failed to mark notification as read: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead($userId)
    {
        try {
            return Notifikasi::where('user_id', $userId)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        } catch (\Exception $e) {
            Log::error('Failed to mark all notifications as read: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get unread count for a user
     */
    public function getUnreadCount($userId)
    {
        return Notifikasi::where('user_id', $userId)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Delete old notifications
     */
    public function deleteOldNotifications($days = 30)
    {
        try {
            return Notifikasi::where('created_at', '<', now()->subDays($days))
                ->whereNotNull('read_at')
                ->delete();
        } catch (\Exception $e) {
            Log::error('Failed to delete old notifications: ' . $e->getMessage());
            return 0;
        }
    }
}