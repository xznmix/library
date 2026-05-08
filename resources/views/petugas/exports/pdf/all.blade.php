<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Lengkap Perpustakaan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.4;
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
            color: rgba(79,70,229,0.08);
            white-space: nowrap;
            z-index: 0;
            font-family: Arial, sans-serif;
            text-transform: uppercase;
            letter-spacing: 10px;
            border: 5px double rgba(79,70,229,0.08);
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

        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        
        .section-title {
            background: #4F46E5;
            color: white;
            padding: 8px;
            margin: 15px 0 10px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 5px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            border: 1px solid #e5e7eb;
        }
        
        .stat-card .value {
            font-size: 20px;
            font-weight: bold;
            color: #4F46E5;
        }
        
        .stat-card .label {
            font-size: 10px;
            color: #6b7280;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        
        table th {
            background: #f3f4f6;
            padding: 8px;
            font-size: 10px;
            text-align: left;
            border: 1px solid #d1d5db;
        }
        
        table td {
            padding: 6px;
            border: 1px solid #e5e7eb;
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
            .section-title {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                background-color: #4F46E5 !important;
            }
            table th {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                background-color: #f3f4f6 !important;
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
            <h3>LAPORAN LENGKAP PERPUSTAKAAN</h3>
            <p>Nomor: 421.3/{{ date('m/Y') }}/SMA.01/LAP-LENGKAP</p>
        </div>

        <div class="info-tanggal">
            <p>Tambang, {{ now()->translatedFormat('d F Y') }}</p>
        </div>

        <!-- Ringkasan Statistik -->
        <div class="section">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="value">{{ number_format($totalPinjam, 0, ',', '.') }}</div>
                    <div class="label">Total Peminjaman</div>
                </div>
                <div class="stat-card">
                    <div class="value">{{ number_format($totalAnggota, 0, ',', '.') }}</div>
                    <div class="label">Total Anggota</div>
                </div>
                <div class="stat-card">
                    <div class="value">{{ number_format($totalBuku, 0, ',', '.') }}</div>
                    <div class="label">Total Buku</div>
                </div>
                <div class="stat-card">
                    <div class="value">{{ number_format($totalKunjungan, 0, ',', '.') }}</div>
                    <div class="label">Total Kunjungan</div>
                </div>
                <div class="stat-card">
                    <div class="value">Rp {{ number_format($totalDenda, 0, ',', '.') }}</div>
                    <div class="label">Total Denda</div>
                </div>
            </div>
        </div>

        <!-- Peminjaman Terbaru -->
        <div class="section">
            <div class="section-title">PEMINJAMAN TERBARU (10 Transaksi Terakhir)</div>
            <table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">Tgl Pinjam</th>
                        <th width="20%">Anggota</th>
                        <th width="25%">Buku</th>
                        <th width="15%">Status</th>
                        <th width="20%">Denda</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($peminjamanTerbaru as $index => $item)
                    @php
                        $dendaValue = $item->denda_total ?? 0;
                        if ($dendaValue == 0 && $item->extra_attributes) {
                            $dendaValue = ($item->extra_attributes['denda_terlambat'] ?? 0) + ($item->extra_attributes['denda_rusak'] ?? 0);
                        }
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}</td>
                        <td>{{ $item->user->name ?? '-' }}<br><small>{{ $item->user->no_anggota ?? '-' }}</small></td>
                        <td>{{ $item->buku->judul ?? '-' }}<br><small>{{ $item->buku->pengarang ?? '-' }}</small></td>
                        <td class="text-center">
                            @if($item->status_pinjam == 'dipinjam')
                                <span style="color: #F59E0B;">Dipinjam</span>
                            @elseif($item->status_pinjam == 'terlambat')
                                <span style="color: #EF4444;">Terlambat</span>
                            @else
                                <span style="color: #10B981;">Dikembalikan</span>
                            @endif
                        </td>
                        <td class="text-right">
                            @if($dendaValue > 0)
                                Rp {{ number_format($dendaValue, 0, ',', '.') }}
                            @else
                                Rp 0
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Buku Populer -->
        <div class="section">
            <div class="section-title">BUKU PALING POPULER (10 Teratas)</div>
            <table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="35%">Judul Buku</th>
                        <th width="25%">Pengarang</th>
                        <th width="15%">Kategori</th>
                        <th width="10%">Dipinjam</th>
                        <th width="10%">Stok</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bukuPopuler as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item->judul }}</td>
                        <td>{{ $item->pengarang ?? '-' }}</td>
                        <td>{{ $item->kategori->nama ?? '-' }}</td>
                        <td class="text-center">{{ $item->peminjaman_count }} kali</span></td>
                        <td class="text-center">{{ $item->stok }} eksemplar</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Anggota Aktif -->
        <div class="section">
            <div class="section-title">ANGGOTA PALING AKTIF (10 Teratas)</div>
            <table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="30%">Nama Anggota</th>
                        <th width="15%">Jenis</th>
                        <th width="20%">No. Anggota</th>
                        <th width="10%">Total Pinjam</th>
                        <th width="10%">Status</th>
                    <tr>
                </thead>
                <tbody>
                    @foreach($anggotaAktif as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item->name }}</td>
                        <td class="text-center">{{ ucfirst($item->jenis ?? 'Umum') }}</td>
                        <td class="text-center">{{ $item->no_anggota ?? '-' }}</td>
                        <td class="text-center">{{ $item->peminjaman_count }} kali</span></td>
                        <td class="text-center">
                            @if($item->status_anggota == 'active')
                                <span style="color: #059669;">Aktif</span>
                            @elseif($item->status_anggota == 'pending')
                                <span style="color: #B45309;">Pending</span>
                            @else
                                <span style="color: #6B7280;">Nonaktif</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
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

        <!-- Informasi Cetak -->
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