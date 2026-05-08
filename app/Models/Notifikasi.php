<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifikasi';

    protected $fillable = [
        'user_id',
        'judul',
        'isi',
        'type',
        'link',
        'read_at'
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relasi ke user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: notifikasi yang belum dibaca
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope: notifikasi untuk user tertentu
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Tandai sebagai sudah dibaca
     */
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    /**
     * Accessor: status baca
     */
    public function getIsReadAttribute()
    {
        return !is_null($this->read_at);
    }

    /**
     * Accessor: badge color berdasarkan type
     */
    public function getBadgeColorAttribute()
    {
        return [
            'info' => 'blue',
            'success' => 'green',
            'warning' => 'yellow',
            'error' => 'red'
        ][$this->type] ?? 'gray';
    }
}