<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kunjungan Perpustakaan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #1F2937;
            background: #FFFFFF;
            padding: 30px 35px;
            position: relative;
            min-height: 100vh;
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 45pt;
            font-weight: 900;
            color: rgba(124,58,237,0.08);
            white-space: nowrap;
            z-index: 0;
            font-family: Arial, sans-serif;
            text-transform: uppercase;
            letter-spacing: 10px;
            border: 5px double rgba(124,58,237,0.08);
            padding: 25px 45px;
            border-radius: 15px;
            pointer-events: none;
        }

        .content-wrapper {
            position: relative;
            z-index: 1;
        }

        .kop-surat {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #1F2937;
        }
        
        .kop-surat-inner {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
        }
        
        .kop-logo {
            flex-shrink: 0;
            width: 90px;
            text-align: center;
        }
        
        .kop-logo img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 10px;
            display: block;
            margin: 0 auto;
        }
        
        .kop-text {
            flex: 1;
            text-align: center;
        }
        
        .kop-text h1 {
            font-size: 22pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 5px;
            color: #000000;
        }

        .kop-text h2 {
            font-size: 18pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
            color: #1F2937;
        }

        .kop-text .alamat {
            font-size: 11pt;
            margin-bottom: 5px;
        }

        .kop-text .kontak {
            font-size: 10pt;
        }

        .garis-tiga {
            border-top: 3px double #1F2937;
            margin: 15px 0;
        }

        .judul-laporan {
            text-align: center;
            margin: 25px 0;
        }

        .judul-laporan h3 {
            font-size: 16pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 5px;
            color: #1F2937;
        }

        .info-tanggal {
            text-align: right;
            margin-bottom: 20px;
            font-size: 11pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
            font-size: 11pt;
        }

        th {
            background-color: #E9D5FF;
            font-weight: bold;
            text-align: center;
            padding: 10px 5px;
            border: 1px solid #1F2937;
            text-transform: uppercase;
        }

        td {
            padding: 8px 5px;
            border: 1px solid #1F2937;
            vertical-align: top;
        }

        tr:nth-child(even) {
            background-color: #F9FAFB;
        }

        .footer {
            margin-top: 50px;
            width: 100%;
        }
        
        .ttd-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 80px;
        }
        
        .ttd-left, .ttd-right {
            flex: 1;
            text-align: center;
        }
        
        .ttd-title {
            font-weight: normal;
            margin-bottom: 5px;
            font-size: 11pt;
        }
        
        .ttd-space {
            margin: 60px 0 10px 0;
        }
        
        .ttd-name {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 10px;
            font-size: 12pt;
        }
        
        .ttd-nip {
            font-size: 10pt;
            color: #4B5563;
            margin-top: 5px;
        }

        .info-cetak {
            margin-top: 40px;
            font-size: 9pt;
            text-align: center;
            border-top: 1px solid #1F2937;
            padding-top: 12px;
            color: #6B7280;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        @media print {
            body {
                padding: 20px;
            }
            .watermark {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            th {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                background-color: #E9D5FF !important;
            }
        }
    </style>
</head>
<body>
    <div class="watermark">SMAN 1 TAMBANG</div>

    <div class="content-wrapper">
        <div class="kop-surat">
            <div class="kop-surat-inner">
                <div class="kop-logo">
                    <img src="{{ public_path('storage/logo.jpg') }}" alt="Logo Perpustakaan" onerror="this.style.display='none'">
                </div>
                <div class="kop-text">
                    <h1>PEMERINTAH PROVINSI RIAU</h1>
                    <h2>SMAN 1 TAMBANG</h2>
                    <div class="alamat">
                        Jl. Raya Pekanbaru - Bangkinang KM. 29, Tambang, Kec. Tambang, Kab. Kampar, Riau 28461
                    </div>
                    <div class="kontak">
                        Telp. (0761) 12345 | Email: sman1tambang@sch.id | Website: www.sman1tambang.sch.id
                    </div>
                </div>
                <div class="kop-logo">
                    <!-- Kosong untuk simetri -->
                </div>
            </div>
        </div>

        <div class="garis-tiga"></div>

        <div class="judul-laporan">
            <h3>LAPORAN KUNJUNGAN PERPUSTAKAAN</h3>
            <p>Nomor: 421.3/{{ date('m/Y') }}/SMA.01/LAP-KUNJUNGAN</p>
        </div>

        <div class="info-tanggal">
            <p>Tambang, {{ now()->translatedFormat('d F Y') }}</p>
        </div>

        {{-- Ringkasan Statistik --}}
        @php
            $totalKunjungan = $kunjungan->sum('total');
            $rataRata = $kunjungan->avg('total');
        @endphp

        <div class="ringkasan" style="margin-bottom: 20px; padding: 10px; background: #f3f4f6; border-radius: 5px;">
            <table style="width: auto; margin: 0 auto; border: none;">
                <tr>
                    <td style="border: none; padding: 5px;"><strong>Tahun:</strong></td>
                    <td style="border: none; padding: 5px;">{{ $year }}</td>
                    <td style="border: none; padding: 5px; width: 50px;"></td>
                    <td style="border: none; padding: 5px;"><strong>Total Kunjungan:</strong></td>
                    <td style="border: none; padding: 5px;">{{ number_format($totalKunjungan, 0, ',', '.') }} kunjungan</span></td>
                </tr>
                <tr>
                    <td style="border: none; padding: 5px;"><strong>Rata-rata per Bulan:</strong></td>
                    <td style="border: none; padding: 5px;">{{ number_format($rataRata, 1) }} kunjungan</span></td>
                    <td style="border: none; padding: 5px;"></td>
                    <td style="border: none; padding: 5px;"><strong>Bulan Tertinggi:</strong></td>
                    <td>
                        @php
                            $tertinggi = $kunjungan->sortByDesc('total')->first();
                        @endphp
                        {{ $tertinggi ? $tertinggi->nama_bulan . ' (' . $tertinggi->total . ' kunjungan)' : '-' }}
                    </td>
                </tr>
            </table>
        </div>

        {{-- Tabel Kunjungan per Bulan --}}
        <table class="data">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="20%">Bulan</th>
                    <th width="15%">Jumlah Kunjungan</th>
                    <th width="15%">Persentase</th>
                    <th width="45%">Detail / Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @php $total = $kunjungan->sum('total'); @endphp
                @foreach($kunjungan as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->nama_bulan }}</td>
                    <td class="text-center">{{ number_format($item->total, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $total > 0 ? round(($item->total / $total) * 100, 1) : 0 }}%</td>
                    <td>
                        <div style="display: flex; align-items: center;">
                            <div style="flex: 1; height: 8px; background: #e5e7eb; border-radius: 4px;">
                                <div style="height: 8px; background: #7C3AED; border-radius: 4px; width: {{ $total > 0 ? ($item->total / $total) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" style="text-align: right;"><strong>TOTAL</strong></td>
                    <td class="text-center"><strong>{{ number_format($total, 0, ',', '.') }}</strong></td>
                    <td class="text-center"><strong>100%</strong></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        {{-- Tanda Tangan --}}
        <div class="footer">
            <div class="ttd-wrapper">
                <div class="ttd-right">
                    <div class="ttd-title">Tambang, {{ now()->translatedFormat('d F Y') }}</div>
                    <div class="ttd-title">Kepala Perpustakaan,</div>
                    <div class="ttd-space"></div>
                    <div class="ttd-name">Hj. Herlina</div>
                    <div class="ttd-nip">NIP. -</div>
                </div>
                <div class="ttd-left">
                    <div class="ttd-title">Mengetahui,</div>
                    <div class="ttd-title">Kepala Sekolah,</div>
                    <div class="ttd-space"></div>
                    <div class="ttd-name">Drs. Khairullah, M.Pd</div>
                    <div class="ttd-nip">NIP. 196906251994031011</div>
                </div>
            </div>
        </div>

        {{-- Informasi Cetak --}}
        <div class="info-cetak">
            <p>
                Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} | 
                Dicetak oleh: {{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }}) |
                Laporan ini adalah dokumen resmi Perpustakaan SMAN 1 Tambang
            </p>
        </div>
    </div>
</body>
</html>