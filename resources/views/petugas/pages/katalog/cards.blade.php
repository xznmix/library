<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Kartu Katalog</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 10pt;
        }
        .page {
            width: 21cm;
            min-height: 29.7cm;
            margin: 0 auto;
            padding: 0.5cm;
            position: relative;
        }
        .card {
            width: 9.5cm;
            height: 13.5cm;
            border: 1px solid #000;
            padding: 0.5cm;
            float: left;
            margin: 0.25cm;
            page-break-inside: avoid;
            position: relative;
        }
        .card-header {
            text-align: center;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
            margin-bottom: 8px;
        }
        .card-header h2 {
            font-size: 10pt;
        }
        .judul {
            font-weight: bold;
            font-size: 10pt;
            margin-bottom: 5px;
        }
        .info {
            font-size: 9pt;
            line-height: 1.3;
        }
        .info-row {
            margin-bottom: 3px;
        }
        .label {
            font-weight: bold;
            width: 70px;
            display: inline-block;
        }
        .clearfix {
            clear: both;
        }
        .page-break {
            page-break-after: always;
        }
        @media print {
            .page {
                margin: 0;
                padding: 0;
            }
            .card {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        @foreach($buku as $index => $item)
            <div class="card">
                <div class="card-header">
                    <h2>KARTU KATALOG</h2>
                    <small>PERPUSTAKAAN SMAN 1 TAMBANG</small>
                </div>
                <div class="judul">
                    {{ Str::limit($item->judul, 50) }}
                </div>
                <div class="info">
                    <div class="info-row">
                        <span class="label">Pengarang</span>: {{ $item->pengarang ?? '-' }}
                    </div>
                    <div class="info-row">
                        <span class="label">Penerbit</span>: {{ $item->penerbit ?? '-' }}
                    </div>
                    <div class="info-row">
                        <span class="label">Tahun</span>: {{ $item->tahun_terbit ?? '-' }}
                    </div>
                    <div class="info-row">
                        <span class="label">Deskripsi</span>: {{ $item->jumlah_halaman ?? '?' }} hlm ; {{ $item->ukuran ?? '?' }} cm
                    </div>
                    <div class="info-row">
                        <span class="label">Nomor Panggil</span>: {{ $item->nomor_panggil ?? $item->no_ddc ?? '-' }}
                    </div>
                    <div class="info-row">
                        <span class="label">Klasifikasi</span>: {{ $item->no_ddc ?? '-' }}
                    </div>
                    <div class="info-row">
                        <span class="label">ISBN</span>: {{ $item->isbn ?? '-' }}
                    </div>
                    <div class="info-row">
                        <span class="label">Lokasi</span>: {{ $item->rak ?? 'R. Baca Umum' }}
                    </div>
                </div>
                <div style="position: absolute; bottom: 0.5cm; left: 0.5cm; right: 0.5cm; font-size: 7pt; text-align: center; border-top: 1px solid #ccc; padding-top: 3px;">
                    {{ $index + 1 }} / {{ count($buku) }}
                </div>
            </div>
            @if(($index + 1) % 4 == 0 && !$loop->last)
                <div class="clearfix"></div>
                <div class="page-break"></div>
                <div class="page">
            @endif
        @endforeach
        <div class="clearfix"></div>
    </div>
</body>
</html>