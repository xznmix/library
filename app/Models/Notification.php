<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $table = 'notifications';
    
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'link',
        'is_read'
    ];
    
    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime'
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
    
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }
    
    public function markAsRead()
    {
        $this->is_read = true;
        return $this->save();
    }
    
    public static function markAllAsRead($userId)
    {
        return self::where('user_id', $userId)->where('is_read', false)->update(['is_read' => true]);
    }
}