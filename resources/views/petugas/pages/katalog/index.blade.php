@extends('petugas.layouts.app')

@section('title', 'Katalog Buku')

@section('content')
<div class="p-4 md:p-6">

    {{-- Header dengan Tombol Cetak --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">📚 Katalog Perpustakaan</h1>
            <p class="text-sm text-gray-500 mt-1">Jelajahi koleksi buku yang tersedia</p>
        </div>
        
        <div class="flex gap-2">
            {{-- Tombol Cetak Semua --}}
            <form action="{{ route('petugas.katalog.print') }}" method="GET" target="_blank">
                @foreach(request()->query() as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <button type="submit" 
                        class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Cetak Katalog
                </button>
            </form>
            
            {{-- Tombol Cetak yang Dipilih --}}
            <button id="btnPrintSelected" 
                    class="hidden items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Cetak Terpilih (<span id="selectedCount">0</span>)
            </button>
        </div>
    </div>

    {{-- Statistik Cepat --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
        <div class="bg-white p-4 rounded-xl shadow-sm border">
            <div class="text-indigo-600 text-2xl font-bold">{{ number_format($totalJudul) }}</div>
            <div class="text-xs text-gray-500">Total Judul</div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border">
            <div class="text-green-600 text-2xl font-bold">{{ number_format($totalEksemplar) }}</div>
            <div class="text-xs text-gray-500">Total Eksemplar</div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border">
            <div class="text-blue-600 text-2xl font-bold">{{ number_format($totalTersedia) }}</div>
            <div class="text-xs text-gray-500">Tersedia</div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border">
            <div class="text-purple-600 text-2xl font-bold">{{ number_format($kategoriList->count()) }}</div>
            <div class="text-xs text-gray-500">Kategori</div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white p-4 rounded-xl shadow-sm border mb-6">
        <form method="GET" action="{{ route('petugas.katalog.index') }}" class="space-y-4">
            <div class="flex flex-col md:flex-row gap-3">
                <div class="flex-1">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Cari judul, pengarang, penerbit, ISBN, atau nomor panggil..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-200">
                    </div>
                </div>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Cari
                </button>
            </div>

            <div class="flex flex-wrap gap-3">
                <select name="kategori" class="px-3 py-2 border rounded-lg">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoriList as $kategori)
                        <option value="{{ $kategori->id }}" {{ request('kategori') == $kategori->id ? 'selected' : '' }}>
                            {{ $kategori->nama }}
                        </option>
                    @endforeach
                </select>

                <select name="tipe" class="px-3 py-2 border rounded-lg">
                    <option value="">Semua Tipe</option>
                    <option value="fisik" {{ request('tipe') == 'fisik' ? 'selected' : '' }}>📖 Fisik</option>
                    <option value="digital" {{ request('tipe') == 'digital' ? 'selected' : '' }}>💻 Digital</option>
                </select>

                <select name="sort" class="px-3 py-2 border rounded-lg">
                    <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>📅 Terbaru</option>
                    <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>📅 Terlama</option>
                    <option value="judul-asc" {{ request('sort') == 'judul-asc' ? 'selected' : '' }}>🔤 Judul A-Z</option>
                    <option value="judul-desc" {{ request('sort') == 'judul-desc' ? 'selected' : '' }}>🔤 Judul Z-A</option>
                </select>

                @if(request()->anyFilled(['search', 'kategori', 'tipe', 'sort']))
                    <a href="{{ route('petugas.katalog.index') }}" class="px-4 py-2 bg-gray-100 rounded-lg flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Checkbox untuk pilih semua --}}
    <div class="mb-4 flex items-center gap-3">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" id="selectAll" class="w-4 h-4 text-indigo-600 rounded border-gray-300">
            <span class="text-sm text-gray-700">Pilih Semua</span>
        </label>
        <span class="text-xs text-gray-400">|</span>
        <span class="text-sm text-gray-500">{{ $buku->total() }} buku ditemukan</span>
    </div>

    {{-- Grid Buku --}}
    @if($buku->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($buku as $item)
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all">
                    {{-- Checkbox --}}
                    <div class="relative z-10 p-2">
                        <input type="checkbox" 
                               class="book-checkbox w-4 h-4 text-indigo-600 rounded border-gray-300"
                               value="{{ $item->id }}">
                    </div>
                    
                    {{-- Cover --}}
                    <div class="h-48 bg-gradient-to-br from-indigo-50 to-purple-50 overflow-hidden relative">
                        @if($item->sampul && Storage::disk('public')->exists($item->sampul))
                            <img src="{{ asset('storage/' . $item->sampul) }}" 
                                 alt="{{ $item->judul }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-20 h-20 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                        @endif
                        
                        <div class="absolute top-2 right-2">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full shadow-sm
                                {{ $item->tipe == 'fisik' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ $item->tipe == 'fisik' ? '📖 Fisik' : '💻 Digital' }}
                            </span>
                        </div>
                    </div>
                    
                    {{-- Info --}}
                    <div class="p-4">
                        <h3 class="font-bold text-gray-800 mb-1 line-clamp-2">{{ $item->judul }}</h3>
                        <p class="text-sm text-gray-600 mb-1">{{ $item->pengarang ?? '-' }}</p>
                        <p class="text-xs text-gray-500 mb-2">{{ $item->penerbit ?? '-' }} {{ $item->tahun_terbit ? '(' . $item->tahun_terbit . ')' : '' }}</p>
                        
                        <div class="flex items-center justify-between mt-3 pt-2 border-t">
                            <div class="text-xs">
                                <span class="text-gray-500">DDC:</span>
                                <span class="font-mono font-medium">{{ $item->no_ddc ?? '-' }}</span>
                            </div>
                            <div class="flex gap-2">
                                <button onclick="showDetail({{ $item->id }})" 
                                        class="text-indigo-600 hover:text-indigo-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                                <a href="/petugas/katalog/print-card/{{ $item->id }}" target="_blank"
                                   class="text-green-600 hover:text-green-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $buku->withQueryString()->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl p-12 text-center">
            <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada buku ditemukan</h3>
            <a href="{{ route('petugas.katalog.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg">
                Reset Filter
            </a>
        </div>
    @endif
</div>

{{-- Modal Detail --}}
<div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeModal()"></div>
        <div class="relative bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div id="modalContent" class="p-6"></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Fungsi escapeHtml yang AMAN untuk semua tipe data
function escapeHtml(value) {
    if (value === null || value === undefined) {
        return '';
    }
    if (typeof value === 'object') {
        return '';
    }
    const str = String(value);
    return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

// Fungsi safeString untuk data yang mungkin null
function safeString(value, defaultValue = '-') {
    if (value === null || value === undefined || value === '') {
        return defaultValue;
    }
    if (typeof value === 'object') {
        return defaultValue;
    }
    return escapeHtml(String(value));
}

// Select All functionality
const selectAll = document.getElementById('selectAll');
const checkboxes = document.querySelectorAll('.book-checkbox');
const btnPrintSelected = document.getElementById('btnPrintSelected');
const selectedCountSpan = document.getElementById('selectedCount');

function updateSelectedCount() {
    const checked = document.querySelectorAll('.book-checkbox:checked').length;
    if (selectedCountSpan) selectedCountSpan.textContent = checked;
    if (btnPrintSelected) {
        if (checked > 0) {
            btnPrintSelected.classList.remove('hidden');
            btnPrintSelected.classList.add('flex');
        } else {
            btnPrintSelected.classList.add('hidden');
            btnPrintSelected.classList.remove('flex');
        }
    }
}

if (selectAll) {
    selectAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateSelectedCount();
    });
}

checkboxes.forEach(cb => {
    cb.addEventListener('change', updateSelectedCount);
});

// Print selected books
if (btnPrintSelected) {
    btnPrintSelected.addEventListener('click', function() {
        const selected = document.querySelectorAll('.book-checkbox:checked');
        const ids = Array.from(selected).map(cb => cb.value).join(',');
        if (ids) {
            window.open('/petugas/katalog/print-multiple?ids=' + ids, '_blank');
        }
    });
}

function showDetail(id) {
    const modal = document.getElementById('detailModal');
    const modalContent = document.getElementById('modalContent');
    if (modal) modal.classList.remove('hidden');
    
    fetch(`/petugas/buku/${id}`)
        .then(res => res.json())
        .then(data => {
            if (modalContent) {
                // Get kategori name safely
                let kategoriNama = '-';
                if (data.kategori && data.kategori.nama) {
                    kategoriNama = safeString(data.kategori.nama);
                }
                
                modalContent.innerHTML = `
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-xl font-bold">${safeString(data.judul)}</h3>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="col-span-1">
                            <div class="bg-gray-100 rounded-lg aspect-[2/3] overflow-hidden">
                                ${data.sampul ? `<img src="/storage/${data.sampul}" class="w-full h-full object-cover">` : 
                                  `<div class="w-full h-full flex items-center justify-center text-gray-400">Tidak ada cover</div>`}
                            </div>
                        </div>
                        <div class="col-span-2 space-y-2">
                            <p><strong>Pengarang:</strong> ${safeString(data.pengarang)}</p>
                            <p><strong>Penerbit:</strong> ${safeString(data.penerbit)} (${safeString(data.tahun_terbit)})</p>
                            <p><strong>ISBN:</strong> ${safeString(data.isbn)}</p>
                            <p><strong>DDC:</strong> ${safeString(data.no_ddc)}</p>
                            <p><strong>Nomor Panggil:</strong> ${safeString(data.nomor_panggil)}</p>
                            <p><strong>Halaman:</strong> ${safeString(data.jumlah_halaman)} hlm</p>
                            <p><strong>Ukuran:</strong> ${safeString(data.ukuran)} cm</p>
                            <p><strong>Bahasa:</strong> ${safeString(data.bahasa, 'Indonesia')}</p>
                            <p><strong>Kategori:</strong> ${kategoriNama}</p>
                            <p><strong>Lokasi Rak:</strong> ${safeString(data.rak)}</p>
                            ${data.deskripsi ? `<p><strong>Sinopsis:</strong><br>${safeString(data.deskripsi)}</p>` : ''}
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end gap-2 pt-4 border-t">
                        <a href="/petugas/buku/${data.id}/edit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Edit</a>
                        <a href="/petugas/katalog/print-card/${data.id}" target="_blank" class="px-4 py-2 bg-green-600 text-white rounded-lg">Cetak Kartu</a>
                        <button onclick="closeModal()" class="px-4 py-2 bg-gray-100 rounded-lg">Tutup</button>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (modalContent) {
                modalContent.innerHTML = `
                    <div class="text-center py-8">
                        <p class="text-red-600">Gagal memuat detail buku</p>
                        <button onclick="closeModal()" class="mt-3 px-4 py-2 bg-gray-100 rounded-lg">Tutup</button>
                    </div>
                `;
            }
        });
}

function closeModal() {
    const modal = document.getElementById('detailModal');
    if (modal) modal.classList.add('hidden');
}
</script>
@endpush

@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endpush
@endsection