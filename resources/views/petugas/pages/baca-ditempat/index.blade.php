@extends('petugas.layouts.app')

@section('title', 'Baca di Tempat')

@section('content')
<div class="p-4 md:p-6">

    {{-- Header --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">📖 Baca di Tempat</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola aktivitas membaca anggota di perpustakaan</p>
        </div>
        <a href="{{ route('petugas.baca-ditempat.create') }}" 
           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg shadow-md transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span class="font-medium">Tambah Baca di Tempat</span>
        </a>
    </div>

    {{-- Statistik Cepat --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white">
            <div class="text-2xl font-bold">{{ $statistik['hari_ini'] ?? 0 }}</div>
            <div class="text-xs opacity-90">Baca Hari Ini</div>
        </div>
        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-4 text-white">
            <div class="text-2xl font-bold">{{ $statistik['sedang_baca'] ?? 0 }}</div>
            <div class="text-xs opacity-90">Sedang Baca</div>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white">
            <div class="text-2xl font-bold">{{ $statistik['total_baca'] ?? 0 }}</div>
            <div class="text-xs opacity-90">Total Selesai</div>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white">
            <div class="text-2xl font-bold">{{ $statistik['total_poin'] ?? 0 }}</div>
            <div class="text-xs opacity-90">Total Poin</div>
        </div>
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl p-4 text-white">
            <div class="text-2xl font-bold">{{ number_format(($statistik['total_baca'] ?? 0) * 5, 0) }}</div>
            <div class="text-xs opacity-90">Estimasi Poin</div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <form method="GET" action="{{ route('petugas.baca-ditempat.index') }}" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Filter Tanggal</label>
                <input type="date" name="tanggal" value="{{ request('tanggal') }}" 
                       class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-200">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Status</label>
                <select name="status" class="px-3 py-2 border rounded-lg">
                    <option value="">Semua Status</option>
                    <option value="sedang_baca" {{ request('status')=='sedang_baca' ? 'selected' : '' }}>Sedang Baca</option>
                    <option value="selesai" {{ request('status')=='selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
            <div class="flex-1">
                <label class="block text-xs text-gray-500 mb-1">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari nama anggota atau judul buku..."
                       class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-200">
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Filter
                </button>
                @if(request()->anyFilled(['tanggal', 'status', 'search']))
                <a href="{{ route('petugas.baca-ditempat.index') }}" class="px-4 py-2 bg-gray-100 rounded-lg ml-2">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tabel Data --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gradient-to-r from-indigo-50 to-purple-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left w-12">#</th>
                        <th class="px-4 py-3 text-left">Waktu Kunjungan</th>
                        <th class="px-4 py-3 text-left">No Barcode</th>
                        <th class="px-4 py-3 text-left">No Anggota</th>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left">Judul Buku</th>
                        <th class="px-4 py-3 text-left">Bentuk Fisik</th>
                        <th class="px-4 py-3 text-left">Lokasi Perpustakaan</th>
                        <th class="px-4 py-3 text-left">Lokasi Ruang</th>
                        <th class="px-4 py-3 text-left">Durasi</th>
                        <th class="px-4 py-3 text-left">Poin</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($bacaDiTempat as $index => $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-gray-500">{{ $bacaDiTempat->firstItem() + $index }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="font-medium">{{ \Carbon\Carbon::parse($item->waktu_mulai)->format('d-m-Y') }}</div>
                            <div class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($item->waktu_mulai)->format('H:i:s') }}</div>
                        </td>
                        <td class="px-4 py-3 font-mono text-xs">{{ $item->barcode_buku ?? '-' }}</td>
                        <td class="px-4 py-3 font-mono text-xs">{{ $item->no_anggota ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $item->user->name ?? '-' }}</div>
                            <div class="text-xs text-gray-400">{{ $item->user->kelas ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium max-w-xs truncate">{{ $item->buku->judul ?? '-' }}</div>
                            <div class="text-xs text-gray-400">{{ $item->buku->pengarang ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">📖 Buku</span>
                        </td>
                        <td class="px-4 py-3">{{ $item->lokasi ?? 'Tambang Ilmu' }}</td>
                        <td class="px-4 py-3">{{ $item->lokasi ?? 'Ruang Baca Umum' }}</td>
                        
                        {{-- DURASI --}}
                        <td class="px-4 py-3">
                            @if($item->status == 'selesai' && $item->waktu_mulai && $item->waktu_selesai)
                                @php
                                    $mulai = \Carbon\Carbon::parse($item->waktu_mulai);
                                    $selesai = \Carbon\Carbon::parse($item->waktu_selesai);
                                    $durasiMenit = $mulai->diffInMinutes($selesai);
                                    $jam = floor($durasiMenit / 60);
                                    $menit = $durasiMenit % 60;
                                @endphp
                                @if($jam > 0)
                                    <span class="font-medium">{{ $jam }}j {{ $menit }}m</span>
                                @else
                                    <span class="font-medium">{{ $menit }}m</span>
                                @endif
                            @elseif($item->durasi_menit)
                                <span class="font-medium">{{ floor($item->durasi_menit / 60) }}j {{ $item->durasi_menit % 60 }}m</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        
                        {{-- POIN --}}
                        <td class="px-4 py-3">
                            @if($item->status == 'selesai')
                                @if($item->poin_didapat)
                                    <span class="text-green-600 font-medium">+{{ $item->poin_didapat }}</span>
                                @else
                                    @php
                                        $mulai = \Carbon\Carbon::parse($item->waktu_mulai);
                                        $selesai = \Carbon\Carbon::parse($item->waktu_selesai);
                                        $durasiMenit = $mulai->diffInMinutes($selesai);
                                        $poin = 5;
                                        if ($durasiMenit >= 30) $poin += 5;
                                        if ($durasiMenit >= 60) $poin += 5;
                                    @endphp
                                    <span class="text-green-600 font-medium">+{{ $poin }}</span>
                                @endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        
                        {{-- AKSI --}}
                        <td class="px-4 py-3 text-center">
                            <div class="flex gap-2 justify-center">
                                <a href="{{ route('petugas.baca-ditempat.show', $item->id) }}" 
                                   class="text-indigo-600 hover:text-indigo-800" title="Detail">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                @if($item->status == 'sedang_baca')
                                <button type="button" 
                                        onclick="confirmSelesai({{ $item->id }}, '{{ addslashes($item->buku->judul ?? 'Buku') }}')" 
                                        class="text-green-600 hover:text-green-800" title="Selesai">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="px-4 py-12 text-center text-gray-500">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <p>Belum ada data baca di tempat</p>
                            <a href="{{ route('petugas.baca-ditempat.create') }}" class="mt-2 inline-block text-indigo-600">
                                + Tambah Sekarang
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($bacaDiTempat->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $bacaDiTempat->withQueryString()->links() }}
        </div>
        @endif

        {{-- Info Footer --}}
        <div class="px-4 py-3 bg-gray-50 border-t text-xs text-gray-500 flex justify-between items-center">
            <div>Menampilkan {{ $bacaDiTempat->firstItem() ?? 0 }}-{{ $bacaDiTempat->lastItem() ?? 0 }} dari {{ $bacaDiTempat->total() ?? 0 }} item</div>
            <div class="flex items-center gap-3">
                <span>⭐ Poin: 5 (dasar) + 5 (≥30 menit) + 5 (≥60 menit)</span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    table {
        min-width: 1000px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmSelesai(id, judulBuku) {
    Swal.fire({
        title: 'Selesaikan Aktivitas Baca?',
        html: `
            <div class="text-left">
                <p>Apakah anggota telah selesai membaca:</p>
                <p class="font-bold text-indigo-600 mt-2">"${judulBuku}"</p>
                <hr class="my-3">
                <div class="bg-yellow-50 p-3 rounded-lg mt-3">
                    <p class="text-sm text-yellow-800">
                        <strong>📌 Informasi:</strong><br>
                        • Poin akan dihitung berdasarkan durasi baca<br>
                        • Data tidak dapat diubah setelah selesai<br>
                        • Poin akan ditambahkan ke akun anggota
                    </p>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '✅ Ya, Selesaikan',
        cancelButtonText: '❌ Batal',
        reverseButtons: false
    }).then((result) => {
        if (result.isConfirmed) {
            // Tampilkan loading
            Swal.fire({
                title: 'Memproses...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Submit form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/petugas/baca-ditempat/${id}/selesai`;
            form.innerHTML = `
                @csrf
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush