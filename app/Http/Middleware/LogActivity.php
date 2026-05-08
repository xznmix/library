<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // <<< TAMBAHKAN INI

class LogActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Log hanya untuk method POST, PUT, PATCH, DELETE
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $this->logActivity($request, $response);
        }

        return $response;
    }

    private function logActivity($request, $response)
    {
        try {
            $user = Auth::user();
            
            if (!$user) return;

            $data = [
                'user_id' => $user->id,
                'role' => $user->role,
                'action' => $this->getAction($request),
                'model' => $this->getModel($request),
                'description' => $this->getDescription($request),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ];

            // Untuk update, simpan data lama
            if ($request->method() == 'PUT' || $request->method() == 'PATCH') {
                $id = $request->route('id') ?? $request->route('peminjaman');
                if ($id && $data['model']) {
                    $model = 'App\\Models\\' . $data['model'];
                    if (class_exists($model)) {
                        $oldData = $model::find($id);
                        if ($oldData) {
                            $data['old_data'] = $oldData->toArray();
                        }
                    }
                }
                $data['new_data'] = $request->all();
            }

            // Untuk store, simpan data baru
            if ($request->method() == 'POST') {
                $data['new_data'] = $request->all();
            }

            ActivityLog::create($data);
            
        } catch (\Exception $e) {
            // Silent fail, jangan sampai menggagalkan request
            Log::error('Gagal log activity: ' . $e->getMessage()); // <<< SUDAH BISA
        }
    }

    private function getAction($request)
    {
        return match($request->method()) {
            'POST' => 'create',
            'PUT', 'PATCH' => 'update',
            'DELETE' => 'delete',
            default => 'unknown'
        };
    }

    private function getModel($request)
    {
        $path = $request->path();
        
        if (str_contains($path, 'peminjaman')) return 'Peminjaman';
        if (str_contains($path, 'buku')) return 'Buku';
        if (str_contains($path, 'anggota')) return 'Anggota';
        if (str_contains($path, 'denda')) return 'Denda';
        if (str_contains($path, 'kunjungan')) return 'Kunjungan';
        
        return null;
    }

    private function getDescription($request)
    {
        $user = Auth::user();
        $action = $this->getAction($request);
        $model = $this->getModel($request);
        
        return "{$user->name} melakukan {$action} pada {$model}";
    }
}