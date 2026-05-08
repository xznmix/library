@extends('admin.layouts.app')

@section('title', 'Laporan Data Akun')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header Sederhana -->
    <div class="bg-white dark:bg-hitam-800 rounded-lg shadow-sm p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-hitam-800 dark:text-white mb-1">Laporan Data Akun</h1>
                <p class="text-hitam-600 dark:text-gray-400">Total {{ $users->total() }} akun terdaftar</p>
            </div>
            <div class="mt-4 md:mt-0 flex gap-2">
                <a href="{{ route('admin.laporan.export-akun', ['format' => 'pdf']) }}" 
                   class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg inline-flex items-center gap-2">
                    <i class="fas fa-file-pdf"></i>
                    PDF
                </a>
                <a href="{{ route('admin.laporan.export-akun', ['format' => 'excel']) }}" 
                   class="bg-hijau-600 hover:bg-hijau-700 text-white px-4 py-2 rounded-lg inline-flex items-center gap-2">
                    <i class="fas fa-file-excel"></i>
                    Excel
                </a>
            </div>
        </div>
    </div>

    <!-- Tabel Data Akun -->
    <div class="bg-white dark:bg-hitam-800 rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-hitam-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-hitam-500 dark:text-gray-300 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-hitam-500 dark:text-gray-300 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-hitam-500 dark:text-gray-300 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-hitam-500 dark:text-gray-300 uppercase">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-hitam-500 dark:text-gray-300 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-hitam-500 dark:text-gray-300 uppercase">Terdaftar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-hitam-700">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 dark:hover:bg-hitam-700">
                        <td class="px-6 py-4 text-sm text-hitam-800 dark:text-gray-100">{{ $user->id }}</td>
                        <td class="px-6 py-4 text-sm text-hitam-800 dark:text-gray-100">{{ $user->name }}</td>
                        <td class="px-6 py-4 text-sm text-hitam-800 dark:text-gray-100">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($user->role == 'admin') bg-purple-100 text-purple-800
                                @elseif($user->role == 'petugas') bg-biru-100 text-biru-800
                                @else bg-hijau-100 text-hijau-800 @endif">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($user->status == 'active') bg-hijau-100 text-hijau-800
                                @else bg-oren-100 text-oren-800 @endif">
                                {{ $user->status == 'active' ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-hitam-800 dark:text-gray-100">
                            {{ $user->created_at->format('d/m/Y') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-hitam-500 dark:text-gray-400">
                            Tidak ada data akun
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-hitam-700">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection