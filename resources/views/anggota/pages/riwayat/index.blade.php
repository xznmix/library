@extends('anggota.layouts.app')

@section('title', 'Riwayat Peminjaman')
@section('page-title', 'Riwayat Peminjaman')

@section('content')
<div class="space-y-6">

    {{-- Alert Denda Belum Dibayar --}}
    @if(isset($totalDendaBelumBayar) && $totalDendaBelumBayar > 0)
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-red-800">Anda memiliki denda!</p>
                    <p class="text-sm text-red-700">Total denda belum dibayar: <strong>Rp {{ number_format($totalDendaBelumBayar, 0, ',', '.') }}</strong></p>
                    <p class="text-xs text-red-600">Silakan segera lunasi denda Anda di perpustakaan atau melalui menu pembayaran.</p>
                </div>
            </div>
            <a href="{{ route('petugas.sirkulasi.denda.index') }}" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">
                Bayar Denda
            </a>
        </div>
    </div>
    @endif

    {{-- Filter Tabs --}}
    <div class="bg-white rounded-xl shadow-sm p-2 flex flex-wrap gap-2">
        <a href="{{ route('anggota.riwayat.index') }}" 
           class="px-4 py-2 rounded-lg transition-colors
           {{ !request('status') ? 'bg-biru text-white' : 'hover:bg-gray-100' }}">
            Semua
        </a>
        <a href="{{ route('anggota.riwayat.index', ['status' => 'dipinjam']) }}" 
           class="px-4 py-2 rounded-lg transition-colors
           {{ request('status') == 'dipinjam' ? 'bg-biru text-white' : 'hover:bg-gray-100' }}">
            Sedang Dipinjam
        </a>
        <a href="{{ route('anggota.riwayat.index', ['status' => 'jatuh_tempo']) }}" 
           class="px-4 py-2 rounded-lg transition-colors
           {{ request('status') == 'jatuh_tempo' ? 'bg-biru text-white' : 'hover:bg-gray-100' }}">
            Jatuh Tempo
        </a>
        <a href="{{ route('anggota.riwayat.index', ['status' => 'selesai']) }}" 
           class="px-4 py-2 rounded-lg transition-colors
           {{ request('status') == 'selesai' ? 'bg-biru text-white' : 'hover:bg-gray-100' }}">
            Selesai
        </a>
    </div>

    {{-- Tabel Riwayat Fisik --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-biru" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                Peminjaman Buku Fisik
            </h3>
        </div>

        @if($riwayatFisik->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left">No</th>
                            <th class="px-4 py-3 text-left">Buku</th>
                            <th class="px-4 py-3 text-left">Tanggal Pinjam</th>
                            <th class="px-4 py-3 text-left">Jatuh Tempo</th>
                            <th class="px-4 py-3 text-left">Tanggal Kembali</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Denda</th>
                            <th class="px-4 py-3 text-left">Rating</th>
                            <th class="px-4 py-3 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($riwayatFisik as $index => $item)
                        @php
                            $sudahRating = \App\Models\UlasanBuku::where('user_id', Auth::id())
                                ->where('buku_id', $item->buku_id)
                                ->exists();
                            
                            // Cek denda dari relasi atau langsung dari field
                            $denda = $item->denda_total ?? $item->denda ?? 0;
                            $statusBayar = $item->denda ? $item->denda->payment_status ?? 'pending' : 'pending';
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $riwayatFisik->firstItem() + $index }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $item->buku->judul ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $item->buku->pengarang ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($item->tgl_jatuh_tempo)->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">
                                {{ $item->tanggal_pengembalian ? \Carbon\Carbon::parse($item->tanggal_pengembalian)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-4 py-3">
                                @if($item->status_pinjam == 'dipinjam')
                                    <span class="px-2 py-1 bg-biru-100 text-biru rounded-full text-xs">Dipinjam</span>
                                @elseif($item->status_pinjam == 'terlambat')
                                    <span class="px-2 py-1 bg-oren-100 text-oren rounded-full text-xs">Terlambat</span>
                                @elseif($item->status_pinjam == 'dikembalikan')
                                    <span class="px-2 py-1 bg-hijau-100 text-hijau rounded-full text-xs">Selesai</span>
                                @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-xs">{{ $item->status_pinjam }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($denda > 0)
                                    <div class="text-red-600 font-medium">
                                        Rp {{ number_format($denda, 0, ',', '.') }}
                                    </div>
                                    {{-- @if($statusBayar != 'paid')
                                        <span class="text-xs text-orange-500">Belum dibayar</span>
                                    @else
                                        <span class="text-xs text-green-500">Lunas</span>
                                    @endif --}}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($item->status_pinjam == 'dikembalikan')
                                    @if($sudahRating)
                                        <span class="text-xs text-hijau flex items-center gap-1">
                                            <i class="fas fa-check-circle"></i> Sudah dirating
                                        </span>
                                    @else
                                        <a href="{{ route('anggota.rating.create', $item->id) }}" 
                                           class="text-xs bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-2 py-1 rounded-lg transition flex items-center gap-1">
                                            <i class="fas fa-star text-yellow-500"></i> Beri Rating
                                        </a>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('anggota.riwayat.show', $item->id) }}" 
                                   class="text-biru hover:text-biru-dark text-sm">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="px-4 py-3 border-t">
                {{ $riwayatFisik->withQueryString()->links() }}
            </div>
        @else
            <div class="p-8 text-center text-gray-500">
                <p>Tidak ada riwayat peminjaman</p>
            </div>
        @endif
    </div>

    {{-- Riwayat Digital --}}
    @if($riwayatDigital->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-biru" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                Peminjaman Digital
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">No</th>
                        <th class="px-4 py-3 text-left">Buku</th>
                        <th class="px-4 py-3 text-left">Tanggal Pinjam</th>
                        <th class="px-4 py-3 text-left">Masa Berlaku</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($riwayatDigital as $index => $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $index + 1 }}</td>
                        <td class="px-4 py-3">{{ $item->buku->judul ?? '-' }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($item->tanggal_expired)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            @if($item->status == 'aktif')
                                <span class="px-2 py-1 bg-hijau-100 text-hijau rounded-full text-xs">Aktif</span>
                            @elseif($item->status == 'expired')
                                <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-xs">Expired</span>
                            @else
                                <span class="px-2 py-1 bg-biru-100 text-biru rounded-full text-xs">{{ ucfirst($item->status) }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($item->status == 'aktif')
                                <a href="{{ route('digital.read', ['token' => $item->token_akses, 'expires' => now()->addHours(24)->timestamp, 'signature' => hash_hmac('sha256', $item->token_akses . now()->addHours(24)->timestamp, config('app.key'))]) }}" 
                                   target="_blank"
                                   class="text-biru hover:text-biru-dark text-sm">
                                    Baca
                                </a>
                            @else
                                <span class="text-gray-400 text-sm">Kadaluarsa</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection