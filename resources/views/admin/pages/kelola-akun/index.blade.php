@extends('admin.layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-biru-50 p-4 md:p-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-hitam-800 mb-2">Kelola Akun Pengguna</h1>
                <p class="text-hitam-600 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    <span>Total {{ $users->total() }} akun • {{ $users->where('status', 'active')->count() }} aktif</span>
                </p>
            </div>
            
            <div class="flex flex-wrap gap-3">
                <!-- Import Excel Button -->
                <button type="button"
                        onclick="openImportModal()"
                        class="inline-flex items-center gap-2 px-4 py-3 bg-hijau-500 hover:bg-hijau-600 text-white rounded-lg font-medium transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Import Excel
                </button>
                
                <!-- Add Account Button -->
                <a href="{{ route('admin.kelola-akun.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-3 bg-gradient-to-r from-biru-600 to-biru-700 hover:from-biru-700 hover:to-biru-800 text-white rounded-lg font-medium transition-all shadow-sm hover:shadow">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Akun Baru
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-hitam-500">Total Akun</p>
                        <p class="text-2xl font-bold text-hitam-800">{{ $users->total() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-biru-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-biru-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-hitam-500">Siswa</p>
                        <p class="text-2xl font-bold text-hitam-800">{{ $users->where('role', 'siswa')->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-hijau-100 rounded-lg flex items-center justify-center">
                        <span class="text-2xl">🎓</span>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-hitam-500">Guru</p>
                        <p class="text-2xl font-bold text-hitam-800">{{ $users->where('role', 'guru')->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <span class="text-2xl">👨‍🏫</span>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-hitam-500">Aktif</p>
                        <p class="text-2xl font-bold text-hitam-800">{{ $users->where('status', 'active')->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-hijau-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-hijau-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter -->
        <form method="GET" action="{{ route('admin.kelola-akun.index') }}">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" 
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari nama, NISN, atau NIK..."
                            class="w-full pl-4 pr-4 py-3 border border-gray-300 rounded-lg">
                    </div>
                </div>

                <div class="flex gap-3">
                    <select name="role" class="px-4 py-3 border border-gray-300 rounded-lg">
                        <option value="">Semua Role</option>
                        <option value="siswa" {{ request('role')=='siswa'?'selected':'' }}>Siswa</option>
                        <option value="guru" {{ request('role')=='guru'?'selected':'' }}>Guru</option>
                        <option value="pegawai" {{ request('role')=='pegawai'?'selected':'' }}>Pegawai</option>
                        <option value="umum" {{ request('role')=='umum'?'selected':'' }}>Umum</option>
                    </select>

                    <select name="status" class="px-4 py-3 border border-gray-300 rounded-lg">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status')=='active'?'selected':'' }}>Aktif</option>
                        <option value="inactive" {{ request('status')=='inactive'?'selected':'' }}>Nonaktif</option>
                    </select>

                    <button type="submit"
                            class="px-4 py-3 bg-biru-600 text-white rounded-lg hover:bg-biru-700">
                        Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- ===== TABLE SECTION ===== -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-biru-600 to-biru-700">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">No</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">No. Anggota</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Pengguna</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Identitas</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Peran</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse ($users as $index => $user)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-hitam-500">{{ $users->firstItem() + $index }}</td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->no_anggota)
                                <span class="font-mono text-sm font-medium text-biru-600 bg-biru-50 px-2 py-1 rounded">{{ $user->no_anggota }}</span>
                            @else
                                <span class="text-hitam-400 text-xs">-</span>
                            @endif
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br 
                                        @if($user->role === 'siswa') from-biru-100 to-biru-200 text-biru-600
                                        @elseif($user->role === 'guru') from-hijau-100 to-hijau-200 text-hijau-600
                                        @elseif($user->role === 'pegawai') from-purple-100 to-purple-200 text-purple-600
                                        @else from-gray-100 to-gray-200 text-hitam-600 @endif
                                        flex items-center justify-center font-semibold">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-semibold text-hitam-800">{{ $user->name }}</div>
                                    @if($user->email)
                                    <div class="text-sm text-hitam-500">{{ $user->email }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            <div class="text-sm text-hitam-800 font-mono">{{ $user->nisn_nik }}</div>
                            @if($user->kelas || $user->jurusan)
                                <div class="text-xs text-hitam-500 mt-1">{{ $user->kelas }} {{ $user->jurusan }}</div>
                            @endif
                        </td>

                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                @if($user->role === 'siswa') bg-biru-100 text-biru-800
                                @elseif($user->role === 'guru') bg-hijau-100 text-hijau-800
                                @elseif($user->role === 'pegawai') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-hitam-800 @endif">
                                @if($user->role === 'siswa') 🎓
                                @elseif($user->role === 'guru') 👨‍🏫
                                @elseif($user->role === 'pegawai') 💼
                                @else 👤 @endif
                                {{ strtoupper($user->role) }}
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                @if($user->status === 'active') bg-hijau-100 text-hijau-800
                                @else bg-oren-100 text-oren-800 @endif">
                                <span class="w-2 h-2 rounded-full mr-1.5
                                    @if($user->status === 'active') bg-hijau-500
                                    @else bg-oren-500 @endif">
                                </span>
                                {{ $user->status === 'active' ? 'AKTIF' : 'NONAKTIF' }}
                            </span>
                            @if($user->masa_berlaku)
                                <div class="text-xs text-hitam-500 mt-1">Berlaku: {{ \Carbon\Carbon::parse($user->masa_berlaku)->format('d/m/Y') }}</div>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <!-- Edit Button -->
                                <a href="{{ route('admin.kelola-akun.edit', $user->id) }}" 
                                   class="p-2 text-biru-600 hover:bg-biru-50 rounded-lg transition-colors duration-200" title="Edit Akun">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                
                                <!-- Reset Password Button -->
                                <form action="{{ route('admin.kelola-akun.reset', $user->id) }}" method="POST" class="inline-block" id="reset-form-{{ $user->id }}">
                                    @csrf
                                    <button type="button"
                                            onclick="confirmReset({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->nisn_nik }}')"
                                            class="p-2 text-oren-600 hover:bg-oren-50 rounded-lg transition-colors duration-200" title="Reset Password">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                        </svg>
                                    </button>
                                </form>
                                
                                <!-- Delete Button -->
                                <form action="{{ route('admin.kelola-akun.destroy', $user->id) }}" method="POST" class="inline-block" id="delete-form-{{ $user->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                            onclick="confirmDelete({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->role }}', '{{ $user->no_anggota ?? '-' }}')"
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200" title="Hapus Akun">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="text-hitam-400">
                                <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13-5.75a4 4 0 01-4 4m4-4a4 4 0 00-4-4m-12 8a4 4 0 114 4m-4-4a4 4 0 104 4m12-4a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <p class="text-lg font-medium text-hitam-800 mb-2">Belum ada akun</p>
                                <a href="{{ route('admin.kelola-akun.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-biru-600 text-white rounded-lg">Tambah Akun Pertama</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>

<!-- IMPORT EXCEL MODAL -->
<div id="importModal" class="fixed inset-0 z-50 overflow-y-auto hidden" style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" onclick="closeImportModal()">
            <div class="absolute inset-0 bg-hitam-800 opacity-75"></div>
        </div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <div class="bg-gradient-to-r from-hijau-600 to-hijau-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white">Import Data dari Excel</h3>
                    <button onclick="closeImportModal()" class="text-white hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <form method="POST" action="{{ route('admin.kelola-akun.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="px-6 py-6">
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-hitam-700 mb-2">File Excel <span class="text-red-500">*</span></label>
                        <input type="file" name="excel_file" accept=".xlsx,.xls,.csv" class="w-full border border-gray-300 rounded-lg p-2" required>
                    </div>
                    
                    <div class="bg-biru-50 border border-biru-100 rounded-lg p-4 mb-4">
                        <p class="text-sm text-biru-800">Format: nisn_nik, name, email, role, no_anggota (opsional), kelas, phone, address</p>
                    </div>
                    
                    <div class="mb-6">
                        <a href="{{ route('admin.kelola-akun.template') }}" class="text-hijau-600 hover:text-hijau-800">📥 Download Template Excel</a>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                    <button type="button" onclick="closeImportModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-hitam-700 hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-hijau-600 text-white rounded-lg hover:bg-hijau-700">Import Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Toast notification helper
function showToast(icon, title) {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });
    Toast.fire({ icon: icon, title: title });
}

// Reset Password Confirmation
function confirmReset(userId, userName, nisnNik) {
    Swal.fire({
        title: 'Reset Password?',
        html: `
            <div class="text-left">
                <p class="mb-3">Password untuk akun:</p>
                <div class="bg-gray-100 p-3 rounded-lg mb-3">
                    <p><strong>Nama:</strong> ${userName}</p>
                </div>
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 text-yellow-700 text-sm">
                    Password akan direset ke: <strong>${nisnNik}</strong>
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#F97316',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Reset Password!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Meresuset...',
                text: 'Mohon tunggu',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
            document.getElementById(`reset-form-${userId}`).submit();
        }
    });
}

// Delete Account Confirmation
function confirmDelete(userId, userName, userRole, noAnggota) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        html: `
            <div class="text-left">
                <p class="mb-3">Anda akan menghapus akun:</p>
                <div class="bg-gray-100 p-3 rounded-lg mb-3">
                    <p><strong>Nama:</strong> ${userName}</p>
                    <p><strong>Role:</strong> ${userRole.toUpperCase()}</p>
                    <p><strong>No. Anggota:</strong> ${noAnggota}</p>
                </div>
                <div class="bg-red-50 border-l-4 border-red-500 p-3 text-red-700 text-sm">
                    <strong>⚠️ PERINGATAN!</strong><br>
                    Tindakan ini <strong>TIDAK DAPAT DIBATALKAN</strong> dan akan menghapus:<br>
                    • Data profil dan akun<br>
                    • Riwayat peminjaman buku<br>
                    • Data denda (jika ada)<br>
                    • Riwayat kunjungan<br>
                    • Dan semua data terkait lainnya
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus Permanen!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Menghapus...',
                text: 'Mohon tunggu, sedang menghapus data akun',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
            document.getElementById(`delete-form-${userId}`).submit();
        }
    });
}

// Import Modal Functions
function openImportModal() {
    document.getElementById('importModal').style.display = 'block';
}

function closeImportModal() {
    document.getElementById('importModal').style.display = 'none';
}

// Close modal when clicking outside (handled by onclick on overlay)

// Show session messages
@if(session('success'))
Swal.fire({
    title: 'Berhasil!',
    text: '{{ session('success') }}',
    icon: 'success',
    confirmButtonColor: '#3085d6',
    timer: 3000
});
@endif

@if(session('error'))
Swal.fire({
    title: 'Gagal!',
    text: '{{ session('error') }}',
    icon: 'error',
    confirmButtonColor: '#d33'
});
@endif
</script>
@endpush

<style>
[x-cloak] { display: none !important; }
</style>