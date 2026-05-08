@extends('petugas.layouts.app')

@section('title', 'Detail Baca di Tempat')

@section('content')
<div class="p-4 md:p-6 max-w-5xl mx-auto">
    {{-- Header dengan Tombol Aksi --}}
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('petugas.baca-ditempat.index') }}" 
                   class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">📖 Detail Baca di Tempat</h1>
                    <p class="text-sm text-gray-500 mt-1">ID Transaksi: #{{ str_pad($baca->id, 6, '0', STR_PAD_LEFT) }}</p>
                </div>
            </div>
            
            {{-- Tombol Aksi --}}
            <div class="flex gap-3">
                @if($baca->status == 'sedang_baca')
                <button type="button" 
                        onclick="confirmSelesai()"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow-sm transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Selesaikan Baca</span>
                </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Status Card --}}
    <div class="mb-6">
        <div class="rounded-xl overflow-hidden shadow-sm">
            @if($baca->status == 'sedang_baca')
            <div class="bg-gradient-to-r from-yellow-500 to-orange-500 p-4 text-white">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg">Sedang Baca</h3>
                        <p class="text-sm text-white/90">Anggota sedang membaca buku di perpustakaan</p>
                    </div>
                    <div class="ml-auto">
                        <span class="px-3 py-1 bg-white/20 rounded-full text-sm animate-pulse">● Aktif</span>
                    </div>
                </div>
            </div>
            @else
            <div class="bg-gradient-to-r from-green-500 to-emerald-500 p-4 text-white">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg">Selesai</h3>
                        <p class="text-sm text-white/90">Aktivitas membaca telah selesai</p>
                    </div>
                    <div class="ml-auto">
                        <span class="px-3 py-1 bg-white/20 rounded-full text-sm">✓ Completed</span>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Kolom Kiri: Informasi Anggota --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Card Anggota --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-5 py-3">
                    <h3 class="text-white font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Informasi Anggota
                    </h3>
                </div>
                <div class="p-5">
                    <div class="text-center mb-4">
                        <div class="w-20 h-20 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center mx-auto">
                            <span class="text-3xl font-bold text-indigo-600">
                                {{ strtoupper(substr($baca->user->name ?? 'A', 0, 1)) }}
                            </span>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="border-b pb-2">
                            <p class="text-xs text-gray-500">Nama Lengkap</p>
                            <p class="font-semibold text-gray-800">{{ $baca->user->name ?? '-' }}</p>
                        </div>
                        <div class="border-b pb-2">
                            <p class="text-xs text-gray-500">No. Anggota</p>
                            <p class="font-mono text-sm font-semibold text-indigo-600">{{ $baca->user->no_anggota ?? '-' }}</p>
                        </div>
                        <div class="border-b pb-2">
                            <p class="text-xs text-gray-500">Kelas / Status</p>
                            <p class="font-medium text-gray-800">{{ $baca->user->kelas ?? $baca->user->role ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Email</p>
                            <p class="font-medium text-gray-800 text-sm">{{ $baca->user->email ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card Lokasi --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-cyan-600 px-5 py-3">
                    <h3 class="text-white font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Lokasi & Catatan
                    </h3>
                </div>
                <div class="p-5 space-y-3">
                    <div>
                        <p class="text-xs text-gray-500">Lokasi Ruang</p>
                        <p class="font-medium text-gray-800">{{ $baca->lokasi ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Catatan</p>
                        <div class="bg-gray-50 rounded-lg p-3 mt-1">
                            <p class="text-gray-700">{{ $baca->catatan ?? 'Tidak ada catatan' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Informasi Buku dan Waktu --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Card Buku --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-500 to-teal-600 px-5 py-3">
                    <h3 class="text-white font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        Informasi Buku
                    </h3>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="border-b pb-2">
                            <p class="text-xs text-gray-500">Judul Buku</p>
                            <p class="font-semibold text-gray-800">{{ $baca->buku->judul ?? '-' }}</p>
                        </div>
                        <div class="border-b pb-2">
                            <p class="text-xs text-gray-500">Pengarang</p>
                            <p class="font-medium text-gray-800">{{ $baca->buku->pengarang ?? '-' }}</p>
                        </div>
                        <div class="border-b pb-2">
                            <p class="text-xs text-gray-500">Penerbit</p>
                            <p class="font-medium text-gray-800">{{ $baca->buku->penerbit ?? '-' }}</p>
                        </div>
                        <div class="border-b pb-2">
                            <p class="text-xs text-gray-500">Tahun Terbit</p>
                            <p class="font-medium text-gray-800">{{ $baca->buku->tahun_terbit ?? '-' }}</p>
                        </div>
                        <div class="border-b pb-2">
                            <p class="text-xs text-gray-500">Barcode</p>
                            <p class="font-mono text-sm text-indigo-600">{{ $baca->barcode_buku ?? '-' }}</p>
                        </div>
                        <div class="border-b pb-2">
                            <p class="text-xs text-gray-500">Lokasi Rak</p>
                            <p class="font-medium text-gray-800">{{ $baca->buku->rak ?? '-' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-xs text-gray-500">Kategori</p>
                            <span class="inline-block px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm font-medium mt-1">
                                {{ $baca->buku->kategori->nama ?? '-' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card Waktu dan Poin --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-amber-500 to-orange-600 px-5 py-3">
                    <h3 class="text-white font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Waktu & Poin
                    </h3>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-500">Waktu Mulai</p>
                            <p class="font-semibold text-gray-800">
                                {{ \Carbon\Carbon::parse($baca->waktu_mulai)->translatedFormat('d F Y') }}
                            </p>
                            <p class="text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($baca->waktu_mulai)->format('H:i:s') }}
                            </p>
                        </div>
                        
                        @if($baca->waktu_selesai)
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-500">Waktu Selesai</p>
                            <p class="font-semibold text-gray-800">
                                {{ \Carbon\Carbon::parse($baca->waktu_selesai)->translatedFormat('d F Y') }}
                            </p>
                            <p class="text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($baca->waktu_selesai)->format('H:i:s') }}
                            </p>
                        </div>
                        
                        @php
                            $mulai = \Carbon\Carbon::parse($baca->waktu_mulai);
                            $selesai = \Carbon\Carbon::parse($baca->waktu_selesai);
                            $durasiMenit = $mulai->diffInMinutes($selesai);
                            $jam = floor($durasiMenit / 60);
                            $menit = $durasiMenit % 60;
                            
                            // HITUNG POIN ULANG jika poin_didapat masih 0
                            $poinDapat = $baca->poin_didapat ?? 0;
                            if ($poinDapat == 0 && $baca->status == 'selesai') {
                                $poinDasar = 5;
                                $poinBonus = 0;
                                if ($durasiMenit >= 30) $poinBonus += 5;
                                if ($durasiMenit >= 60) $poinBonus += 5;
                                $poinDapat = $poinDasar + $poinBonus;
                            }
                        @endphp
                        
                        <div class="bg-green-50 rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-500">Durasi Baca</p>
                            <p class="font-bold text-2xl text-green-600">
                                @if($jam > 0)
                                    {{ $jam }} jam {{ $menit }} menit
                                @else
                                    {{ $menit }} menit
                                @endif
                            </p>
                        </div>
                        <div class="bg-yellow-50 rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-500">Poin Didapat</p>
                            <p class="font-bold text-2xl text-yellow-600">
                                +{{ $poinDapat }}
                            </p>
                            <p class="text-xs text-gray-500">poin</p>
                            @if($baca->poin_didapat == 0 && $baca->status == 'selesai')
                                <p class="text-xs text-red-500 mt-1">⚠️ Perlu update poin</p>
                            @endif
                        </div>
                        @else
                        <div class="md:col-span-2 bg-blue-50 rounded-lg p-3 text-center">
                            <p class="text-sm text-blue-600">⏳ Aktivitas baca masih berlangsung</p>
                            <p class="text-xs text-blue-500 mt-1">Poin akan dihitung setelah selesai membaca</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Card Petugas --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-600 to-gray-700 px-5 py-3">
                    <h3 class="text-white font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Informasi Petugas
                    </h3>
                </div>
                <div class="p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500">Dicatat oleh</p>
                            <p class="font-semibold text-gray-800">{{ $baca->petugas->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Tanggal Input</p>
                            <p class="font-medium text-gray-800">{{ $baca->created_at ? \Carbon\Carbon::parse($baca->created_at)->format('d/m/Y H:i') : '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Selesai Baca --}}
<div id="modalSelesai" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-full max-w-md mx-4 transform transition-all">
        <div class="text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Selesaikan Baca?</h3>
            <p class="text-gray-600 mb-4">
                Konfirmasi bahwa anggota telah selesai membaca buku:
                <br><strong class="text-indigo-600">"{{ $baca->buku->judul ?? '-' }}"</strong>
            </p>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4 text-left">
                <p class="text-sm text-yellow-800">
                    <strong>📌 Informasi:</strong><br>
                    • Poin akan dihitung berdasarkan durasi baca<br>
                    • Data tidak dapat diubah setelah selesai<br>
                    • Poin akan ditambahkan ke akun anggota
                </p>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="tutupModal()" 
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <form action="{{ route('petugas.baca-ditempat.selesai', $baca->id) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" 
                            class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        Ya, Selesaikan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function confirmSelesai() {
    document.getElementById('modalSelesai').style.display = 'flex';
}

function tutupModal() {
    document.getElementById('modalSelesai').style.display = 'none';
}

// Tutup modal jika klik di luar
document.getElementById('modalSelesai').addEventListener('click', function(e) {
    if (e.target === this) {
        tutupModal();
    }
});
</script>
@endpush