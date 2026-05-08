@extends('petugas.layouts.app')

@section('title', 'Rekap Kunjungan')
@section('page-title', 'Rekap Kunjungan')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Rekap Kunjungan
            </h1>
            <p class="text-sm text-gray-500 mt-1">Riwayat kunjungan perpustakaan</p>
        </div>
        
        <a href="{{ route('petugas.kunjungan.index') }}" 
           class="mt-4 md:mt-0 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
    </div>

    {{-- Filter Form --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}" 
                       class="px-3 py-2 border border-gray-200 rounded-lg">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date', now()->format('Y-m-d')) }}" 
                       class="px-3 py-2 border border-gray-200 rounded-lg">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Jenis</label>
                <select name="jenis" class="px-3 py-2 border border-gray-200 rounded-lg">
                    <option value="">Semua</option>
                    <option value="siswa" {{ request('jenis') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                    <option value="guru" {{ request('jenis') == 'guru' ? 'selected' : '' }}>Guru</option>
                    <option value="pegawai" {{ request('jenis') == 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                    <option value="umum" {{ request('jenis') == 'umum' ? 'selected' : '' }}>Umum</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Tampilkan
            </button>
            <a href="{{ route('petugas.kunjungan.rekap') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">
                Reset
            </a>
        </form>
    </div>

    {{-- Tabel Rekap --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-indigo-600 to-indigo-700 text-white">
                        <th class="px-4 py-3 text-left">No</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Jam</th>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left">Jenis</th>
                        <th class="px-4 py-3 text-left">Kelas/Kontak</th>
                        <th class="px-4 py-3 text-left">Keperluan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rekap as $index => $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $rekap->firstItem() + $index }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') }}</td>
                        <td class="px-4 py-3">{{ $item->nama }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                @if($item->jenis == 'siswa') bg-blue-100 text-blue-800
                                @elseif($item->jenis == 'guru') bg-green-100 text-green-800
                                @elseif($item->jenis == 'pegawai') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($item->jenis) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if($item->kelas)
                                {{ $item->kelas }}
                            @elseif($item->no_hp)
                                {{ $item->no_hp }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $item->keperluan ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            Tidak ada data kunjungan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($rekap->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 bg-gray-50">
                {{ $rekap->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection