@extends('petugas.layouts.app')

@section('title', 'Detail Kunjungan')

@section('content')
<div class="p-4 md:p-6 max-w-4xl mx-auto">
    
    <div class="mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('petugas.kunjungan.index') }}" 
               class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detail Kunjungan</h1>
                <p class="text-sm text-gray-500 mt-1">Informasi lengkap kunjungan perpustakaan</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50">
            <h3 class="font-semibold text-gray-800">📋 Informasi Kunjungan</h3>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500">Nama Pengunjung</p>
                    <p class="font-medium text-gray-800">{{ $kunjungan->nama }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Jenis</p>
                    <p class="font-medium capitalize">{{ $kunjungan->jenis }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Kelas / Kontak</p>
                    <p class="font-medium">{{ $kunjungan->kelas ?? $kunjungan->no_hp ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Tanggal Kunjungan</p>
                    <p class="font-medium">{{ \Carbon\Carbon::parse($kunjungan->tanggal)->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Jam Masuk</p>
                    <p class="font-medium">{{ \Carbon\Carbon::parse($kunjungan->jam_masuk)->format('H:i:s') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">NISN/NIK</p>
                    <p class="font-medium">{{ $kunjungan->user->nisn_nik ?? '-' }}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-sm text-gray-500">Keperluan</p>
                    <p class="font-medium">{{ $kunjungan->keperluan ?? '-' }}</p>
                </div>
                @if($kunjungan->alamat)
                <div class="md:col-span-2">
                    <p class="text-sm text-gray-500">Alamat</p>
                    <p class="font-medium">{{ $kunjungan->alamat }}</p>
                </div>
                @endif
            </div>
            
            <div class="mt-6 flex justify-end">
                <a href="{{ route('petugas.kunjungan.index') }}" 
                   class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    Kembali
                </a>
            </div>
        </div>
    </div>
</div>
@endsection