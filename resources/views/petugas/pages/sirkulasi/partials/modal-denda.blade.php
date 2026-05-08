{{-- Modal Pengembalian dengan Denda --}}
<div id="pengembalianModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity"></div>
        
        <div class="relative bg-white rounded-xl max-w-4xl w-full p-6 shadow-2xl transform transition-all">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Form Pengembalian Buku</h3>
                <button onclick="tutupModal()" class="text-gray-400 hover:text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form action="{{ route('petugas.sirkulasi.pengembalian.proses') }}" method="POST">
                @csrf
                <input type="hidden" name="peminjaman_id" id="modalPeminjamanId">
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    {{-- Left Column: Info Peminjaman --}}
                    <div class="lg:col-span-2 space-y-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-700 mb-3">📋 Informasi Anggota</h4>
                            <div class="grid grid-cols-2 gap-3 text-sm" id="infoAnggota"></div>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-700 mb-3">📚 Informasi Buku</h4>
                            <div class="grid grid-cols-2 gap-3 text-sm" id="infoBuku"></div>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-700 mb-3">📅 Periode Peminjaman</h4>
                            <div class="grid grid-cols-3 gap-3 text-sm" id="infoPeriode"></div>
                        </div>
                    </div>
                    
                    {{-- Right Column: Kondisi & Denda --}}
                    <div class="space-y-4">
                        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                            <h4 class="font-medium text-gray-700 mb-3">🔍 Kondisi Buku</h4>
                            
                            <div class="space-y-2">
                                <label class="flex items-start p-2 bg-white rounded-lg border cursor-pointer hover:bg-green-50">
                                    <input type="radio" name="kondisi_kembali" value="baik" class="mt-1 mr-3" checked>
                                    <div>
                                        <p class="font-medium text-green-700">✅ Baik</p>
                                        <p class="text-xs text-gray-500">Buku dalam kondisi sempurna</p>
                                    </div>
                                </label>
                                
                                <label class="flex items-start p-2 bg-white rounded-lg border cursor-pointer hover:bg-yellow-50">
                                    <input type="radio" name="kondisi_kembali" value="rusak_ringan" class="mt-1 mr-3">
                                    <div>
                                        <p class="font-medium text-yellow-700">⚠️ Rusak Ringan</p>
                                        <p class="text-xs text-gray-500">Sampul sobek, coretan, dll</p>
                                    </div>
                                </label>
                                
                                <label class="flex items-start p-2 bg-white rounded-lg border cursor-pointer hover:bg-orange-50">
                                    <input type="radio" name="kondisi_kembali" value="rusak_berat" class="mt-1 mr-3">
                                    <div>
                                        <p class="font-medium text-orange-700">🔥 Rusak Berat</p>
                                        <p class="text-xs text-gray-500">Halaman hilang, bolak-balik</p>
                                    </div>
                                </label>
                                
                                <label class="flex items-start p-2 bg-white rounded-lg border cursor-pointer hover:bg-red-50">
                                    <input type="radio" name="kondisi_kembali" value="hilang" class="mt-1 mr-3">
                                    <div>
                                        <p class="font-medium text-red-700">❌ Hilang</p>
                                        <p class="text-xs text-gray-500">Buku tidak dikembalikan</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        <div class="bg-white p-4 rounded-lg border border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Catatan Kondisi
                            </label>
                            <textarea name="catatan_kondisi" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                      placeholder="Contoh: Halaman 20-25 coretan..."></textarea>
                        </div>
                        
                        <div class="bg-gradient-to-br from-purple-50 to-indigo-50 p-4 rounded-lg border border-purple-200">
                            <h4 class="font-medium text-gray-700 mb-3">💰 Perhitungan Denda</h4>
                            
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span>Denda Keterlambatan:</span>
                                    <span class="font-semibold" id="dendaTerlambatText">Rp 0</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Denda Kerusakan:</span>
                                    <span class="font-semibold" id="dendaRusakText">Rp 0</span>
                                </div>
                                <div class="border-t border-purple-200 my-2 pt-2">
                                    <div class="flex justify-between font-bold">
                                        <span>TOTAL:</span>
                                        <span class="text-lg text-red-600" id="dendaTotalText">Rp 0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <input type="hidden" name="denda_terlambat" id="dendaTerlambatInput" value="0">
                        <input type="hidden" name="denda_rusak" id="dendaRusakInput" value="0">
                        
                        <div class="flex gap-2">
                            <button type="button" onclick="tutupModal()" 
                                    class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                                Batal
                            </button>
                            <button type="submit" 
                                    class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                Kembalikan
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let dataPeminjamanModal = null;

function tampilkanModalPengembalian(data) {
    dataPeminjamanModal = data;
    
    document.getElementById('modalPeminjamanId').value = data.id;
    
    // Info Anggota
    document.getElementById('infoAnggota').innerHTML = `
        <div>
            <p class="text-xs text-gray-500">Nama</p>
            <p class="font-medium">${data.user.name}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500">No. Anggota</p>
            <p class="font-mono">${data.user.no_anggota || '-'}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500">Kelas</p>
            <p>${data.user.kelas || '-'}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500">No. HP</p>
            <p>${data.user.phone || '-'}</p>
        </div>
    `;
    
    // Info Buku
    document.getElementById('infoBuku').innerHTML = `
        <div class="col-span-2">
            <p class="text-xs text-gray-500">Judul</p>
            <p class="font-medium">${data.buku.judul}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500">Pengarang</p>
            <p>${data.buku.pengarang || '-'}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500">ISBN</p>
            <p>${data.buku.isbn || '-'}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500">Kode Eksemplar</p>
            <p class="font-mono">${data.kode_eksemplar}</p>
        </div>
    `;
    
    // Info Periode
    const tanggalPinjam = new Date(data.tanggal_pinjam);
    const jatuhTempo = new Date(data.tgl_jatuh_tempo);
    const today = new Date();
    
    const terlambat = today > jatuhTempo;
    const hariTerlambat = terlambat ? Math.floor((today - jatuhTempo) / (1000 * 60 * 60 * 24)) : 0;
    const dendaPerHari = data.buku.denda_per_hari || 1000;
    const dendaTerlambat = hariTerlambat * dendaPerHari;
    
    document.getElementById('infoPeriode').innerHTML = `
        <div>
            <p class="text-xs text-gray-500">Tanggal Pinjam</p>
            <p class="font-medium">${tanggalPinjam.toLocaleDateString('id-ID')}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500">Jatuh Tempo</p>
            <p class="font-medium ${terlambat ? 'text-red-600' : ''}">${jatuhTempo.toLocaleDateString('id-ID')}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500">Hari Ini</p>
            <p class="font-medium">${today.toLocaleDateString('id-ID')}</p>
        </div>
    `;
    
    // Set denda
    document.getElementById('dendaTerlambatText').innerText = 'Rp ' + dendaTerlambat.toLocaleString('id-ID');
    document.getElementById('dendaTerlambatInput').value = dendaTerlambat;
    
    document.getElementById('dendaRusakText').innerText = 'Rp 0';
    document.getElementById('dendaRusakInput').value = 0;
    
    document.getElementById('dendaTotalText').innerText = 'Rp ' + dendaTerlambat.toLocaleString('id-ID');
    
    // Event listener untuk kondisi
    document.querySelectorAll('input[name="kondisi_kembali"]').forEach(radio => {
        radio.addEventListener('change', function() {
            hitungDendaRusak(this.value, data.buku);
        });
    });
    
    document.getElementById('pengembalianModal').classList.remove('hidden');
}

function hitungDendaRusak(kondisi, buku) {
    const dendaTerlambat = parseFloat(document.getElementById('dendaTerlambatInput').value) || 0;
    let dendaRusak = 0;
    
    if (kondisi === 'rusak_ringan') {
        dendaRusak = 5000;
    } else if (kondisi === 'rusak_berat') {
        dendaRusak = 50000;
    } else if (kondisi === 'hilang') {
        let hargaBuku = buku.harga;
        
        // Konversi ke number (handle string, float, dll)
        if (typeof hargaBuku === 'string') {
            // Hapus titik, koma, dan ambil angka saja
            hargaBuku = parseFloat(hargaBuku.replace(/[^0-9,-]/g, '').replace(',', '.'));
        }
        
        // Pastikan number dan bulatkan
        hargaBuku = Math.round(Number(hargaBuku));
        
        // Jika NaN atau 0, pakai default
        if (isNaN(hargaBuku) || hargaBuku <= 0) {
            hargaBuku = 50000;
        }
        
        dendaRusak = hargaBuku;
    }
    
    // Format dengan Number() untuk memastikan integer
    dendaRusak = Number(dendaRusak);
    
    document.getElementById('dendaRusakInput').value = dendaRusak;
    document.getElementById('dendaRusakText').innerText = 'Rp ' + dendaRusak.toLocaleString('id-ID');
    
    const total = Number(dendaTerlambat) + dendaRusak;
    document.getElementById('dendaTotalText').innerText = 'Rp ' + total.toLocaleString('id-ID');
}

function tutupModal() {
    document.getElementById('pengembalianModal').classList.add('hidden');
}
</script>