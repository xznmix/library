@extends('petugas.layouts.app')

@section('title', 'Kunjungan Perpustakaan')
@section('page-title', 'Kunjungan')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Kunjungan Perpustakaan
            </h1>
            <p class="text-sm text-gray-500 mt-1 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                {{ now()->translatedFormat('l, d F Y') }}
            </p>
        </div>

        <div class="mt-4 md:mt-0">
            <a href="{{ route('petugas.kunjungan.rekap') }}" 
               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-all shadow-sm hover:shadow-md">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Rekap Kunjungan
            </a>
        </div>
    </div>

    {{-- Statistik Sederhana --}}
    <div class="grid grid-cols-1 sm:grid-cols-1 gap-4">
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl p-5 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-indigo-100 text-sm">Total Kunjungan Hari Ini</p>
                    <p class="text-4xl font-bold mt-1">{{ $totalHariIni }}</p>
                </div>
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter dan Pencarian --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-[200px]">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" 
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Cari nama atau NISN..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-200">
                </div>
            </div>
            
            <select name="jenis" class="px-4 py-2 border border-gray-200 rounded-lg">
                <option value="">Semua Jenis</option>
                <option value="siswa" {{ request('jenis') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                <option value="guru" {{ request('jenis') == 'guru' ? 'selected' : '' }}>Guru</option>
                <option value="pegawai" {{ request('jenis') == 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                <option value="umum" {{ request('jenis') == 'umum' ? 'selected' : '' }}>Umum</option>
            </select>
            
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Filter
            </button>
            
            @if(request()->anyFilled(['search', 'jenis']))
                <a href="{{ route('petugas.kunjungan.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Tabel Kunjungan --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-indigo-600 to-indigo-700 text-white">
                        <th class="px-4 py-3 text-left">No</th>
                        <th class="px-4 py-3 text-left">Jam</th>
                        <th class="px-4 py-3 text-left">Foto</th>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left">Jenis</th>
                        <th class="px-4 py-3 text-left">Kelas/Kontak</th>
                        <th class="px-4 py-3 text-left">NISN/NIK</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($kunjunganHariIni as $index => $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-gray-500">{{ $kunjunganHariIni->firstItem() + $index }}</td>
                        
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') }}</div>
                        </td>
                        
                        <td class="px-4 py-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 overflow-hidden">
                                @if($item->user && $item->user->foto_ktp)
                                    <img src="{{ asset('storage/'.$item->user->foto_ktp) }}" 
                                         alt="{{ $item->nama }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-indigo-100">
                                        <span class="text-sm font-bold text-indigo-600">
                                            {{ strtoupper(substr($item->nama, 0, 1)) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </td>
                        
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ $item->nama }}</div>
                            @if($item->user && $item->user->no_anggota)
                                <div class="text-xs text-gray-500">{{ $item->user->no_anggota }}</div>
                            @endif
                        </td>
                        
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                @if($item->jenis == 'siswa') bg-blue-100 text-blue-800
                                @elseif($item->jenis == 'guru') bg-green-100 text-green-800
                                @elseif($item->jenis == 'pegawai') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($item->jenis) }}
                            </span>
                        </td>
                        
                        {{-- KELAS/KONTAK --}}
                        <td class="px-4 py-3">
                            @if($item->kelas)
                                <div class="text-sm">{{ $item->kelas }}</div>
                            @elseif($item->no_hp)
                                <div class="text-sm">{{ $item->no_hp }}</div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        
                        {{-- NISN/NIK --}}
                        <td class="px-4 py-3">
                            @if($item->user)
                                {{ $item->user->nisn_nik ?? '-' }}
                            @else
                                -
                            @endif
                        </td>
                        
                        {{-- AKSI --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('petugas.kunjungan.show', $item->id) }}" 
                                   class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                   title="Detail">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                
                                <button type="button" 
                                        onclick="confirmDeleteKunjungan({{ $item->id }}, '{{ addslashes($item->nama) }}')"
                                        class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                        title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-20 h-20 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <p class="text-gray-500 text-lg">Belum ada kunjungan hari ini</p>
                                <p class="text-sm text-gray-400 mt-1">Kunjungan akan muncul di sini</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Info Tambahan --}}
        <div class="px-4 py-3 border-t border-gray-100 bg-gray-50 text-sm text-gray-600 flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-4">
                <span>Total: <strong>{{ $kunjunganHariIni->total() }}</strong> kunjungan</span>
                <span class="w-px h-4 bg-gray-300 hidden sm:inline"></span>
                <span>Hari ini: <span class="text-indigo-600 font-medium">{{ $totalHariIni }}</span></span>
            </div>
            <div class="text-xs text-gray-400">
                Terakhir diperbarui: {{ now()->format('H:i:s') }}
            </div>
        </div>

        {{-- Pagination --}}
        @if($kunjunganHariIni->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $kunjunganHariIni->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDeleteKunjungan(id, nama) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        html: `Anda akan menghapus data kunjungan <strong class="text-red-600">"${nama}"</strong>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Menghapus...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/petugas/kunjungan/${id}`;
            form.innerHTML = `@csrf @method('DELETE')`;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush