@extends('petugas.layouts.app')

@section('title', 'Manajemen Booking')

@section('content')
<div class="p-4 md:p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">📅 Manajemen Booking</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola booking buku dari anggota</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Statistik --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
        <div class="bg-white p-4 rounded-xl shadow-sm border">
            <div class="text-gray-500 text-sm">Total Booking</div>
            <div class="text-2xl font-bold text-gray-800">{{ $statistik['total'] }}</div>
        </div>
        <div class="bg-yellow-50 p-4 rounded-xl shadow-sm border border-yellow-200">
            <div class="text-yellow-600 text-sm">Menunggu</div>
            <div class="text-2xl font-bold text-yellow-700">{{ $statistik['menunggu'] }}</div>
        </div>
        <div class="bg-green-50 p-4 rounded-xl shadow-sm border border-green-200">
            <div class="text-green-600 text-sm">Disetujui</div>
            <div class="text-2xl font-bold text-green-700">{{ $statistik['disetujui'] }}</div>
        </div>
        <div class="bg-blue-50 p-4 rounded-xl shadow-sm border border-blue-200">
            <div class="text-blue-600 text-sm">Sudah Diambil</div>
            <div class="text-2xl font-bold text-blue-700">{{ $statistik['diambil'] }}</div>
        </div>
        <div class="bg-gray-50 p-4 rounded-xl shadow-sm border border-gray-200">
            <div class="text-gray-600 text-sm">Hangus</div>
            <div class="text-2xl font-bold text-gray-700">{{ $statistik['hangus'] }}</div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white p-4 rounded-xl shadow-sm mb-6">
        <form method="GET" class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari kode booking, anggota, atau judul buku..."
                       class="w-full px-4 py-2 border rounded-lg">
            </div>
            <select name="status" class="px-4 py-2 border rounded-lg">
                <option value="">Semua Status</option>
                <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                <option value="diambil" {{ request('status') == 'diambil' ? 'selected' : '' }}>Diambil</option>
                <option value="hangus" {{ request('status') == 'hangus' ? 'selected' : '' }}>Hangus</option>
            </select>
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Filter</button>
            @if(request()->anyFilled(['search', 'status']))
                <a href="{{ route('petugas.booking.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Reset</a>
            @endif
        </form>
    </div>

    {{-- Tabel Booking --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Anggota</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Buku</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl Ambil</th>
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
                            <div class="font-medium">{{ $booking->user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $booking->user->phone }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium">{{ $booking->buku->judul }}</div>
                            <div class="text-xs text-gray-500">{{ $booking->buku->rak ?? 'Rak Umum' }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm">{{ \Carbon\Carbon::parse($booking->tanggal_ambil)->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-sm {{ now()->greaterThan($booking->batas_ambil) && $booking->status == 'disetujui' ? 'text-red-600 font-bold' : '' }}">
                            {{ \Carbon\Carbon::parse($booking->batas_ambil)->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColor = [
                                    'menunggu' => 'yellow',
                                    'disetujui' => 'green',
                                    'ditolak' => 'red',
                                    'diambil' => 'blue',
                                    'hangus' => 'gray',
                                ][$booking->status] ?? 'gray';
                            @endphp
                            <span class="inline-flex px-2 py-1 text-xs rounded-full bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('petugas.booking.show', $booking->id) }}" 
                               class="text-indigo-600 hover:text-indigo-800">Proses</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            Tidak ada data booking
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