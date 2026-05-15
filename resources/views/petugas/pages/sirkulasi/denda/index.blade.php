@extends('petugas.layouts.app')

@section('title', 'Daftar Denda')

@section('content')
<div class="p-4 md:p-6">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Daftar Denda</h1>
            <p class="text-sm text-gray-500">Daftar denda yang belum dibayar</p>
        </div>
        <a href="{{ route('petugas.sirkulasi.denda.lunas') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
            Riwayat Lunas →
        </a>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">No</th>
                        <th class="px-4 py-3 text-left">Anggota</th>
                        <th class="px-4 py-3 text-left">Buku</th>
                        <th class="px-4 py-3 text-right">Jumlah Denda</th>
                        <th class="px-4 py-3 text-left">Kode Bayar</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($denda as $index => $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $denda->firstItem() + $index }}</td>
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $item->anggota->name ?? '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $item->anggota->no_anggota ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $item->peminjaman->buku->judul ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-red-600">
                            {{ $item->formatted_amount }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs">{{ $item->kode_pembayaran ?? '-' }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($item->payment_status == 'pending')
                                <a href="{{ route('petugas.sirkulasi.pembayaran.show', $item->id) }}"
                                   class="px-3 py-1.5 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 inline-flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Bayar
                                </a>
                            @else
                                <span class="px-3 py-1.5 bg-green-100 text-green-700 rounded-lg text-sm">✅ Lunas</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                            <p>Tidak ada denda pending</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($totalPending > 0)
                <tfoot class="bg-gray-50 border-t">
                    <tr>
                        <td colspan="3" class="px-4 py-3 font-bold text-right">Total Denda:</td>
                        <td class="px-4 py-3 font-bold text-right text-red-600">
                            Rp {{ number_format($totalPending, 0, ',', '.') }}
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        @if($denda->hasPages())
        <div class="px-4 py-3 border-t">
            {{ $denda->links() }}
        </div>
        @endif
    </div>
</div>
@endsection