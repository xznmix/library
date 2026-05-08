@extends('anggota.layouts.app')

@section('title', 'Booking Saya')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">📅 Booking Saya</h1>
        <a href="{{ route('opac.index') }}" class="text-indigo-600 hover:text-indigo-800">
            🔍 Cari Buku Lain →
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Booking</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Buku</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Ambil</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batas Ambil</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($bookings as $booking)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-mono">{{ $booking->kode_booking }}</td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $booking->buku->judul }}</div>
                            <div class="text-xs text-gray-500">{{ $booking->buku->pengarang }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm">{{ \Carbon\Carbon::parse($booking->tanggal_ambil)->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-sm">{{ \Carbon\Carbon::parse($booking->batas_ambil)->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4">
                            @php
                                $statusColor = [
                                    'menunggu' => 'yellow',
                                    'disetujui' => 'green',
                                    'ditolak' => 'red',
                                    'diambil' => 'blue',
                                    'hangus' => 'gray',
                                ][$booking->status] ?? 'gray';
                                
                                $statusIcon = [
                                    'menunggu' => '⏳',
                                    'disetujui' => '✅',
                                    'ditolak' => '❌',
                                    'diambil' => '📖',
                                    'hangus' => '⏰',
                                ][$booking->status] ?? '❓';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800">
                                {{ $statusIcon }} {{ ucfirst($booking->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <a href="{{ route('anggota.booking.show', $booking->id) }}" 
                                   class="text-indigo-600 hover:text-indigo-800 text-sm">Detail</a>
                                @if($booking->status == 'menunggu')
                                    <form action="{{ route('anggota.booking.cancel', $booking->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm" 
                                                onclick="return confirm('Yakin ingin membatalkan booking ini?')">Batalkan</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p>Belum ada booking buku</p>
                            <a href="{{ route('opac.index') }}" class="mt-2 inline-block text-indigo-600 hover:text-indigo-800">
                                Cari buku untuk di-booking →
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $bookings->links() }}
    </div>
</div>
@endsection