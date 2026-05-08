@extends('pimpinan.layouts.app')

@section('title', 'Laporan Peminjaman')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                </svg>
                Laporan Peminjaman
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Rekap peminjaman buku perpustakaan
            </p>
        </div>
        
        <div class="flex gap-2 mt-4 md:mt-0">
            <button onclick="window.print()" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print
            </button>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Periode</label>
                <select name="periode" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700">
                    <option value="hari_ini" {{ ($periode ?? 'bulan_ini') == 'hari_ini' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="minggu_ini" {{ ($periode ?? 'bulan_ini') == 'minggu_ini' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="bulan_ini" {{ ($periode ?? 'bulan_ini') == 'bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="tahun_ini" {{ ($periode ?? 'bulan_ini') == 'tahun_ini' ? 'selected' : '' }}>Tahun Ini</option>
                    <option value="custom" {{ ($periode ?? 'bulan_ini') == 'custom' ? 'selected' : '' }}>Kustom</option>
                </select>
            </div>
            
            <div id="startDateField" class="{{ ($periode ?? 'bulan_ini') == 'custom' ? '' : 'hidden' }}">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d') ?? now()->startOfMonth()->format('Y-m-d')) }}" 
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700">
            </div>
            
            <div id="endDateField" class="{{ ($periode ?? 'bulan_ini') == 'custom' ? '' : 'hidden' }}">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d') ?? now()->format('Y-m-d')) }}" 
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700">
            </div>
            
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">
                    Tampilkan
                </button>
                <a href="{{ route('pimpinan.laporan.peminjaman') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <script>
        document.querySelector('select[name="periode"]').addEventListener('change', function() {
            const startField = document.getElementById('startDateField');
            const endField = document.getElementById('endDateField');
            
            if (this.value === 'custom') {
                startField.classList.remove('hidden');
                endField.classList.remove('hidden');
            } else {
                startField.classList.add('hidden');
                endField.classList.add('hidden');
            }
        });
    </script>

    {{-- Statistik Ringkas --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-5 text-white shadow-lg">
            <p class="text-blue-100 text-sm">📚 Total Peminjaman</p>
            <p class="text-3xl font-bold">{{ number_format($totalPeminjaman) }}</p>
            <p class="text-xs text-blue-100 mt-2">Dalam periode ini</p>
        </div>
        
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-5 text-white shadow-lg">
            <p class="text-green-100 text-sm">✅ Tepat Waktu</p>
            <p class="text-3xl font-bold">{{ number_format($tepatWaktu) }}</p>
            <p class="text-xs text-green-100 mt-2">{{ $totalPeminjaman > 0 ? round(($tepatWaktu / $totalPeminjaman) * 100, 1) : 0 }}% dari total</p>
        </div>
        
        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-5 text-white shadow-lg">
            <p class="text-yellow-100 text-sm">⚠️ Terlambat</p>
            <p class="text-3xl font-bold">{{ number_format($terlambat) }}</p>
            <p class="text-xs text-yellow-100 mt-2">{{ $totalPeminjaman > 0 ? round(($terlambat / $totalPeminjaman) * 100, 1) : 0 }}% dari total</p>
        </div>
        
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-5 text-white shadow-lg">
            <p class="text-purple-100 text-sm">📊 Rata-rata per Hari</p>
            <p class="text-3xl font-bold">{{ $rataPerHari }}</p>
            <p class="text-xs text-purple-100 mt-2">Periode ini</p>
        </div>
    </div>

    {{-- Tabel Detail Peminjaman --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="font-semibold text-gray-800 dark:text-white">📋 Detail Peminjaman</h3>
            <p class="text-xs text-gray-500 mt-1">Menampilkan {{ count($peminjaman) }} data terbaru dalam periode ini</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left">No</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Anggota</th>
                        <th class="px-4 py-3 text-left">Buku</th>
                        <th class="px-4 py-3 text-left">Petugas</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-right">Denda</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($peminjaman as $index => $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3">{{ $index + 1 }}</td>
                        <td class="px-4 py-3">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 font-medium">{{ $item->user->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $item->buku->judul ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $item->petugas->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($item->status_pinjam == 'dipinjam')
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">Dipinjam</span>
                            @elseif($item->status_pinjam == 'terlambat')
                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs">Terlambat</span>
                            @else
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">Kembali</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format($item->denda_total, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            Tidak ada data peminjaman dalam periode ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-700/50 font-semibold">
                    <tr>
                        <td colspan="6" class="px-4 py-3 text-right">Total Denda:</td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format($peminjaman->sum('denda_total'), 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 text-center text-sm text-gray-500">
            <p>Total {{ number_format($totalPeminjaman) }} peminjaman dalam periode ini</p>
        </div>
    </div>

    {{-- Ringkasan Kategori Buku (Ganti Grafik) --}}
    @if(!empty($kategoriLabels) && count($kategoriLabels) > 0)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
        <h3 class="font-semibold text-gray-800 dark:text-white mb-4">📖 Distribusi Kategori Buku</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($kategoriLabels as $index => $label)
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-600">{{ $label }}</span>
                    <span class="font-medium">{{ $kategoriValues[$index] ?? 0 }} peminjaman</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    @php $persen = max($kategoriValues) > 0 ? (($kategoriValues[$index] ?? 0) / max($kategoriValues)) * 100 : 0; @endphp
                    <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ $persen }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Rekomendasi Strategis --}}
    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-xl p-6 border border-indigo-100 dark:border-indigo-800">
        <h3 class="font-semibold text-indigo-900 dark:text-indigo-300 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Rekomendasi Strategis
        </h3>
        <ul class="space-y-2 text-sm text-indigo-800 dark:text-indigo-200">
            @if(!empty($kategoriLabels) && count($kategoriLabels) > 0)
            <li class="flex items-start gap-2">
                <span class="text-indigo-500">•</span>
                <span>Kategori "{{ $kategoriLabels[0] ?? 'Favorit' }}" paling banyak dipinjam ({{ $kategoriValues[0] ?? 0 }} kali), pertimbangkan penambahan koleksi</span>
            </li>
            @endif
            @if($terlambat > 0)
            <li class="flex items-start gap-2">
                <span class="text-indigo-500">•</span>
                <span>Tingkat keterlambatan {{ $totalPeminjaman > 0 ? round(($terlambat / $totalPeminjaman) * 100, 1) : 0 }}% ({{ number_format($terlambat) }} dari {{ number_format($totalPeminjaman) }} peminjaman), perlu sosialisasi ke anggota</span>
            </li>
            @endif
            @if($tepatWaktu > 0)
            <li class="flex items-start gap-2">
                <span class="text-indigo-500">•</span>
                <span>Ketepatan waktu {{ $totalPeminjaman > 0 ? round(($tepatWaktu / $totalPeminjaman) * 100, 1) : 0 }}%, pertahankan dan tingkatkan</span>
            </li>
            @endif
        </ul>
    </div>

</div>
@endsection

@push('scripts')
@endpush