@extends('petugas.layouts.app')

@section('title','Pengembalian Buku')

@section('content')
<div class="p-4 md:p-6">

<div class="flex justify-between items-center mb-6 flex-wrap gap-3">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-slate-800">
            Pengembalian Buku
        </h1>
        <p class="text-gray-500 text-sm">
            Proses pengembalian dan pembayaran denda
        </p>
    </div>

    <a href="{{ route('petugas.sirkulasi.denda.index') }}"
       class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition">
        Daftar Denda
    </a>
</div>

{{-- Statistik --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-6">
    <div class="bg-white shadow rounded-xl p-3 md:p-4">
        <h3 class="text-xs md:text-sm text-gray-500">Total Aktif</h3>
        <p class="text-xl md:text-2xl font-bold text-indigo-600">{{ $statistik['total'] ?? 0 }}</p>
    </div>
    <div class="bg-white shadow rounded-xl p-3 md:p-4">
        <h3 class="text-xs md:text-sm text-gray-500">Tepat Waktu</h3>
        <p class="text-xl md:text-2xl font-bold text-green-600">{{ $statistik['tepat_waktu'] ?? 0 }}</p>
    </div>
    <div class="bg-white shadow rounded-xl p-3 md:p-4">
        <h3 class="text-xs md:text-sm text-gray-500">Terlambat</h3>
        <p class="text-xl md:text-2xl font-bold text-red-600">{{ $statistik['terlambat'] ?? 0 }}</p>
    </div>
    <div class="bg-white shadow rounded-xl p-3 md:p-4">
        <h3 class="text-xs md:text-sm text-gray-500">Jatuh Tempo Hari Ini</h3>
        <p class="text-xl md:text-2xl font-bold text-yellow-600">{{ $statistik['jatuh_tempo_hari_ini'] ?? 0 }}</p>
    </div>
</div>

{{-- Scan Barcode --}}
<div class="bg-white rounded-xl shadow p-4 md:p-5 mb-6">
    <label class="font-semibold mb-2 block text-gray-700">Scan Kode Eksemplar</label>
    <div class="flex gap-2">
        <input id="kodeEksemplar" type="text" 
            class="flex-1 border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            placeholder="Scan barcode / masukkan kode eksemplar..." autocomplete="off">
        <button id="cariBtn" class="bg-green-600 hover:bg-green-700 text-white px-6 rounded-lg transition">
            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            Cari
        </button>
    </div>
</div>

{{-- Tabel Peminjaman Aktif --}}
<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="bg-indigo-600 text-white p-4 font-semibold">Daftar Peminjaman Aktif</div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-100">
                <tr>
                    <th class="p-3 text-left">No</th>
                    <th class="p-3 text-left">Kode</th>
                    <th class="p-3 text-left">Anggota</th>
                    <th class="p-3 text-left">Buku</th>
                    <th class="p-3 text-left">Jatuh Tempo</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($peminjamanAktif as $index => $item)
                @php $telat = now()->gt($item->tgl_jatuh_tempo); @endphp
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3">{{ $peminjamanAktif->firstItem() + $index }}</td>
                    <td class="p-3"><span class="bg-gray-100 px-2 py-1 rounded text-xs font-mono">{{ $item->kode_eksemplar }}</span></td>
                    <td class="p-3">{{ $item->user->name ?? '-' }}</td>
                    <td class="p-3">{{ $item->buku->judul ?? '-' }}</td>
                    <td class="p-3">{{ \Carbon\Carbon::parse($item->tgl_jatuh_tempo)->format('d/m/Y') }}</td>
                    <td class="p-3">
                        @if($telat)
                            <span class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs">Terlambat</span>
                        @else
                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs">Dipinjam</span>
                        @endif
                    </td>
                    <td class="p-3 text-center">
                        <button onclick="prosesPengembalian({{ $item->id }})"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm transition">
                            Proses
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center p-10 text-gray-500">Tidak ada data peminjaman aktif</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($peminjamanAktif->hasPages())
    <div class="p-4 border-t">{{ $peminjamanAktif->links() }}</div>
    @endif
</div>

</div>

{{-- MODAL PENGEMBALIAN --}}
<div id="pengembalianModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div class="relative h-full overflow-y-auto">
        <div class="min-h-screen flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl my-8">
                <div id="modalContent" class="p-6">
                    <div class="text-center py-10">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
                        <p class="mt-3 text-gray-500">Memuat data...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const modal = document.getElementById('pengembalianModal');

function tutupModal() {
    modal.classList.add('hidden');
    document.body.style.overflow = '';
}

function prosesPengembalian(id) {
    console.log('Processing return for ID:', id);
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    fetch(`/petugas/sirkulasi/peminjaman/${id}/json`)
        .then(r => {
            console.log('Response status:', r.status);
            return r.json();
        })
        .then(data => {
            console.log('Data received:', data);
            if (data.error) {
                alert(data.message || 'Error loading data');
                tutupModal();
                return;
            }
            renderForm(data);
        })
        .catch(err => {
            console.error('Fetch error:', err);
            alert('Gagal memuat data: ' + err.message);
            tutupModal();
        });
}

function renderForm(data) {
    console.log('Rendering form with data:', data);
    
    if (!data || !data.buku || !data.user) {
        document.getElementById('modalContent').innerHTML = `
            <div class="text-center py-10 text-red-600">
                <p>Error: Data peminjaman tidak lengkap</p>
                <button onclick="tutupModal()" class="mt-4 px-4 py-2 bg-gray-600 text-white rounded-lg">Tutup</button>
            </div>
        `;
        return;
    }
    
    let dendaTerlambat = data.denda_terlambat || 0;
    let hargaBuku = data.buku.harga || 50000;
    
    const today = new Date().toISOString().slice(0,10);
    
    document.getElementById('modalContent').innerHTML = `
        <div class="flex justify-between items-center pb-4 border-b">
            <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Form Pengembalian Buku
            </h3>
            <button onclick="tutupModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>
        
        <form id="formPengembalian" method="POST" action="{{ route('petugas.sirkulasi.pengembalian.proses') }}" class="mt-4">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="peminjaman_id" value="${data.id}">
            <input type="hidden" name="denda_terlambat" id="dendaTerlambatInput" value="${dendaTerlambat}">
            <input type="hidden" name="denda_rusak" id="dendaRusakInput" value="0">
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Kolom Kiri - Info -->
                <div class="space-y-4">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-lg border border-blue-200">
                        <h4 class="font-semibold text-gray-800 mb-3">📋 Informasi Peminjaman</h4>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-gray-500">📚 Judul Buku</span>
                                <p class="font-semibold text-gray-800">${escapeHtml(data.buku.judul)}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">👤 Peminjam</span>
                                <p class="font-semibold text-gray-800">${escapeHtml(data.user.name)}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">🆔 No Anggota</span>
                                <p class="font-semibold text-gray-800">${escapeHtml(data.user.no_anggota || '-')}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">📅 Jatuh Tempo</span>
                                <p class="font-semibold ${data.hari_terlambat > 0 ? 'text-red-600' : 'text-gray-800'}">
                                    ${data.tgl_jatuh_tempo || '-'}
                                    ${data.hari_terlambat > 0 ? ` (Terlambat ${data.hari_terlambat} hari)` : ''}
                                </p>
                            </div>
                            <div>
                                <span class="text-gray-500">🔖 Kode Eksemplar</span>
                                <p class="font-mono text-sm">${escapeHtml(data.kode_eksemplar)}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                        <h4 class="font-semibold text-gray-800 mb-3">💰 Detail Denda</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span>Denda Keterlambatan:</span>
                                <span id="dendaTerlambatText" class="font-semibold text-red-600">Rp ${dendaTerlambat.toLocaleString('id-ID')}</span>
                            </div>
                            <div class="flex justify-between denda-rusak-row" style="display:none;">
                                <span>Denda Kerusakan:</span>
                                <span id="dendaRusakText" class="font-semibold text-red-600">Rp 0</span>
                            </div>
                            <div class="border-t border-yellow-200 pt-2 mt-2">
                                <div class="flex justify-between font-bold">
                                    <span>TOTAL DENDA:</span>
                                    <span id="dendaTotalText" class="text-lg text-red-600">Rp ${dendaTerlambat.toLocaleString('id-ID')}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Kolom Kanan - Form -->
                <div class="space-y-4">
                    <div>
                        <label class="block mb-2 font-medium text-gray-700">📅 Tanggal Pengembalian</label>
                        <input type="date" name="tanggal_pengembalian" id="tanggalPengembalian"
                            value="${today}" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label class="block mb-2 font-medium text-gray-700">💳 Metode Pembayaran</label>
                        <div class="flex gap-3">
                            <label class="flex items-center gap-2 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 flex-1 transition">
                                <input type="radio" name="payment_method" value="tunai" checked onchange="togglePaymentMethod('tunai')"> 💰 Tunai
                            </label>
                            <label class="flex items-center gap-2 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 flex-1 transition">
                                <input type="radio" name="payment_method" value="qris" onchange="togglePaymentMethod('qris')"> 📱 QRIS
                            </label>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block mb-2 font-medium text-gray-700">🔍 Kondisi Buku</label>
                        <select name="kondisi_kembali" id="kondisiBuku" class="w-full border border-gray-300 rounded-lg p-2.5" data-harga="${hargaBuku}">
                            <option value="baik">✅ Baik - Tidak ada denda tambahan</option>
                            <option value="rusak_ringan">⚠️ Rusak Ringan - Tambahan Rp 5.000</option>
                            <option value="rusak_berat">🔥 Rusak Berat - Tambahan Rp 50.000</option>
                            <option value="hilang">❌ Hilang - Ganti rugi harga buku</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block mb-2 font-medium text-gray-700">📝 Catatan (Opsional)</label>
                        <textarea name="catatan_kondisi" rows="3" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500"
                            placeholder="Contoh: Halaman 20-25 sobek, sampul sedikit kotor..."></textarea>
                    </div>
                </div>
            </div>
            
            <div class="flex gap-3 pt-6 mt-4 border-t">
                <button type="button" onclick="tutupModal()" 
                    class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 rounded-lg font-medium transition">
                    ❌ Batal
                </button>
                <button type="submit" id="btnSubmit"
                    class="flex-1 bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-medium transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    ✅ Proses Pengembalian
                </button>
            </div>
        </form>
    `;
    
    // Set tanggal
    const tanggalInput = document.getElementById('tanggalPengembalian');
    if (tanggalInput) tanggalInput.value = today;
    
    // Event listener untuk kondisi buku
    const kondisiBuku = document.getElementById('kondisiBuku');
    if (kondisiBuku) {
        const harga = kondisiBuku.getAttribute('data-harga');
        kondisiBuku.addEventListener('change', function() {
            hitungDendaRusak(this.value, harga);
        });
    }
    
    // Loading state saat submit
    const form = document.getElementById('formPengembalian');
    if (form) {
        form.addEventListener('submit', function(e) {
            const btn = document.getElementById('btnSubmit');
            btn.disabled = true;
            btn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memproses...';
        });
    }
}

function hitungDendaRusak(kondisi, hargaBuku) {
    let dendaRusak = 0;
    
    let hargaBersih = 0;
    if (typeof hargaBuku === 'string') {
        hargaBersih = parseInt(hargaBuku.replace(/[^0-9]/g, '')) || 0;
    } else {
        hargaBersih = parseInt(hargaBuku) || 0;
    }
    
    if (kondisi === 'rusak_ringan') {
        dendaRusak = 5000;
    } else if (kondisi === 'rusak_berat') {
        dendaRusak = 50000;
    } else if (kondisi === 'hilang') {
        dendaRusak = hargaBersih > 0 ? hargaBersih : 50000;
    }
    
    const dendaTerlambat = parseInt(document.getElementById('dendaTerlambatInput')?.value || 0);
    const total = dendaTerlambat + dendaRusak;
    
    const dendaRusakInput = document.getElementById('dendaRusakInput');
    const dendaRusakText = document.getElementById('dendaRusakText');
    const dendaTotalText = document.getElementById('dendaTotalText');
    const dendaRusakRow = document.querySelector('.denda-rusak-row');
    
    if (dendaRusakInput) dendaRusakInput.value = dendaRusak;
    if (dendaRusakText) dendaRusakText.innerHTML = 'Rp ' + dendaRusak.toLocaleString('id-ID');
    if (dendaTotalText) dendaTotalText.innerHTML = 'Rp ' + total.toLocaleString('id-ID');
    if (dendaRusakRow) {
        dendaRusakRow.style.display = dendaRusak > 0 ? 'flex' : 'none';
    }
}

function togglePaymentMethod(method) {
    console.log('Payment method selected:', method);
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

// Cari dengan barcode
document.getElementById('cariBtn')?.addEventListener('click', function() {
    let kode = document.getElementById('kodeEksemplar').value.trim();
    if (!kode) {
        alert('Masukkan kode eksemplar');
        return;
    }
    
    fetch(`/petugas/sirkulasi/cari-peminjaman?kode_eksemplar=${encodeURIComponent(kode)}`)
        .then(r => r.json())
        .then(r => {
            if (r.success && r.data) {
                prosesPengembalian(r.data.id);
                document.getElementById('kodeEksemplar').value = '';
            } else {
                alert(r.message || 'Peminjaman tidak ditemukan');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Gagal mencari: ' + err.message);
        });
});

// Enter key untuk barcode
document.getElementById('kodeEksemplar')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('cariBtn')?.click();
    }
});
</script>
@endpush