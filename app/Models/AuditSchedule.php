<?php
// app/Models/AuditSchedule.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditSchedule extends Model
{
    protected $table = 'audit_schedules';
    
    protected $fillable = [
        'buku_id', 'status', 'scheduled_date', 'completed_date', 
        'notes', 'assigned_by'
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'completed_date' => 'date',
    ];

    public function buku()
    {
        return $this->belongsTo(Buku::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}