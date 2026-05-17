@extends('kepala-pustaka.layouts.app')

@section('title', 'Detail Verifikasi Denda')

@section('content')

{{-- Kalkulasi semua variabel di satu tempat, null-safe --}}
@php
    $peminjaman       = $denda->peminjaman;
    $buku             = $peminjaman?->buku;        // sudah withTrashed() dari controller
    $anggota          = $denda->anggota ?? $peminjaman?->user;
    $petugas          = $peminjaman?->petugas;
    $confirmedBy      = $denda->confirmedBy;       // relasi ke User (confirmed_by)

    // Status
    $statusVerifikasi = $denda->payment_status ?? 'pending';   // pending | paid | failed
    // Mapping payment_status → label
    $statusLabel = match($statusVerifikasi) {
        'paid'    => 'disetujui',
        'failed'  => 'ditolak',
        default   => 'pending',
    };

    $catatanVerifikasi = $denda->keterangan ?? '';

    // Tanggal
    $tglPinjam        = $peminjaman?->tanggal_pinjam       ?? null;
    $tglJatuhTempo    = $peminjaman?->tgl_jatuh_tempo      ?? null;
    $tglKembali       = $peminjaman?->tanggal_pengembalian ?? null;

    // Hitung terlambat (gunakan $terlambat dari controller jika ada, fallback hitung ulang)
    $terlambatHari = $terlambat ?? 0;

    // Nominal
    $dendaTerlambat = (int) ($denda->denda_terlambat ?? 0);
    $dendaRusak     = (int) ($denda->denda_kerusakan ?? 0);
    $totalDenda     = (int) ($denda->jumlah_denda ?? ($dendaTerlambat + $dendaRusak));

    // Kondisi buku
    $kondisiKembali  = $peminjaman?->kondisi_kembali ?? $denda->kondisi_kembali ?? 'baik';
    $catatanKondisi  = $peminjaman?->catatan_kondisi ?? $denda->catatan_kondisi ?? '';

    // Helper: warna status
    $statusColor = match($statusLabel) {
        'disetujui' => 'green',
        'ditolak'   => 'red',
        default     => 'yellow',
    };
@endphp

<div class="max-w-5xl mx-auto" x-data="detailVerifikasi()">

    {{-- ===== HEADER ===== --}}
    <div class="mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('kepala-pustaka.verifikasi.index') }}"
               class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detail Verifikasi Denda</h1>
                <p class="text-sm text-gray-500 mt-1">Informasi lengkap transaksi denda #{{ $denda->id }}</p>
            </div>
        </div>
    </div>

    {{-- ===== STATUS BANNER ===== --}}
    <div class="mb-6 p-4 rounded-xl border
        {{ $statusLabel === 'pending'    ? 'bg-yellow-50 border-yellow-200' : '' }}
        {{ $statusLabel === 'disetujui' ? 'bg-green-50  border-green-200'  : '' }}
        {{ $statusLabel === 'ditolak'   ? 'bg-red-50    border-red-200'    : '' }}
    ">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-3">

                {{-- Ikon status --}}
                <div class="w-12 h-12 rounded-full flex items-center justify-center
                    {{ $statusLabel === 'pending'    ? 'bg-yellow-100' : '' }}
                    {{ $statusLabel === 'disetujui' ? 'bg-green-100'  : '' }}
                    {{ $statusLabel === 'ditolak'   ? 'bg-red-100'    : '' }}
                ">
                    @if($statusLabel === 'pending')
                        <svg class="w-6 h-6 text-yellow-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    @elseif($statusLabel === 'disetujui')
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    @else
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    @endif
                </div>

                <div>
                    <h3 class="font-semibold
                        {{ $statusLabel === 'pending'    ? 'text-yellow-800' : '' }}
                        {{ $statusLabel === 'disetujui' ? 'text-green-800'  : '' }}
                        {{ $statusLabel === 'ditolak'   ? 'text-red-800'    : '' }}
                    ">
                        @if($statusLabel === 'pending')    Menunggu Verifikasi
                        @elseif($statusLabel === 'disetujui') Denda Disetujui
                        @else Denda Ditolak
                        @endif
                    </h3>

                    @if($statusLabel !== 'pending')
                        <p class="text-sm {{ $statusLabel === 'disetujui' ? 'text-green-600' : 'text-red-600' }}">
                            Diverifikasi oleh {{ $confirmedBy?->name ?? '-' }}
                            pada {{ $denda->paid_at ? \Carbon\Carbon::parse($denda->paid_at)->format('d/m/Y H:i') : '-' }}
                        </p>
                    @else
                        <p class="text-sm text-yellow-600">
                            Dicatat oleh {{ $petugas?->name ?? '-' }}
                            pada {{ $denda->created_at?->format('d/m/Y H:i') ?? '-' }}
                        </p>
                    @endif
                </div>
            </div>

            @if($statusLabel === 'pending')
                <button @click="openModal({{ $denda->id }}, {{ $totalDenda }})"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg
                               flex items-center gap-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Verifikasi Sekarang
                </button>
            @endif
        </div>

        @if($statusLabel === 'ditolak' && $catatanVerifikasi)
            <div class="mt-3 p-3 bg-white rounded-lg border border-red-200">
                <p class="text-sm text-red-700">
                    <span class="font-medium">Catatan Penolakan:</span>
                    {{ $catatanVerifikasi }}
                </p>
            </div>
        @endif
    </div>

    {{-- ===== QUICK INFO CARDS ===== --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Keterlambatan</p>
            <p class="text-xl font-bold text-red-600">{{ $terlambatHari }} Hari</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Denda per Hari</p>
            <p class="text-xl font-bold text-indigo-600">
                Rp {{ number_format($terlambatHari > 0 ? intdiv($dendaTerlambat, $terlambatHari) : 0, 0, ',', '.') }}
            </p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Total Denda</p>
            <p class="text-xl font-bold text-orange-600">
                Rp {{ number_format($totalDenda, 0, ',', '.') }}
            </p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Kode Eksemplar</p>
            <p class="text-base font-mono font-bold text-gray-800 break-all">
                {{ $peminjaman?->kode_eksemplar ?? $denda->kode_eksemplar ?? '-' }}
            </p>
        </div>
    </div>

    {{-- ===== CONTENT GRID ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT: Informasi Transaksi --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Info Anggota --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Informasi Anggota
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500">Nama</p>
                        <p class="font-medium text-gray-800">{{ $anggota?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">No. Anggota</p>
                        <p class="font-mono text-gray-800">{{ $anggota?->no_anggota ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Jenis</p>
                        <p class="text-gray-800">{{ ucfirst($anggota?->jenis ?? 'Umum') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Kelas / Jurusan</p>
                        <p class="text-gray-800">
                            {{ trim(($anggota?->kelas ?? '') . ' ' . ($anggota?->jurusan ?? '')) ?: '-' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Info Buku --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13
                                 C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13
                                 C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13
                                 C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Informasi Buku
                    @if(!$buku)
                        <span class="ml-2 text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full font-normal">
                            Data tidak tersedia
                        </span>
                    @endif
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <p class="text-xs text-gray-500">Judul</p>
                        <p class="font-medium text-gray-800">{{ $buku?->judul ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Pengarang</p>
                        <p class="text-gray-800">{{ $buku?->pengarang ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">ISBN</p>
                        <p class="font-mono text-gray-800">{{ $buku?->isbn ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Penerbit</p>
                        <p class="text-gray-800">{{ $buku?->penerbit ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Tahun Terbit</p>
                        <p class="text-gray-800">{{ $buku?->tahun_terbit ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Timeline Peminjaman --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Timeline Peminjaman
                </h3>
                <div class="space-y-4">

                    {{-- Tanggal Pinjam --}}
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5
                                         a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800">Tanggal Pinjam</p>
                            <p class="text-sm text-gray-500">
                                {{ $tglPinjam ? \Carbon\Carbon::parse($tglPinjam)->isoFormat('D MMMM Y') : '-' }}
                            </p>
                        </div>
                        @if($tglPinjam)
                            <span class="text-xs text-gray-400 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($tglPinjam)->diffForHumans() }}
                            </span>
                        @endif
                    </div>

                    {{-- Jatuh Tempo --}}
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800">Jatuh Tempo</p>
                            <p class="text-sm text-gray-500">
                                {{ $tglJatuhTempo ? \Carbon\Carbon::parse($tglJatuhTempo)->isoFormat('D MMMM Y') : '-' }}
                            </p>
                        </div>
                    </div>

                    {{-- Tanggal Kembali --}}
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800">Tanggal Kembali</p>
                            <p class="text-sm {{ $terlambatHari > 0 ? 'text-red-500 font-medium' : 'text-gray-500' }}">
                                {{ $tglKembali ? \Carbon\Carbon::parse($tglKembali)->isoFormat('D MMMM Y') : '-' }}
                                @if($terlambatHari > 0)
                                    <span class="text-xs">(terlambat {{ $terlambatHari }} hari)</span>
                                @endif
                            </p>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Riwayat Peminjaman Anggota --}}
            @if($riwayatAnggota->count())
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2
                                 M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Riwayat Peminjaman (5 Terakhir)
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 text-left">
                                <th class="pb-2 text-xs text-gray-500 font-medium">Buku</th>
                                <th class="pb-2 text-xs text-gray-500 font-medium">Tgl Pinjam</th>
                                <th class="pb-2 text-xs text-gray-500 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($riwayatAnggota as $r)
                            <tr>
                                <td class="py-2 text-gray-800">{{ $r->buku?->judul ?? '-' }}</td>
                                <td class="py-2 text-gray-500 whitespace-nowrap">
                                    {{ $r->tanggal_pinjam ? \Carbon\Carbon::parse($r->tanggal_pinjam)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="py-2">
                                    @php
                                        $rStatus = $r->status ?? '-';
                                        $rClass  = match($rStatus) {
                                            'dikembalikan' => 'bg-green-100 text-green-700',
                                            'dipinjam'     => 'bg-blue-100 text-blue-700',
                                            'terlambat'    => 'bg-red-100 text-red-700',
                                            default        => 'bg-gray-100 text-gray-600',
                                        };
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-full text-xs {{ $rClass }}">
                                        {{ ucfirst($rStatus) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        </div>

        {{-- RIGHT: Ringkasan Denda & Aksi --}}
        <div class="space-y-4">

            {{-- Card Total Denda --}}
            <div class="bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-xl p-5 text-white">
                <h3 class="font-semibold mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2
                                 m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1
                                 m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Rincian Denda
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-indigo-200">Denda Keterlambatan</span>
                        <span class="font-semibold">Rp {{ number_format($dendaTerlambat, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-indigo-200">Denda Kerusakan</span>
                        <span class="font-semibold">Rp {{ number_format($dendaRusak, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t border-indigo-400 pt-3">
                        <div class="flex justify-between font-bold text-lg">
                            <span>Total</span>
                            <span>Rp {{ number_format($totalDenda, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kondisi Buku --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Kondisi Buku
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Kondisi Kembali</span>
                        @php
                            $kondisiClass = match($kondisiKembali) {
                                'baik'         => 'bg-green-100 text-green-800',
                                'rusak_ringan' => 'bg-yellow-100 text-yellow-800',
                                'rusak_berat'  => 'bg-orange-100 text-orange-800',
                                'hilang'       => 'bg-red-100 text-red-800',
                                default        => 'bg-gray-100 text-gray-700',
                            };
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $kondisiClass }}">
                            {{ ucfirst(str_replace('_', ' ', $kondisiKembali)) }}
                        </span>
                    </div>
                    @if($catatanKondisi)
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Catatan Petugas</p>
                            <div class="text-sm bg-gray-50 p-3 rounded-lg border-l-4 border-indigo-400">
                                {{ $catatanKondisi }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Petugas Pencatat --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Petugas Pencatat
                </h3>
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                        <span class="font-bold text-indigo-600">
                            {{ strtoupper(substr($petugas?->name ?? 'P', 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">{{ $petugas?->name ?? 'Tidak diketahui' }}</p>
                        <p class="text-xs text-gray-500">{{ ucfirst($petugas?->role ?? 'Petugas') }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $denda->created_at?->format('d/m/Y H:i') ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex gap-2">
                <a href="{{ route('kepala-pustaka.verifikasi.index') }}"
                   class="flex-1 px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white
                          rounded-lg text-center text-sm transition-colors">
                    Kembali
                </a>
                @if($statusLabel === 'pending')
                    <button @click="openModal({{ $denda->id }}, {{ $totalDenda }})"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white
                                   rounded-lg text-sm transition-colors">
                        Verifikasi
                    </button>
                @endif
            </div>

        </div>
    </div>

</div>{{-- end max-w --}}

{{-- ===== MODAL VERIFIKASI ===== --}}
<div x-show="showModal"
     x-cloak
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-center justify-center px-4">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm"
         @click="showModal = false"></div>

    {{-- Panel --}}
    <div class="relative bg-white rounded-xl w-full max-w-md shadow-2xl" @click.stop>

        {{-- Header --}}
        <div class="flex items-center gap-3 p-5 border-b border-gray-200">
            <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3
                             L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="font-semibold text-gray-900">Verifikasi Denda</h3>
                <p class="text-xs text-gray-500" x-text="'ID Denda: #' + selectedId"></p>
            </div>
            <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="p-5 space-y-4">

            {{-- Pilihan Status --}}
            <div>
                <p class="text-sm font-medium text-gray-700 mb-2">Keputusan</p>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center gap-2 p-3 border rounded-lg cursor-pointer transition-colors"
                           :class="status === 'disetujui'
                               ? 'border-green-500 bg-green-50'
                               : 'border-gray-300 hover:border-green-300'">
                        <input type="radio" value="disetujui" x-model="status" class="text-green-600">
                        <span class="text-sm font-medium" :class="status === 'disetujui' ? 'text-green-700' : 'text-gray-700'">
                            ✅ Setujui
                        </span>
                    </label>
                    <label class="flex items-center gap-2 p-3 border rounded-lg cursor-pointer transition-colors"
                           :class="status === 'ditolak'
                               ? 'border-red-500 bg-red-50'
                               : 'border-gray-300 hover:border-red-300'">
                        <input type="radio" value="ditolak" x-model="status" class="text-red-600">
                        <span class="text-sm font-medium" :class="status === 'ditolak' ? 'text-red-700' : 'text-gray-700'">
                            ❌ Tolak
                        </span>
                    </label>
                </div>
            </div>

            {{-- Nominal (jika disetujui) --}}
            <div x-show="status === 'disetujui'" x-transition>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nominal Disetujui
                    <span class="text-xs font-normal text-gray-400">(kosongkan untuk nominal penuh)</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">Rp</span>
                    <input type="number" x-model="nominal" :max="maxDenda" min="0"
                           class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg
                                  focus:ring-2 focus:ring-indigo-300 focus:border-indigo-500
                                  text-sm transition-colors">
                </div>
                <p class="text-xs text-gray-400 mt-1"
                   x-text="'Denda asli: Rp ' + new Intl.NumberFormat(\'id-ID\').format(maxDenda)"></p>
            </div>

            {{-- Catatan (jika ditolak) --}}
            <div x-show="status === 'ditolak'" x-transition>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Catatan Penolakan <span class="text-red-500">*</span>
                </label>
                <textarea x-model="catatan" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg
                                 focus:ring-2 focus:ring-red-300 focus:border-red-500
                                 text-sm resize-none transition-colors"
                          placeholder="Tuliskan alasan penolakan..."></textarea>
            </div>

        </div>

        {{-- Footer --}}
        <div class="flex justify-end gap-2 px-5 pb-5">
            <button type="button" @click="showModal = false"
                    class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200
                           text-gray-700 rounded-lg transition-colors">
                Batal
            </button>
            <button type="button" @click="submitVerifikasi()"
                    :disabled="loading"
                    class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700
                           text-white rounded-lg transition-colors disabled:opacity-60
                           flex items-center gap-2">
                <svg x-show="loading" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10"
                            stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor"
                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291
                             A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                </svg>
                <span x-text="loading ? 'Menyimpan...' : 'Simpan'"></span>
            </button>
        </div>

    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function detailVerifikasi() {
    return {
        // Modal state
        showModal : false,
        loading   : false,
        selectedId: null,
        maxDenda  : 0,

        // Form state
        status  : 'disetujui',
        nominal : '',
        catatan : '',

        // Buka modal
        openModal(id, max) {
            this.selectedId = id;
            this.maxDenda   = max || 0;
            this.status     = 'disetujui';
            this.nominal    = '';
            this.catatan    = '';
            this.showModal  = true;
        },

        // Submit via fetch
        async submitVerifikasi() {
            // Validasi client-side
            if (!this.status) {
                return Swal.fire('Perhatian', 'Pilih keputusan terlebih dahulu.', 'warning');
            }
            if (this.status === 'ditolak' && !this.catatan.trim()) {
                return Swal.fire('Perhatian', 'Catatan penolakan wajib diisi.', 'warning');
            }

            this.loading = true;

            try {
                const body = new FormData();
                body.append('_token', '{{ csrf_token() }}');
                body.append('status', this.status);

                if (this.status === 'disetujui' && this.nominal) {
                    body.append('nominal_setuju', this.nominal);
                }
                if (this.status === 'ditolak') {
                    body.append('catatan', this.catatan);
                }

                const res  = await fetch(`/kepala-pustaka/verifikasi/${this.selectedId}`, {
                    method : 'POST',
                    headers: { 'Accept': 'application/json' },
                    body,
                });

                const json = await res.json();

                if (json.success) {
                    await Swal.fire({
                        icon            : 'success',
                        title           : 'Berhasil!',
                        text            : json.message,
                        confirmButtonColor: '#4f46e5',
                        timer           : 1800,
                        timerProgressBar: true,
                    });
                    window.location.reload();
                } else {
                    Swal.fire({
                        icon : 'error',
                        title: 'Gagal!',
                        text : json.message || 'Terjadi kesalahan.',
                        confirmButtonColor: '#4f46e5',
                    });
                }
            } catch (err) {
                Swal.fire({
                    icon : 'error',
                    title: 'Error!',
                    text : 'Koneksi bermasalah: ' + err.message,
                    confirmButtonColor: '#4f46e5',
                });
            } finally {
                this.loading   = false;
                this.showModal = false;
            }
        },
    };
}
</script>
@endpush

<style>
[x-cloak] { display: none !important; }
</style>
@endsection