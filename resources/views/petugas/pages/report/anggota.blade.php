@extends('petugas.layouts.app')

@section('title', 'Laporan Anggota')

@section('content')
<div class="p-4 md:p-6">

    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('petugas.report.index') }}" 
               class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">👥 Laporan Anggota</h1>
                <p class="text-sm text-gray-500 mt-1">Data dan statistik keanggotaan perpustakaan</p>
            </div>
        </div>
    </div>

    {{-- Filter dan Export --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <div class="flex flex-wrap gap-3 justify-between items-center">
            <form method="GET" class="flex flex-wrap gap-3">
                <div>
                    <select name="status" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div>
                    <select name="jenis" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                        <option value="">Semua Jenis</option>
                        <option value="siswa" {{ request('jenis') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                        <option value="guru" {{ request('jenis') == 'guru' ? 'selected' : '' }}>Guru</option>
                        <option value="pegawai" {{ request('jenis') == 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                        <option value="umum" {{ request('jenis') == 'umum' ? 'selected' : '' }}>Umum</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Filter</button>
                <a href="{{ route('petugas.report.anggota') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg">Reset</a>
            </form>
            
            {{-- Tombol Export --}}
            <div class="flex gap-2">
                <a href="{{ route('petugas.report.anggota.export.excel', request()->all()) }}" 
                   class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export Excel
                </a>
                <a href="{{ route('petugas.report.anggota.export.pdf', request()->all()) }}" 
                   class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export PDF
                </a>
            </div>
        </div>
    </div>

    {{-- Statistik --}}
    @php
        $totalAnggota = $anggota->count();
        $totalAktif = $anggota->where('status_anggota', 'active')->count();
        $totalPending = $anggota->where('status_anggota', 'pending')->count();
        $totalPinjam = $anggota->sum('peminjaman_count');
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-6">
        <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100">
            <p class="text-2xl font-bold text-indigo-600">{{ $totalAnggota }}</p>
            <p class="text-xs text-gray-600">Total Anggota</p>
        </div>
        <div class="bg-green-50 p-4 rounded-xl border border-green-100">
            <p class="text-2xl font-bold text-green-600">{{ $totalAktif }}</p>
            <p class="text-xs text-gray-600">Aktif</p>
        </div>
        <div class="bg-yellow-50 p-4 rounded-xl border border-yellow-100">
            <p class="text-2xl font-bold text-yellow-600">{{ $totalPending }}</p>
            <p class="text-xs text-gray-600">Pending</p>
        </div>
        <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
            <p class="text-2xl font-bold text-blue-600">{{ $totalPinjam }}</p>
            <p class="text-xs text-gray-600">Total Peminjaman</p>
        </div>
    </div>

    {{-- Tabel Anggota --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="px-4 py-3 text-left">No</th>
                        <th class="px-4 py-3 text-left">No. Anggota</th>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left">Jenis</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Tgl Daftar</th>
                        <th class="px-4 py-3 text-left">Masa Berlaku</th>
                        <th class="px-4 py-3 text-left">Total Pinjam</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($anggota as $index => $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $index + 1 }}</td>
                        <td class="px-4 py-2 font-mono">{{ $item->no_anggota ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $item->name }}</td>
                        <td class="px-4 py-2 capitalize">{{ $item->jenis ?? 'Umum' }}</td>
                        <td class="px-4 py-2">
                            @if($item->status_anggota == 'active')
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">Aktif</span>
                            @elseif($item->status_anggota == 'pending')
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs">Pending</span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">{{ $item->tanggal_daftar ? \Carbon\Carbon::parse($item->tanggal_daftar)->format('d/m/Y') : '-' }}</td>
                        <td class="px-4 py-2">{{ $item->masa_berlaku ? \Carbon\Carbon::parse($item->masa_berlaku)->format('d/m/Y') : '-' }}</td>
                        <td class="px-4 py-2">{{ $item->peminjaman_count }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                            Tidak ada data anggota
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection