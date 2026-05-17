<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Denda Perpustakaan</title>
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

        .ringkasan {
            margin-bottom: 25px;
            padding: 15px;
            border: 1px solid #1F2937;
            background-color: #F3F4F6;
        }

        .ringkasan h4 {
            font-size: 13pt;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
            text-transform: uppercase;
            color: #F97316;
        }

        .grid-ringkasan {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }

        .item-ringkasan {
            padding: 8px;
            border-bottom: 1px dotted #1F2937;
        }

        .item-ringkasan .label {
            font-weight: bold;
        }

        .tabel-container {
            margin: 25px 0;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #1F2937;
            font-size: 11pt;
        }

        th {
            background-color: #DBEAFE;
            font-weight: bold;
            text-align: center;
            padding: 10px 5px;
            border: 1px solid #1F2937;
            text-transform: uppercase;
            font-size: 10pt;
            color: #1F2937;
        }

        td {
            padding: 8px 5px;
            border: 1px solid #1F2937;
            vertical-align: middle;
            color: #1F2937;
        }

        tr:nth-child(even) {
            background-color: #F9FAFB;
        }

        .status-lunas {
            font-weight: bold;
            color: #10B981;
        }

        .status-belum {
            font-weight: bold;
            color: #F97316;
        }

        .keterangan {
            margin-top: 20px;
            margin-bottom: 30px;
            font-size: 10pt;
        }

        .keterangan ul {
            margin-left: 30px;
            margin-top: 5px;
        }

        .keterangan li {
            margin-bottom: 3px;
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
        
        .ttd-left {
            flex: 1;
            text-align: center;
        }
        
        .ttd-right {
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

        .total-row {
            background-color: #F3F4F6;
            font-weight: bold;
        }

        @media print {
            body {
                padding: 20px;
            }
            
            .watermark {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .ringkasan {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                background-color: #F3F4F6 !important;
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
                    <img src="{{ public_path('images/logo.jpg') }}" alt="Logo Perpustakaan" onerror="this.style.display='none'">
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
                <div class="kop-logo"></div>
            </div>
        </div>

        <div class="garis-tiga"></div>

        <div class="judul-laporan">
            <h3>LAPORAN DENDA PERPUSTAKAAN</h3>
            <p>Nomor: 421.3/{{ date('m/Y') }}/SMA.01/LAP-DENDA</p>
        </div>

        <div class="info-tanggal">
            <p>Tambang, {{ now()->translatedFormat('d F Y') }}</p>
            <p>Periode: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p>
        </div>

        <!-- Ringkasan -->
        <div class="ringkasan">
            <h4>RINGKASAN DENDA</h4>
            <div class="grid-ringkasan">
                <div class="item-ringkasan">
                    <span class="label">Total Transaksi:</span> {{ $totalTransaksi }} Transaksi
                </div>
                <div class="item-ringkasan">
                    <span class="label">Total Denda:</span> Rp {{ number_format($totalDenda, 0, ',', '.') }}
                </div>
                <div class="item-ringkasan">
                    <span class="label">Rata-rata Denda:</span> Rp {{ number_format($totalTransaksi > 0 ? $totalDenda / $totalTransaksi : 0, 0, ',', '.') }}
                </div>
                <div class="item-ringkasan">
                    <span class="label">Status Lunas:</span> {{ $data->where('status_verifikasi', 'disetujui')->count() }} Transaksi
                </div>
            </div>
        </div>

        <!-- Tabel Denda -->
        <div class="tabel-container">
            <table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="12%">Tgl. Kembali</th>
                        <th width="15%">Nama Anggota</th>
                        <th width="25%">Judul Buku</th>
                        <th width="10%">Terlambat</th>
                        <th width="15%">Denda</th>
                        <th width="10%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $index => $item)
                    @php
                        $jatuhTempo = \Carbon\Carbon::parse($item->tgl_jatuh_tempo);
                        $kembali = \Carbon\Carbon::parse($item->tanggal_pengembalian);
                        $terlambat = $kembali->diffInDays($jatuhTempo);
                    @endphp
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td style="text-align: center;">{{ \Carbon\Carbon::parse($item->tanggal_pengembalian)->format('d/m/Y') }}</td>
                        <td>{{ $item->user->name ?? '-' }}</td>
                        <td>{{ $item->buku->judul ?? '-' }}</td>
                        <td style="text-align: center;">{{ $terlambat }} hari</td>
                        <td style="text-align: right;">Rp {{ number_format($item->denda_total, 0, ',', '.') }}</td>
                        <td style="text-align: center;">
                            @if($item->status_verifikasi == 'disetujui')
                                <span class="status-lunas">LUNAS</span>
                            @else
                                <span class="status-belum">BELUM LUNAS</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 20px;">
                            Tidak ada data denda
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($data->count() > 0)
                <tfoot>
                    <tr class="total-row">
                        <td colspan="5" style="text-align: right;"><strong>TOTAL:</strong></td>
                        <td style="text-align: right;"><strong>Rp {{ number_format($totalDenda, 0, ',', '.') }}</strong></td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        <!-- Keterangan -->
        <div class="keterangan">
            <p><strong>Keterangan:</strong></p>
            <ul>
                <li>Laporan ini dibuat berdasarkan data denda perpustakaan periode {{ $startDate->format('d/m/Y') }} s.d {{ $endDate->format('d/m/Y') }}</li>
                <li>Denda dihitung berdasarkan keterlambatan pengembalian buku dan/atau kerusakan buku</li>
                <li>Status "LUNAS" menandakan denda telah dibayarkan oleh peminjam</li>
            </ul>
        </div>

        <!-- Tanda Tangan -->
        <div class="footer">
            <div class="ttd-wrapper">
                <div class="ttd-right">
                    <div class="ttd-title">Tambang, {{ now()->translatedFormat('d F Y') }}</div>
                    <div class="ttd-title">Kepala Perpustakaan,</div>
                    <div class="ttd-space"></div>
                    <div class="ttd-name">Hj. Herlina</div>
                    <div class="ttd-nip">NIP. -</div>
                    <div class="ttd-stamp">( stempel basah )</div>
                </div>
                <div class="ttd-left">
                    <div class="ttd-title">Mengetahui,</div>
                    <div class="ttd-title">Kepala Sekolah,</div>
                    <div class="ttd-space"></div>
                    <div class="ttd-name">Drs. Khairullah, M.Pd</div>
                    <div class="ttd-nip">NIP. 196906251994031011</div>
                    <div class="ttd-stamp">( stempel basah )</div>
                </div> 
            </div>
        </div>

        <!-- Info Cetak -->
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