<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function index()
    {
        // Ambil data user untuk ditampilkan di tabel
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        
        return view('admin.pages.laporan.index', compact('users'));
    }
    
    public function exportAkun(Request $request)
    {
        $format = $request->get('format', 'pdf');
        $users = User::all();
        
        if ($format == 'excel') {
            return $this->exportExcel($users);
        }
        
        return $this->exportPdf($users);
    }
    
    private function exportPdf($users)
    {
        $pdf = Pdf::loadView('admin.exports.akun-pdf', compact('users'));
        return $pdf->download('data-akun-'.date('Y-m-d').'.pdf');
    }
    
    private function exportExcel($users)
    {
        return Excel::download(new class($users) implements 
            \Maatwebsite\Excel\Concerns\FromCollection,
            \Maatwebsite\Excel\Concerns\WithHeadings,
            \Maatwebsite\Excel\Concerns\WithMapping
        {
            private $users;
            
            public function __construct($users)
            {
                $this->users = $users;
            }
            
            public function collection()
            {
                return $this->users;
            }
            
            public function headings(): array
            {
                return [
                    'ID',
                    'Nama',
                    'Email',
                    'Role',
                    'Status',
                    'Terdaftar'
                ];
            }
            
            public function map($user): array
            {
                return [
                    $user->id,
                    $user->name,
                    $user->email,
                    ucfirst($user->role),
                    $user->status == 'active' ? 'Aktif' : 'Nonaktif',
                    $user->created_at->format('d/m/Y')
                ];
            }
        }, 'data-akun-'.date('Y-m-d').'.xlsx');
    }
}