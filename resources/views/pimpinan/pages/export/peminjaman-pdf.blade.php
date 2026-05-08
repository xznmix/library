<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
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

        /* Watermark */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 45pt;
            font-weight: 900;
            color: rgba(249,115,22,0.08);
            white-space: nowrap;
            z-index: 0;
            font-family: Arial, sans-serif;
            text-transform: uppercase;
            letter-spacing: 10px;
            border: 5px double rgba(249,115,22,0.08);
            padding: 25px 45px;
            border-radius: 15px;
            pointer-events: none;
        }

        .content-wrapper {
            position: relative;
            z-index: 1;
        }

        /* Kop Surat */
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

        /* Judul Laporan */
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

        .judul-laporan p {
            font-size: 11pt;
            color: #10B981;
        }

        .info-tanggal {
            text-align: right;
            margin-bottom: 20px;
            font-size: 11pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        
        th {
            background-color: #DBEAFE;
            color: #1F2937;
            font-weight: bold;
        }
        
        tr:nth-child(even) {
            background-color: #f9fafb;
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
        
        .ttd-stamp {
            margin-top: 5px;
            font-size: 9pt;
            color: #6B7280;
            font-style: italic;
        }

        .info-cetak {
            margin-top: 40px;
            font-size: 9pt;
            text-align: center;
            border-top: 1px solid #1F2937;
            padding-top: 12px;
            color: #6B7280;
        }

        .total {
            margin-top: 20px;
            font-weight: bold;
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
                background-color: #DBEAFE !important;
            }
        }
    </style>
</head>
<body>
    <div class="watermark">SMAN 1 TAMBANG</div>

    <div class="content-wrapper">
        <!-- Kop Surat -->
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
                    <!-- Kosong -->
                </div>
            </div>
        </div>

        <div class="garis-tiga"></div>

        <div class="judul-laporan">
            <h3>LAPORAN PEMINJAMAN BUKU</h3>
            <p>Nomor: 421.3/{{ date('m/Y') }}/PIMPINAN/LAP-PINJAM</p>
        </div>

        <div class="info-tanggal">
            <p>Tambang, {{ now()->translatedFormat('d F Y') }}</p>
            <p>Periode: {{ $periode }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Peminjam</th>
                    <th>Buku</th>
                    <th>Tanggal Pinjam</th>
                    <th>Tanggal Kembali</th>
                    <th>Status</th>
                    <th>Denda</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->user->name ?? '-' }}</td>
                    <td>{{ $item->buku->judul ?? '-' }}</td>
                    <td>{{ $item->tanggal_pinjam ? \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $item->tanggal_pengembalian ? \Carbon\Carbon::parse($item->tanggal_pengembalian)->format('d/m/Y') : '-' }}</td>
                    <td>
                        @if($item->status_pinjam == 'dipinjam')
                            <span style="color: orange;">Dipinjam</span>
                        @elseif($item->status_pinjam == 'terlambat')
                            <span style="color: red;">Terlambat</span>
                        @else
                            <span style="color: green;">Dikembalikan</span>
                        @endif
                    </td>
                    <td>Rp {{ number_format($item->denda ?? 0, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total">
            Total Data: {{ $total }} Peminjaman<br>
            Total Denda: Rp {{ number_format($totalDenda, 0, ',', '.') }}
        </div>

        <!-- Footer Tanda Tangan -->
        <div class="footer">
            <div class="ttd-wrapper">
                <div class="ttd-left">
                    <div class="ttd-title">Mengetahui,</div>
                    <div class="ttd-title">Kepala Sekolah,</div>
                    <div class="ttd-space"></div>
                    <div class="ttd-name">Drs. Khairullah, M.Pd</div>
                    <div class="ttd-nip">NIP. 196906251994031011</div>
                    <div class="ttd-stamp">( stempel basah )</div>
                </div>
                
                <div class="ttd-right">
                    <div class="ttd-title">Tambang, {{ now()->translatedFormat('d F Y') }}</div>
                    <div class="ttd-title">Kepala Perpustakaan,</div>
                    <div class="ttd-space"></div>
                    <div class="ttd-name">Hj. Herlina</div>
                    <div class="ttd-nip">NIP. -</div>
                    <div class="ttd-stamp">( stempel basah )</div>
                </div>
            </div>
        </div>

        <div class="info-cetak">
            <p>
                Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} | 
                Dicetak oleh: {{ auth()->user()->name }} (Pimpinan) |
                Laporan ini adalah dokumen resmi Perpustakaan SMAN 1 Tambang
            </p>
        </div>
    </div>
</body>
</html>