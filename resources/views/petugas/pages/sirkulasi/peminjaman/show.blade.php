@extends('petugas.layouts.app')

@section('title', 'Detail Peminjaman')

@section('content')
<div class="p-4 md:p-6 max-w-5xl mx-auto">

    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('petugas.sirkulasi.peminjaman.index') }}" 
               class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Detail Peminjaman</h1>
                <p class="text-sm text-gray-500 mt-1">Informasi lengkap peminjaman buku</p>
            </div>
        </div>
    </div>

    {{-- Status Banner --}}
    @php
        $today = now();
        $jatuhTempo = \Carbon\Carbon::parse($peminjaman->tgl_jatuh_tempo);
        $terlambat = $today > $jatuhTempo && $peminjaman->status_pinjam != 'dikembalikan';
        $statusClass = $terlambat ? 'bg-red-50 border-red-200 text-red-800' : 'bg-green-50 border-green-200 text-green-800';
        $statusIcon = $terlambat ? '⚠️' : '✅';
        $statusText = $terlambat ? 'Terlambat' : 'Dipinjam';
        
        if ($peminjaman->status_pinjam == 'dikembalikan') {
            $statusClass = 'bg-blue-50 border-blue-200 text-blue-800';
            $statusIcon = '📚';
            $statusText = 'Dikembalikan';
        } elseif ($peminjaman->status_pinjam == 'diperpanjang') {
            $statusClass = 'bg-purple-50 border-purple-200 text-purple-800';
            $statusIcon = '🔄';
            $statusText = 'Diperpanjang';
        }
    @endphp

    <div class="{{ $statusClass }} border rounded-xl p-4 mb-6">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-3">
                <span class="text-2xl">{{ $statusIcon }}</span>
                <div>
                    <p class="font-medium">Status: {{ $statusText }}</p>
                    @if($terlambat)
                        <p class="text-sm">Terlambat {{ floor($today->diffInDays($jatuhTempo)) }} hari</p>
                    @elseif($peminjaman->status_pinjam == 'dikembalikan')
                        <p class="text-sm">Dikembalikan pada {{ \Carbon\Carbon::parse($peminjaman->tanggal_pengembalian)->format('d/m/Y H:i') }}</p>
                    @elseif($peminjaman->status_pinjam == 'diperpanjang')
                        <p class="text-sm">Peminjaman ini telah diperpanjang</p>
                    @else
                        <p class="text-sm">Sisa waktu: {{ floor($today->diffInDays($jatuhTempo)) }} hari</p>
                    @endif
                </div>
            </div>
            
            <div class="flex gap-2">
                @if($peminjaman->status_pinjam != 'dikembalikan' && $peminjaman->status_pinjam != 'diperpanjang')
                    <button onclick="prosesPengembalian({{ $peminjaman->id }})"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h14a2 2 0 002-2z"/>
                        </svg>
                        Proses Pengembalian
                    </button>
                    
                    {{-- TOMBOL PERPANJANG dengan SweetAlert2 --}}
                    @if($peminjaman->status_pinjam == 'dipinjam' && !$peminjaman->is_perpanjangan && $peminjaman->denda_total == 0)
                        @php
                            $bisaPerpanjang = now() <= $jatuhTempo;
                        @endphp
                        @if($bisaPerpanjang)
                            <button type="button" 
                                    onclick="confirmPerpanjang({{ $peminjaman->id }}, '{{ addslashes($peminjaman->buku->judul) }}', '{{ addslashes($peminjaman->user->name) }}')"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 flex items-center gap-2 shadow-sm hover:shadow-md">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Perpanjang
                            </button>
                        @endif
                    @endif
                    
                    @if($peminjaman->is_perpanjangan)
                        <div class="px-4 py-2 bg-gray-200 text-gray-600 rounded-lg flex items-center gap-2 cursor-default">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Sudah Diperpanjang
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    {{-- Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Left Column - Info Anggota --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Foto & Nama --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
                <div class="w-24 h-24 mx-auto rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center overflow-hidden border-4 border-white shadow-lg">
                    @if($peminjaman->user->foto_ktp)
                        <img src="{{ asset('storage/'.$peminjaman->user->foto_ktp) }}" 
                             alt="{{ $peminjaman->user->name }}"
                             class="w-full h-full object-cover">
                    @else
                        <span class="text-3xl font-bold text-indigo-600">
                            {{ strtoupper(substr($peminjaman->user->name, 0, 1)) }}
                        </span>
                    @endif
                </div>
                <h2 class="text-xl font-bold text-gray-800 mt-4">{{ $peminjaman->user->name }}</h2>
                <p class="text-sm text-gray-500">{{ $peminjaman->user->email }}</p>
                
                @if($peminjaman->user->no_anggota)
                    <div class="mt-3 inline-block bg-indigo-50 px-4 py-2 rounded-lg">
                        <span class="text-xs text-gray-500">No. Anggota</span>
                        <p class="font-mono font-bold text-indigo-600">{{ $peminjaman->user->no_anggota }}</p>
                    </div>
                @endif
            </div>

            {{-- Detail Kontak --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Kontak
                </h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">NISN/NIK:</span>
                        <span class="font-medium">{{ $peminjaman->user->nisn_nik ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">No. HP:</span>
                        <span class="font-medium">{{ $peminjaman->user->phone ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Jenis:</span>
                        <span class="font-medium capitalize">{{ $peminjaman->user->jenis ?? 'Umum' }}</span>
                    </div>
                    @if($peminjaman->user->kelas)
                    <div class="flex justify-between">
                        <span class="text-gray-500">Kelas:</span>
                        <span class="font-medium">{{ $peminjaman->user->kelas }} {{ $peminjaman->user->jurusan }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Column - Detail Peminjaman --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Informasi Buku --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Informasi Buku
                </h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2 flex gap-4">
                        <div class="w-20 h-28 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                            <img src="{{ $peminjaman->buku->sampul_url ?? asset('img/default-book.jpg') }}" 
                                 alt="Cover" 
                                 class="w-full h-full object-cover">
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Judul</p>
                            <p class="font-bold text-gray-800 text-lg">{{ $peminjaman->buku->judul }}</p>
                            
                            <p class="text-sm text-gray-500 mt-2">Pengarang</p>
                            <p class="font-medium">{{ $peminjaman->buku->pengarang ?? '-' }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">ISBN</p>
                        <p class="font-medium">{{ $peminjaman->buku->isbn ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Penerbit</p>
                        <p class="font-medium">{{ $peminjaman->buku->penerbit ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Kode Eksemplar</p>
                        <p class="font-mono font-bold text-indigo-600">{{ $peminjaman->kode_eksemplar }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Denda/hari</p>
                        <p class="font-medium">Rp {{ number_format($peminjaman->buku->denda_per_hari ?? 1000, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            {{-- Timeline Peminjaman --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Timeline Peminjaman
                </h3>
                
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium">Dipinjam</p>
                            <p class="text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d F Y H:i') }}
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 {{ $terlambat ? 'bg-red-100' : 'bg-yellow-100' }} rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 {{ $terlambat ? 'text-red-600' : 'text-yellow-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium">Jatuh Tempo</p>
                            <p class="text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($peminjaman->tgl_jatuh_tempo)->format('d F Y') }}
                            </p>
                            @if($terlambat)
                                <p class="text-xs text-red-600 mt-1">
                                    Terlambat {{ floor($today->diffInDays($jatuhTempo)) }} hari
                                </p>
                            @endif
                        </div>
                    </div>
                    
                    @if($peminjaman->tanggal_pengembalian)
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h14a2 2 0 002-2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium">Dikembalikan</p>
                            <p class="text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($peminjaman->tanggal_pengembalian)->format('d F Y H:i') }}
                            </p>
                            @if($peminjaman->denda > 0)
                                <p class="text-xs text-red-600 mt-1">
                                    Denda: Rp {{ number_format($peminjaman->denda, 0, ',', '.') }}
                                </p>
                            @endif
                        </div>
                    </div>
                    @endif
                    
                    @if($peminjaman->status_pinjam == 'diperpanjang' && $peminjaman->perpanjangan)
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium">Perpanjangan</p>
                            <p class="text-sm text-gray-500">
                                Peminjaman diperpanjang dengan ID: <a href="{{ route('petugas.sirkulasi.peminjaman.show', $peminjaman->perpanjangan->id) }}" class="text-indigo-600">#{{ $peminjaman->perpanjangan->id }}</a>
                            </p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Riwayat Aktivitas --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Riwayat Aktivitas
                </h3>
                
                <div class="space-y-3">
                    @forelse($peminjaman->logs ?? [] as $log)
                    <div class="flex items-start gap-3 p-2 hover:bg-gray-50 rounded-lg">
                        <div class="w-2 h-2 mt-2 rounded-full bg-indigo-500"></div>
                        <div class="flex-1">
                            <p class="text-sm">{{ $log->aktivitas }}</p>
                            <p class="text-xs text-gray-500">{{ $log->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500 text-center py-4">Belum ada riwayat aktivitas</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Pengembalian --}}
@include('petugas.pages.sirkulasi.partials.modal-denda')

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmPerpanjang(id, judulBuku, namaAnggota) {
    // Tampilkan konfirmasi dengan SweetAlert2
    Swal.fire({
        title: '⚠️ Perpanjang Peminjaman?',
        html: `
            <div style="text-align: left;">
                <p style="margin-bottom: 8px;"><strong>📚 Buku:</strong> ${judulBuku}</p>
                <p style="margin-bottom: 8px;"><strong>👤 Anggota:</strong> ${namaAnggota}</p>
                <p style="margin-bottom: 8px;"><strong>📅 Jatuh Tempo Baru:</strong> +7 hari dari hari ini</p>
                <hr style="margin: 12px 0;">
                <p style="color: #f59e0b; font-size: 13px;">⚠️ Perpanjangan hanya bisa dilakukan 1x per peminjaman</p>
                <p style="color: #10b981; font-size: 13px;">✅ Tidak ada denda yang dikenakan</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '✅ Ya, Perpanjang',
        cancelButtonText: '❌ Batal',
        backdrop: true,
        allowOutsideClick: false,
        allowEscapeKey: true
    }).then((result) => {
        if (result.isConfirmed) {
            prosesPerpanjang(id);
        }
    });
}

function prosesPerpanjang(id) {
    // Tampilkan loading
    Swal.fire({
        title: '⏳ Memproses...',
        text: 'Sedang memperpanjang peminjaman',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Kirim request AJAX
    fetch(`/petugas/sirkulasi/peminjaman/${id}/perpanjang`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Sukses
            Swal.fire({
                title: '✅ Berhasil!',
                html: `
                    <p>Peminjaman berhasil diperpanjang!</p>
                    <p class="text-sm text-gray-500 mt-2">Jatuh tempo baru: <strong>${data.tanggal_baru}</strong></p>
                `,
                icon: 'success',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK',
                timer: 3000,
                timerProgressBar: true
            }).then(() => {
                // Redirect ke halaman detail peminjaman baru
                window.location.href = `/petugas/sirkulasi/peminjaman/${data.id_baru}`;
            });
        } else {
            // Error dari server
            Swal.fire({
                title: '❌ Gagal!',
                text: data.message || 'Terjadi kesalahan. Silakan coba lagi.',
                icon: 'error',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: '❌ Error!',
            text: 'Gagal terhubung ke server. Periksa koneksi Anda.',
            icon: 'error',
            confirmButtonColor: '#d33',
            confirmButtonText: 'OK'
        });
    });
}
</script>

<script>
function prosesPengembalian(id) {
    fetch(`/petugas/sirkulasi/peminjaman/${id}/json`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }
            
            if (typeof tampilkanModalPengembalian === 'function') {
                tampilkanModalPengembalian(data);
            } else {
                console.error('Fungsi tampilkanModalPengembalian tidak ditemukan');
                alert('Modal pengembalian tidak tersedia. Pastikan file modal-denda.blade.php sudah di-include dengan benar.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal mengambil data peminjaman: ' + error.message);
        });
}
</script>
@endpush