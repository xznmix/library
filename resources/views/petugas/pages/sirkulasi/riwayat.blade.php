@extends('petugas.layouts.app')

@section('title', 'Riwayat Peminjaman')

@section('content')
<div class="p-4 md:p-6">

    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Riwayat Peminjaman</h1>
                <p class="text-sm text-gray-500 mt-1">Semua data peminjaman dan pengembalian</p>
            </div>
            <a href="{{ route('petugas.sirkulasi.peminjaman.index') }}" 
               class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                ← Kembali
            </a>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
        <form method="GET" class="flex flex-col md:flex-row gap-3">
            <div class="flex-1">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Cari anggota, judul buku, atau kode..." 
                       class="w-full px-4 py-2 border border-gray-200 rounded-lg">
            </div>
            <select name="status" class="px-4 py-2 border border-gray-200 rounded-lg">
                <option value="">Semua Status</option>
                <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
            </select>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="px-4 py-2 border border-gray-200 rounded-lg">
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="px-4 py-2 border border-gray-200 rounded-lg">
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Filter
            </button>
            @if(request()->anyFilled(['search', 'status', 'start_date', 'end_date']))
                <a href="{{ route('petugas.sirkulasi.riwayat') }}" 
                   class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Tabel Riwayat --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-indigo-600 to-indigo-700 text-white">
                        <th class="px-4 py-3 text-left">No</th>
                        <th class="px-4 py-3 text-left">Kode</th>
                        <th class="px-4 py-3 text-left">Anggota</th>
                        <th class="px-4 py-3 text-left">Buku</th>
                        <th class="px-4 py-3 text-left">Tgl Pinjam</th>
                        <th class="px-4 py-3 text-left">Tgl Kembali</th>
                        <th class="px-4 py-3 text-left">Denda</th>
                        <th class="px-4 py-3 text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($riwayat as $index => $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">{{ $riwayat->firstItem() + $index }}</td>
                        <td class="px-4 py-3 font-mono text-sm">{{ $item->kode_eksemplar }}</td>
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $item->user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $item->user->no_anggota }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $item->buku->judul }}</div>
                            <div class="text-xs text-gray-500">{{ $item->buku->pengarang }}</div>
                        </td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            {{ $item->tanggal_pengembalian ? \Carbon\Carbon::parse($item->tanggal_pengembalian)->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-4 py-3">
                            @if($item->denda > 0)
                                <span class="text-red-600 font-medium">Rp {{ number_format($item->denda, 0, ',', '.') }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $statusLabels = [
                                    'dipinjam' => ['bg-blue-100', 'text-blue-800', 'Dipinjam'],
                                    'terlambat' => ['bg-red-100', 'text-red-800', 'Terlambat'],
                                    'dikembalikan' => ['bg-green-100', 'text-green-800', 'Dikembalikan']
                                ];
                                $status = $statusLabels[$item->status_pinjam] ?? ['bg-gray-100', 'text-gray-800', 'Unknown'];
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $status[0] }} {{ $status[1] }}">
                                {{ $status[2] }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p>Tidak ada data peminjaman</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($riwayat->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $riwayat->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection