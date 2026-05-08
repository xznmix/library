{{-- MODAL APPROVE --}}
<div id="approveModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-6 py-4">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Setujui Anggota</h3>
                        <p class="text-sm text-gray-500">Konfirmasi persetujuan keanggotaan</p>
                    </div>
                </div>
                
                <p class="text-gray-600 mb-4" id="approveName"></p>
                
                <div class="bg-green-50 border border-green-100 rounded-lg p-3 mb-4">
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-xs text-green-700">
                            Anggota akan mendapatkan nomor anggota otomatis dan bisa langsung meminjam buku setelah disetujui.
                        </p>
                    </div>
                </div>
                
                <form id="approveForm" method="POST" class="flex justify-end gap-3">
                    @csrf
                    <button type="button" onclick="closeApproveModal()" 
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Ya, Setujui
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- MODAL REJECT --}}
<div id="rejectModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-6 py-4">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Tolak Pendaftaran</h3>
                        <p class="text-sm text-gray-500">Berikan alasan penolakan</p>
                    </div>
                </div>
                
                <p class="text-gray-600 mb-2" id="rejectName"></p>
                
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Alasan Penolakan <span class="text-red-500">*</span>
                        </label>
                        <textarea name="alasan_penolakan" 
                                  rows="4" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-200 focus:border-red-500"
                                  placeholder="Jelaskan alasan mengapa pendaftaran ditolak..."
                                  required></textarea>
                        <p class="text-xs text-gray-500 mt-1">Minimal 10 karakter</p>
                    </div>
                    
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeRejectModal()" 
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Tolak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DEACTIVATE --}}
<div id="deactivateModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-6 py-4">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Nonaktifkan Anggota</h3>
                        <p class="text-sm text-gray-500">Anggota tidak akan bisa meminjam buku</p>
                    </div>
                </div>
                
                <p class="text-gray-600 mb-2" id="deactivateName"></p>
                
                <form id="deactivateForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Alasan Penonaktifan <span class="text-red-500">*</span>
                        </label>
                        <textarea name="alasan" 
                                  rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-200 focus:border-orange-500"
                                  required></textarea>
                    </div>
                    
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeDeactivateModal()" 
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                            Nonaktifkan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Approve Modal
function openApproveModal(id, name) {
    document.getElementById('approveModal').classList.remove('hidden');
    document.getElementById('approveName').innerText = 'Setujui ' + name + ' sebagai anggota?';
    document.getElementById('approveForm').action = '/petugas/keanggotaan/' + id + '/approve';
}

function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
}

// Reject Modal
function openRejectModal(id, name) {
    document.getElementById('rejectModal').classList.remove('hidden');
    document.getElementById('rejectName').innerText = 'Tolak pendaftaran ' + name + '?';
    document.getElementById('rejectForm').action = '/petugas/keanggotaan/' + id + '/reject';
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

// Deactivate Modal
function openDeactivateModal(id, name) {
    document.getElementById('deactivateModal').classList.remove('hidden');
    document.getElementById('deactivateName').innerText = 'Nonaktifkan ' + name + '?';
    document.getElementById('deactivateForm').action = '/petugas/keanggotaan/' + id + '/deactivate';
}

function closeDeactivateModal() {
    document.getElementById('deactivateModal').classList.add('hidden');
}

// Close modals on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeApproveModal();
        closeRejectModal();
        closeDeactivateModal();
    }
});
</script>