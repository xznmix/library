<?php

namespace App\Exports;

use App\Models\ActivityLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;

class ActivitiesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = ActivityLog::with('user');
        
        if ($this->request->filled('role')) {
            $query->where('role', $this->request->role);
        }
        
        if ($this->request->filled('action')) {
            $query->where('action', $this->request->action);
        }
        
        if ($this->request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $this->request->start_date);
        }
        
        if ($this->request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $this->request->end_date);
        }
        
        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Waktu',
            'User',
            'Role',
            'Aksi',
            'Deskripsi',
            'IP Address'
        ];
    }

    public function map($log): array
    {
        static $no = 0;
        $no++;
        
        return [
            $no,
            $log->created_at->format('d/m/Y H:i'),
            $log->user->name ?? $log->user_name ?? 'System',
            ucfirst($log->role ?? 'System'),
            $this->getActionLabel($log->action),
            $log->description ?? '-',
            $log->ip_address ?? '-'
        ];
    }

    private function getActionLabel($action)
    {
        return match($action) {
            'create' => 'Tambah',
            'update' => 'Ubah',
            'delete' => 'Hapus',
            default => $action
        };
    }
}