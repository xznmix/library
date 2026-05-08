@extends('petugas.layouts.app')

@section('title', 'Data Peminjaman')

@section('content')
<div class="p-4 md:p-6 max-w-7xl mx-auto">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                Data Peminjaman Buku
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Riwayat transaksi peminjaman perpustakaan
            </p>
        </div>
        <a href="{{ route('petugas.sirkulasi.peminjaman.create') }}"
           class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Peminjaman
        </a>
    </div>

    {{-- ALERT SUCCESS --}}
    @if(session('success'))
    <div class="mb-4 p-4 rounded-lg bg-green-50 border border-green-200 text-green-700">
        {{ session('success') }}
    </div>
    @endif

    {{-- ALERT ERROR --}}
    @if(session('error'))
    <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200 text-red-700">
        {{ session('error') }}
    </div>
    @endif

    {{-- TABLE --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left">No</th>
                    <th class="px-4 py-3 text-left">Anggota</th>
                    <th class="px-4 py-3 text-left">Buku</th>
                    <th class="px-4 py-3 text-left">Kode Eksemplar</th>
                    <th class="px-4 py-3 text-left">Tgl Pinjam</th>
                    <th class="px-4 py-3 text-left">Jatuh Tempo</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Detail</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse($peminjaman as $p)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        {{ $loop->iteration }}
                    </td>
                    <td class="px-4 py-3 font-medium">
                        {{ $p->user->name ?? '-' }}
                        <div class="text-xs text-gray-500">{{ $p->user->no_anggota ?? '-' }}</div>
                    </td>
                    <td class="px-4 py-3">
                        {{ $p->buku->judul ?? '-' }}
                        <div class="text-xs text-gray-500">{{ $p->buku->pengarang ?? '-' }}</div>
                    </td>
                    <td class="px-4 py-3 font-mono text-xs">
                        {{ $p->kode_eksemplar }}
                    </td>
                    <td class="px-4 py-3">
                        {{ \Carbon\Carbon::parse($p->tanggal_pinjam)->format('d/m/Y') }}
                    </td>
                    <td class="px-4 py-3">
                        {{ \Carbon\Carbon::parse($p->tgl_jatuh_tempo)->format('d/m/Y') }}
                        @php
                            $today = now();
                            $jatuhTempo = \Carbon\Carbon::parse($p->tgl_jatuh_tempo);
                            $terlambat = $today > $jatuhTempo && in_array($p->status_pinjam, ['dipinjam', 'terlambat']);
                        @endphp
                        @if($terlambat)
                            <div class="text-xs text-red-600 font-bold">
                                Terlambat {{ floor($today->diffInDays($jatuhTempo)) }} hari
                            </div>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($p->status_pinjam == 'dipinjam')
                            <span class="px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded-full">
                                Dipinjam
                            </span>
                        @elseif($p->status_pinjam == 'terlambat')
                            <span class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded-full">
                                Terlambat
                            </span>
                        @elseif($p->status_pinjam == 'dikembalikan')
                            <span class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded-full">
                                Dikembalikan
                            </span>
                            @if($p->denda_total > 0)
                                <div class="text-xs text-red-600 mt-1">
                                    Denda: Rp {{ number_format($p->denda_total, 0, ',', '.') }}
                                </div>
                            @endif
                        @elseif($p->status_pinjam == 'diperpanjang')
                            <span class="px-3 py-1 text-xs bg-purple-100 text-purple-700 rounded-full">
                                Diperpanjang
                            </span>
                        @else
                            <span class="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-full">
                                {{ $p->status_pinjam }}
                            </span>
                        @endif
                    </td>

                    <td class="px-4 py-3">
                        <a href="{{ route('petugas.sirkulasi.peminjaman.show', $p->id) }}"
                           class="text-indigo-600 hover:text-indigo-800 text-sm flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-10 text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        <p>Belum ada data peminjaman</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    @if($peminjaman->hasPages())
    <div class="mt-4">
        {{ $peminjaman->links() }}
    </div>
    @endif

</div>
@endsection