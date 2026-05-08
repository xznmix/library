@extends('petugas.layouts.app')

@section('title','Pengembalian Berhasil')

@section('content')

<div class="max-w-2xl mx-auto p-6">

<div class="bg-white rounded-xl shadow p-8">

<div class="text-center mb-8">

<div class="text-6xl mb-3">
✅
</div>

<h2 class="text-2xl font-bold text-green-600">
Pengembalian Berhasil
</h2>

</div>

<div class="space-y-3">

<p>
<b>Buku:</b>
{{ $peminjaman->buku->judul ?? '-' }}
</p>

<p>
<b>Anggota:</b>
{{ $peminjaman->user->name ?? '-' }}
</p>

<p>
<b>Denda:</b>

Rp
{{ number_format(($peminjaman->denda ??0)+($peminjaman->denda_rusak ??0),0,',','.') }}

</p>

<p>
<b>Kondisi:</b>

{{ ucfirst($peminjaman->kondisi_kembali ?? 'Baik') }}

</p>

</div>


<div class="grid grid-cols-2 gap-4 mt-8">

<a href="{{ route('petugas.sirkulasi.pengembalian.index') }}"
class="bg-green-600 text-white p-3 rounded text-center">
Pengembalian Lagi
</a>

<a href="{{ route('petugas.sirkulasi.riwayat') }}"
class="bg-indigo-600 text-white p-3 rounded text-center">
Riwayat
</a>

</div>

</div>

</div>

@endsection