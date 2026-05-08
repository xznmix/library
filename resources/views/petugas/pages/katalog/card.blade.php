<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Kartu Katalog - {{ $buku->judul }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            background: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .card {
            width: 17.5cm;
            height: 12.5cm;
            border: 1px solid #000;
            padding: 0.8cm;
            position: relative;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
            background: white;
        }
        .header {
            text-align: center;
            border-bottom: 1px solid #000;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header h1 {
            font-size: 14pt;
            letter-spacing: 2px;
            margin-bottom: 2px;
        }
        .header p {
            font-size: 9pt;
        }
        .content {
            line-height: 1.5;
        }
        .judul-utama {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 8px;
            text-align: center;
            border-bottom: 1px dashed #ccc;
            padding-bottom: 5px;
        }
        .row {
            margin-bottom: 6px;
            font-size: 10pt;
        }
        .label {
            font-weight: bold;
            width: 85px;
            display: inline-block;
        }
        .value {
            display: inline-block;
        }
        .deskripsi-fisik {
            margin-top: 8px;
            padding-top: 5px;
            border-top: 1px dashed #ccc;
        }
        .footer {
            position: absolute;
            bottom: 0.8cm;
            left: 0.8cm;
            right: 0.8cm;
            font-size: 8pt;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 6px;
        }
        @media print {
            .card {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h1>KARTU KATALOG</h1>
            <p>PERPUSTAKAAN SMAN 1 TAMBANG</p>
        </div>
        
        <div class="content">
            <div class="judul-utama">
                {{ $buku->judul }}
                @if($buku->sub_judul)
                    <br><span style="font-size: 10pt;">{{ $buku->sub_judul }}</span>
                @endif
            </div>
            
            <div class="row">
                <span class="label">Pengarang</span>
                <span class="value">: {{ $buku->pengarang ?? '-' }}</span>
            </div>
            
            <div class="row">
                <span class="label">Penerbit</span>
                <span class="value">: {{ $buku->penerbit ?? '-' }}</span>
            </div>
            
            <div class="row">
                <span class="label">Tempat Terbit</span>
                <span class="value">: {{ $buku->kota_terbit ?? 'Jakarta' }}</span>
            </div>
            
            <div class="row">
                <span class="label">Tahun Terbit</span>
                <span class="value">: {{ $buku->tahun_terbit ?? '-' }}</span>
            </div>
            
            <div class="deskripsi-fisik">
                <div class="row">
                    <span class="label">Deskripsi Fisik</span>
                    <span class="value">: {{ $buku->jumlah_halaman ?? '?' }} hlm ; {{ $buku->ukuran ?? '?' }} cm</span>
                </div>
                <div class="row">
                    <span class="label">ISBN/ISSN</span>
                    <span class="value">: {{ $buku->isbn ?? $buku->issn ?? '-' }}</span>
                </div>
                <div class="row">
                    <span class="label">Nomor Panggil</span>
                    <span class="value">: {{ $buku->nomor_panggil ?? $buku->no_ddc ?? '-' }}</span>
                </div>
                <div class="row">
                    <span class="label">Klasifikasi</span>
                    <span class="value">: {{ $buku->no_ddc ?? '-' }}</span>
                </div>
                <div class="row">
                    <span class="label">Lokasi Rak</span>
                    <span class="value">: {{ $buku->rak ?? 'Rak Umum' }}</span>
                </div>
            </div>
            
            @if($buku->deskripsi)
            <div style="margin-top: 8px;">
                <div class="row">
                    <span class="label">Sinopsis</span>
                    <span class="value" style="font-size: 9pt;">: {{ Str::limit($buku->deskripsi, 120) }}</span>
                </div>
            </div>
            @endif
        </div>
        
        <div class="footer">
            Katalog ini dicetak dari Sistem Informasi Perpustakaan Digital SMAN 1 Tambang
        </div>
    </div>
</body>
</html>