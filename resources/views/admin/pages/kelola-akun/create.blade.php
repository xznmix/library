@extends('admin.layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-hitam-800">Tambah Akun Baru</h1>
                    <p class="mt-1 text-hitam-600">Buat akun baru untuk pengguna sistem</p>
                </div>
                <a href="{{ route('admin.kelola-akun.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-hitam-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
            </div>
            
            <!-- Progress Steps -->
            <div class="mt-6">
                <div class="flex items-center">
                    <div class="flex items-center text-biru-600">
                        <div class="w-8 h-8 rounded-full bg-biru-100 flex items-center justify-center">
                            <span class="text-sm font-semibold">1</span>
                        </div>
                        <span class="ml-2 text-sm font-medium">Informasi Dasar</span>
                    </div>
                    <div class="flex-1 h-0.5 bg-gray-200 mx-4"></div>
                    <div class="flex items-center text-hitam-500">
                        <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                            <span class="text-sm font-semibold">2</span>
                        </div>
                        <span class="ml-2 text-sm font-medium">Konfirmasi</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Form -->
        <form method="POST" action="{{ route('admin.kelola-akun.store') }}" class="space-y-6" id="createForm">
            @csrf

            <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
                <!-- Form Header -->
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-hitam-800">Informasi Pengguna Baru</h3>
                    <p class="mt-1 text-sm text-hitam-600">Isi data pengguna yang akan dibuat</p>
                </div>

                <!-- Form Content -->
                <div class="p-6 space-y-6">
                    <!-- Identitas Field -->
                    <div>
                        <label for="nisn_nik" class="block text-sm font-medium text-hitam-700 mb-2">
                            Nomor Identitas
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   id="nisn_nik" 
                                   name="nisn_nik" 
                                   value="{{ old('nisn_nik') }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-biru-500 focus:border-biru-500 transition-colors
                                          @error('nisn_nik') border-red-500 @enderror"
                                   placeholder="Masukkan NISN atau NIK"
                                   maxlength="20">
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
                        @else
                            <div class="mt-2 flex items-center justify-between">
                                <p class="text-xs text-hitam-500">Gunakan NISN untuk siswa atau NIK untuk lainnya</p>
                                <span id="charCount" class="text-xs text-hitam-500">0/20</span>
                            </div>
                        @enderror
                    </div>

                    <!-- Name Field -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-hitam-700 mb-2">
                            Nama Lengkap
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}"
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-biru-500 focus:border-biru-500 transition-colors
                                      @error('name') border-red-500 @enderror"
                               placeholder="Masukkan nama lengkap">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-hitam-700 mb-2">
                            Email
                            <span class="text-xs font-normal text-hitam-500 ml-1">(Wajib Isi)</span>
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-biru-500 focus:border-biru-500 transition-colors
                                      @error('email') border-red-500 @enderror"
                               placeholder="email@contoh.com">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @else
                            <p class="mt-2 text-xs text-hitam-500">Email digunakan login pengguna</p>
                        @enderror
                    </div>

                    <!-- Role Selection -->
                    <div>
                        <label class="block text-sm font-medium text-hitam-700 mb-3">
                            Peran Pengguna
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @php
                                $roles = [
                                    'siswa' => ['label' => 'Siswa', 'icon' => '🎓', 'color' => 'border-biru-200', 'desc' => 'NISN'],
                                    'guru' => ['label' => 'Guru', 'icon' => '👨‍🏫', 'color' => 'border-hijau-200', 'desc' => 'NIK'],
                                    'pegawai' => ['label' => 'Pegawai', 'icon' => '💼', 'color' => 'border-purple-200', 'desc' => 'NIK'],
                                    'umum' => ['label' => 'Umum', 'icon' => '👤', 'color' => 'border-gray-200', 'desc' => 'ID'],
                                ];
                            @endphp
                            
                            @foreach($roles as $roleValue => $roleData)
                                <label class="relative">
                                    <input type="radio" 
                                           name="role" 
                                           value="{{ $roleValue }}"
                                           {{ old('role', 'siswa') == $roleValue ? 'checked' : '' }}
                                           class="sr-only peer role-radio"
                                           data-placeholder="{{ $roleData['desc'] }}">
                                    <div class="p-4 border-2 rounded-lg text-center cursor-pointer transition-all duration-200
                                                {{ $roleData['color'] }}
                                                peer-checked:border-biru-500 peer-checked:ring-2 peer-checked:ring-biru-200
                                                hover:border-gray-300">
                                        <div class="text-2xl mb-2">{{ $roleData['icon'] }}</div>
                                        <div class="font-medium text-hitam-800">{{ $roleData['label'] }}</div>
                                        <div class="text-xs text-hitam-500 mt-1">{{ $roleData['desc'] }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('role')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Option (Hidden fields for future use) -->
                    <div id="manualFields" class="hidden">
                        <div>
                            <label for="password" class="block text-sm font-medium text-hitam-700 mb-2">Password</label>
                            <input type="password" id="password" name="password" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-hitam-700 mb-2">Konfirmasi Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                        </div>
                    </div>
                    
                    <!-- Hidden auto password radio -->
                    <input type="radio" id="autoPassword" name="password_option" value="auto" checked class="hidden">
                </div>

                <!-- Form Footer -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-hitam-500">
                            Akun akan dibuat dengan status <span class="font-medium">Aktif</span>
                        </div>
                        <div class="flex space-x-3">
                            <button type="submit"
                                    class="px-5 py-2.5 bg-biru-600 text-white rounded-lg text-sm font-medium hover:bg-biru-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-500"
                                    id="submitBtn">
                                Buat Akun
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Information Card -->
            <div class="bg-biru-50 border border-biru-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-biru-400 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h4 class="text-sm font-medium text-biru-800">Panduan Pembuatan Akun</h4>
                        <ul class="mt-2 text-sm text-biru-700 space-y-1">
                            <li>• Pastikan nomor identitas belum digunakan sebelumnya</li>
                            <li>• Email diperlukan untuk login dan reset password</li>
                            <li>• Password default menggunakan NISN/NIK</li>
                            <li>• Pilih peran yang sesuai dengan jenis pengguna</li>
                        </ul>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Character counter for identitas
        const nisn_nikInput = document.getElementById('nisn_nik');
        const charCount = document.getElementById('charCount');
        
        if (nisn_nikInput && charCount) {
            nisn_nikInput.addEventListener('input', function() {
                charCount.textContent = `${this.value.length}/20`;
            });
            
            // Initialize character count
            charCount.textContent = `${nisn_nikInput.value.length}/20`;
        }
        
        // Dynamic placeholder based on role
        const roleRadios = document.querySelectorAll('.role-radio');
        
        roleRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                const placeholder = this.getAttribute('data-placeholder');
                if (nisn_nikInput) {
                    nisn_nikInput.placeholder = `Masukkan ${placeholder}`;
                }
            });
        });
        
        // Form submission
        const form = document.getElementById('createForm');
        const submitBtn = document.getElementById('submitBtn');
        
        if (form && submitBtn) {
            form.addEventListener('submit', function(e) {
                // Basic validation
                const nisn_nik = document.getElementById('nisn_nik')?.value.trim();
                const name = document.getElementById('name')?.value.trim();
                const email = document.getElementById('email')?.value.trim();
                const selectedRole = document.querySelector('input[name="role"]:checked');
                
                if (!nisn_nik || !name || !selectedRole) {
                    e.preventDefault();
                    alert('Harap isi semua field yang wajib diisi!');
                    return false;
                }
                
                if (!email) {
                    e.preventDefault();
                    alert('Email wajib diisi!');
                    return false;
                }
                
                // Show loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Membuat Akun...
                `;
                
                return true;
            });
        }
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

    /* Custom checkbox/radio styles */
    input[type="checkbox"]:focus, input[type="radio"]:focus {
        outline: none;
        ring-width: 2px;
        ring-color: rgba(59, 130, 246, 0.5);
    }

    /* Smooth transitions */
    input, select, button {
        transition: all 0.2s ease-in-out;
    }
</style>
@endsection