@extends('admin.layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Akun</h1>
                    <p class="mt-1 text-gray-600">Perbarui informasi akun pengguna</p>
                </div>
                <a href="{{ route('admin.kelola-akun.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
            </div>
            
            <!-- User Info Card -->
            <div class="mt-6 bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                            <span class="text-indigo-600 font-semibold text-lg">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $user->name }}</h3>
                        <div class="flex flex-wrap items-center gap-2 mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($user->status === 'active') 
                                    bg-green-100 text-green-800 
                                @else 
                                    bg-red-100 text-red-800 
                                @endif">
                                {{ $user->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                            </span>
                            <span class="text-sm text-gray-600">ID: {{ $user->nisn_nik }}</span>
                            <span class="text-sm text-gray-600">Role: {{ ucfirst($user->role) }}</span>
                            <span class="text-sm text-gray-500">Terdaftar: {{ $user->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <form method="POST" action="{{ route('admin.kelola-akun.update', $user->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
                <!-- Form Header -->
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Informasi Pengguna</h3>
                    <p class="mt-1 text-sm text-gray-600">Perbarui data identitas dan peran pengguna</p>
                </div>

                <!-- Form Content -->
                <div class="p-6 space-y-6">
                    <!-- Identitas Field -->
                    <div>
                        <label for="nisn_nik" class="block text-sm font-medium text-gray-700 mb-2">
                            Nomor Identitas
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   id="nisn_nik" 
                                   name="nisn_nik" 
                                   value="{{ old('nisn_nik', $user->nisn_nik) }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors
                                          @error('nisn_nik') border-red-500 @enderror"
                                   placeholder="Masukkan NISN atau NIK">
                            @error('nisn_nik')
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            @enderror
                        </div>
                        @error('nisn_nik')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Name Field -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Lengkap
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $user->name) }}"
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors
                                      @error('name') border-red-500 @enderror"
                               placeholder="Masukkan nama lengkap">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $user->email) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors
                                      @error('email') border-red-500 @enderror"
                               placeholder="email@contoh.com">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @else
                            <p class="mt-2 text-xs text-gray-500">Email digunakan untuk notifikasi sistem</p>
                        @enderror
                    </div>

                    <!-- Role Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Peran Pengguna
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @php
                                $roles = [
                                    'siswa' => ['label' => 'Siswa', 'icon' => '🎓', 'color' => 'border-blue-200'],
                                    'guru' => ['label' => 'Guru', 'icon' => '👨‍🏫', 'color' => 'border-green-200'],
                                    'pegawai' => ['label' => 'Pegawai', 'icon' => '💼', 'color' => 'border-purple-200'],
                                    'umum' => ['label' => 'Umum', 'icon' => '👤', 'color' => 'border-gray-200'],
                                ];
                            @endphp
                            
                            @foreach($roles as $roleValue => $roleData)
                                <label class="relative">
                                    <input type="radio" 
                                           name="role" 
                                           value="{{ $roleValue }}"
                                           {{ old('role', $user->role) == $roleValue ? 'checked' : '' }}
                                           class="sr-only peer">
                                    <div class="p-4 border-2 rounded-lg text-center cursor-pointer transition-all duration-200
                                                {{ $roleData['color'] }}
                                                peer-checked:border-indigo-500 peer-checked:ring-2 peer-checked:ring-indigo-200
                                                hover:border-gray-300">
                                        <div class="text-2xl mb-2">{{ $roleData['icon'] }}</div>
                                        <div class="font-medium text-gray-900">{{ $roleData['label'] }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('role')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Status Akun
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="relative">
                                <input type="radio" 
                                       name="status" 
                                       value="active"
                                       {{ old('status', $user->status) == 'active' ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="p-4 border-2 rounded-lg text-center cursor-pointer transition-all duration-200
                                            border-gray-200
                                            peer-checked:border-green-500 peer-checked:ring-2 peer-checked:ring-green-200
                                            hover:border-gray-300">
                                    <div class="flex flex-col items-center">
                                        <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center mb-2">
                                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div class="font-medium text-gray-900">Aktif</div>
                                        <div class="text-xs text-gray-500 mt-1">Dapat login</div>
                                    </div>
                                </div>
                            </label>
                            
                            <label class="relative">
                                <input type="radio" 
                                       name="status" 
                                       value="inactive"
                                       {{ old('status', $user->status) == 'inactive' ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="p-4 border-2 rounded-lg text-center cursor-pointer transition-all duration-200
                                            border-gray-200
                                            peer-checked:border-red-500 peer-checked:ring-2 peer-checked:ring-red-200
                                            hover:border-gray-300">
                                    <div class="flex flex-col items-center">
                                        <div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center mb-2">
                                            <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div class="font-medium text-gray-900">Nonaktif</div>
                                        <div class="text-xs text-gray-500 mt-1">Tidak dapat login</div>
                                    </div>
                                </div>
                            </label>
                        </div>
                        @error('status')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Footer -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                            Terakhir diperbarui: {{ $user->updated_at->format('d M Y H:i') }}
                        </div>
                        <div class="flex space-x-3">
                            <button type="button" 
                                    onclick="window.location.href='{{ route('admin.kelola-akun.index') }}'"
                                    class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                                    class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Danger Zone (Optional) -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-red-400 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        <h4 class="text-sm font-medium text-red-800">Zona Berbahaya</h4>
                        <p class="mt-1 text-sm text-red-700">
                            Hati-hati saat mengubah status menjadi nonaktif. Pengguna tidak akan dapat mengakses sistem.
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add visual feedback for radio selections
    const radioCards = document.querySelectorAll('input[type="radio"] + div');
    
    radioCards.forEach(card => {
        card.addEventListener('click', function() {
            // Remove ring from all cards in the same group
            const groupName = this.previousElementSibling.name;
            document.querySelectorAll(`input[name="${groupName}"] + div`).forEach(c => {
                c.classList.remove('ring-2', 'ring-indigo-200');
            });
        });
    });
    
    // Form submission confirmation for status change
    const form = document.querySelector('form');
    const currentStatus = "{{ $user->status }}";
    
    form.addEventListener('submit', function(e) {
        const newStatus = document.querySelector('input[name="status"]:checked').value;
        
        if (currentStatus === 'active' && newStatus === 'inactive') {
            if (!confirm('Yakin ingin menonaktifkan akun ini? Pengguna tidak akan bisa login lagi.')) {
                e.preventDefault();
            }
        }
        
        if (currentStatus === 'inactive' && newStatus === 'active') {
            if (!confirm('Yakin ingin mengaktifkan akun ini? Pengguna akan bisa login kembali.')) {
                e.preventDefault();
            }
        }
    });
});
</script>
@endpush

<style>
/* Custom radio card hover effect */
input[type="radio"] + div {
    transition: all 0.2s ease-in-out;
}

input[type="radio"]:checked + div {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Focus styles */
input:focus, button:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}
</style>
@endsection