@extends('petugas.layouts.app')

@section('title', 'Riwayat Pembayaran Denda')

@section('content')
<div class="p-4 md:p-6">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Riwayat Pembayaran Denda
                </h1>
                <p class="text-sm text-gray-500 mt-1">Daftar denda yang sudah lunas</p>
            </div>
            <a href="{{ route('petugas.sirkulasi.denda.index') }}" 
               class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                ← Kembali ke Denda Pending
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">No</th>
                        <th class="px-4 py-3 text-left">Anggota</th>
                        <th class="px-4 py-3 text-left">Buku</th>
                        <th class="px-4 py-3 text-right">Jumlah Denda</th>
                        <th class="px-4 py-3 text-left">Metode</th>
                        <th class="px-4 py-3 text-left">Tanggal Bayar</th>
                        <th class="px-4 py-3 text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($dendaLunas as $index => $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $dendaLunas->firstItem() + $index }}</td>
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $item->anggota->name ?? '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $item->anggota->no_anggota ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $item->peminjaman->buku->judul ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-green-600">
                            {{ $item->formatted_amount }}
                        </td>
                        <td class="px-4 py-3">
                            @if($item->payment_method == 'qris')
                                <span class="px-2 py-1 bg-indigo-100 text-indigo-800 rounded-full text-xs">QRIS</span>
                            @elseif($item->payment_method == 'tunai')
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Tunai</span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            {{ $item->paid_at ? \Carbon\Carbon::parse($item->paid_at)->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                                Lunas
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p>Belum ada riwayat pembayaran denda</p>
                        </td>
                    </td>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 border-t">
                    <tr>
                        <td colspan="3" class="px-4 py-3 font-bold text-right">Total Pendapatan:</td>
                        <td class="px-4 py-3 font-bold text-right text-green-600">
                            Rp {{ number_format($totalLunas, 0, ',', '.') }}
                        </td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @if($dendaLunas->hasPages())
        <div class="px-4 py-3 border-t">
            {{ $dendaLunas->links() }}
        </div>
        @endif
    </div>
</div>
@endsection