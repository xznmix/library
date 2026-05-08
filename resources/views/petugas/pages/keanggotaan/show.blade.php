@extends('petugas.layouts.app')

@section('title', 'Detail Anggota')

@section('content')
<div class="p-4 md:p-6 max-w-5xl mx-auto">

    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('petugas.keanggotaan.index') }}" 
               class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Detail Anggota</h1>
                <p class="text-sm text-gray-500 mt-1">Informasi lengkap profil anggota</p>
            </div>
        </div>
    </div>

    {{-- Status Banner --}}
    @php
        $bannerStyles = [
            'pending' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
            'active' => 'bg-green-50 border-green-200 text-green-800',
            'inactive' => 'bg-red-50 border-red-200 text-red-800',
            'rejected' => 'bg-gray-50 border-gray-200 text-gray-800'
        ];
        $bannerIcons = [
            'pending' => '⏳',
            'active' => '✅',
            'inactive' => '❌',
            'rejected' => '⛔'
        ];
        $bannerStyle = $bannerStyles[$calonAnggota->status_anggota] ?? 'bg-gray-50';
        $bannerIcon = $bannerIcons[$calonAnggota->status_anggota] ?? '❓';
    @endphp

    <div class="{{ $bannerStyle }} border rounded-xl p-4 mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="text-2xl">{{ $bannerIcon }}</span>
            <div>
                <p class="font-medium">
                    Status: {{ ucfirst($calonAnggota->status_anggota ?? 'unknown') }}
                </p>
                @if($calonAnggota->status_anggota == 'pending')
                    <p class="text-sm">Menunggu verifikasi oleh petugas</p>
                @elseif($calonAnggota->status_anggota == 'active')
                    <p class="text-sm">Anggota aktif hingga {{ \Carbon\Carbon::parse($calonAnggota->masa_berlaku)->format('d F Y') }}</p>
                @elseif($calonAnggota->status_anggota == 'rejected' && $calonAnggota->catatan_penolakan)
                    <p class="text-sm">Alasan: {{ $calonAnggota->catatan_penolakan }}</p>
                @endif
            </div>
        </div>
        
        {{-- Action Buttons --}}
        <div class="flex gap-2">
            @if($calonAnggota->status_anggota == 'pending')
                <button onclick="openApproveModal({{ $calonAnggota->id }}, '{{ $calonAnggota->name }}')"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Setujui
                </button>
                
                <button onclick="openRejectModal({{ $calonAnggota->id }}, '{{ $calonAnggota->name }}')"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Tolak
                </button>
            @endif
            
            <a href="{{ route('petugas.keanggotaan.edit', $calonAnggota->id) }}" 
               class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Data
            </a>
        </div>
    </div>

    {{-- Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Left Column - Foto & Identitas --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Foto Profile --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
                <div class="w-32 h-32 mx-auto rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center overflow-hidden border-4 border-white shadow-lg">
                    @if($calonAnggota->foto_ktp)
                        <img src="{{ asset('storage/'.$calonAnggota->foto_ktp) }}" 
                             alt="{{ $calonAnggota->name }}"
                             class="w-full h-full object-cover">
                    @else
                        <span class="text-4xl font-bold text-indigo-600">
                            {{ strtoupper(substr($calonAnggota->name, 0, 1)) }}
                        </span>
                    @endif
                </div>
                <h2 class="text-xl font-bold text-gray-800 mt-4">{{ $calonAnggota->name }}</h2>
                <p class="text-sm text-gray-500">{{ $calonAnggota->email }}</p>
                
                @if($calonAnggota->no_anggota)
                    <div class="mt-3 inline-block bg-indigo-50 px-4 py-2 rounded-lg">
                        <span class="text-xs text-gray-500">No. Anggota</span>
                        <p class="font-mono font-bold text-indigo-600">{{ $calonAnggota->no_anggota }}</p>
                    </div>
                @endif
            </div>
            
            {{-- Dokumen --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Dokumen Identitas
                </h3>
                
                {{-- Foto KTP --}}
                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-2">Foto KTP/Kartu Pelajar</p>
                    <div class="border rounded-lg p-2 bg-gray-50">
                        @if($calonAnggota->foto_ktp)
                            <img src="{{ asset('storage/'.$calonAnggota->foto_ktp) }}" 
                                 alt="KTP" 
                                 class="w-full rounded-lg cursor-pointer hover:opacity-90 transition-opacity"
                                 onclick="window.open(this.src)">
                        @else
                            <div class="h-32 flex items-center justify-center text-gray-400">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                </div>
                
                {{-- Kartu Anggota (jika sudah aktif) --}}
                @if($calonAnggota->status_anggota == 'active')
                <div>
                    <p class="text-sm text-gray-600 mb-2">Kartu Anggota</p>
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg p-4 text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs opacity-80">PERPUSTAKAAN SMAN 1 TAMBANG</p>
                                <p class="text-sm font-bold mt-2">{{ $calonAnggota->name }}</p>
                                <p class="text-xs mt-1">{{ $calonAnggota->no_anggota }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs opacity-80">Berlaku s/d</p>
                                <p class="text-sm font-bold">{{ \Carbon\Carbon::parse($calonAnggota->masa_berlaku)->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Right Column - Detail Informasi --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Informasi Pribadi --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Informasi Pribadi
                </h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500">Nama Lengkap</p>
                        <p class="font-medium">{{ $calonAnggota->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">NISN/NIK</p>
                        <p class="font-mono">{{ $calonAnggota->nisn_nik }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Jenis</p>
                        <p class="font-medium capitalize">{{ $calonAnggota->jenis ?? 'Umum' }}</p>
                    </div>
                    @if($calonAnggota->kelas)
                    <div>
                        <p class="text-xs text-gray-500">Kelas</p>
                        <p>{{ $calonAnggota->kelas }} {{ $calonAnggota->jurusan }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Kontak --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Kontak & Alamat
                </h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500">Email</p>
                        <p>{{ $calonAnggota->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">No. Telepon</p>
                        <p>{{ $calonAnggota->phone ?? '-' }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs text-gray-500">Alamat</p>
                        <p>{{ $calonAnggota->address ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Statistik Keanggotaan --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Statistik Peminjaman
                </h3>
                
                <div class="grid grid-cols-3 gap-3">
                    <div class="text-center p-3 bg-indigo-50 rounded-lg">
                        <p class="text-2xl font-bold text-indigo-600">{{ $calonAnggota->peminjaman->count() }}</p>
                        <p class="text-xs text-gray-600">Total Pinjam</p>
                    </div>
                    <div class="text-center p-3 bg-green-50 rounded-lg">
                        <p class="text-2xl font-bold text-green-600">
                            {{ $calonAnggota->peminjaman->where('status_pinjam', 'dikembalikan')->count() }}
                        </p>
                        <p class="text-xs text-gray-600">Dikembalikan</p>
                    </div>
                    <div class="text-center p-3 bg-yellow-50 rounded-lg">
                        <p class="text-2xl font-bold text-yellow-600">
                            {{ $calonAnggota->peminjaman->where('status_pinjam', 'dipinjam')->count() }}
                        </p>
                        <p class="text-xs text-gray-600">Sedang Dipinjam</p>
                    </div>
                </div>
            </div>

            {{-- Riwayat Aktivitas --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Riwayat Aktivitas
                </h3>
                
                                <div class="space-y-3">
                    @forelse($calonAnggota->peminjaman->sortByDesc('created_at')->take(5) as $pinjam)
                    <div class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-lg">
                        <div class="w-2 h-2 rounded-full 
                            @if($pinjam->status_pinjam == 'dipinjam') bg-yellow-500
                            @elseif($pinjam->status_pinjam == 'dikembalikan') bg-green-500
                            @else bg-red-500 @endif">
                        </div>
                        <div class="flex-1">
                            <p class="text-sm">
                                <span class="font-medium">{{ $pinjam->buku->judul ?? 'Buku' }}</span>
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ $pinjam->created_at->format('d M Y') }} • 
                                {{ $pinjam->status_pinjam == 'dipinjam' ? 'Belum dikembalikan' : 'Dikembalikan' }}
                            </p>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500 text-center py-4">Belum ada riwayat peminjaman</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Include Modals from index --}}
@include('petugas.pages.keanggotaan.partials.modals')
@endsection

@push('scripts')
<script>
function openApproveModal(id, name) {
    document.getElementById('approveModal').classList.remove('hidden');
    document.getElementById('approveName').innerText = 'Setujui ' + name + ' sebagai anggota?';
    document.getElementById('approveForm').action = '/petugas/keanggotaan/' + id + '/approve';
}

function openRejectModal(id, name) {
    document.getElementById('rejectModal').classList.remove('hidden');
    document.getElementById('rejectName').innerText = 'Tolak pendaftaran ' + name + '?';
    document.getElementById('rejectForm').action = '/petugas/keanggotaan/' + id + '/reject';
}

function closeModals() {
    document.getElementById('approveModal')?.classList.add('hidden');
    document.getElementById('rejectModal')?.classList.add('hidden');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModals();
});
</script>
@endpush