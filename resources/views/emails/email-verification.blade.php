<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - Perpustakaan SMAN 1 Tambang</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 2px solid #3b82f6;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #1e40af;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            color: #6b7280;
            margin: 5px 0 0;
        }
        .content {
            padding: 20px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .button:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
        }
        .info-box {
            background-color: #f3f4f6;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            font-size: 14px;
        }
        .warning {
            color: #d97706;
            font-size: 13px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📚 Perpustakaan SMAN 1 Tambang</h1>
            <p>Verifikasi Alamat Email</p>
        </div>
        
        <div class="content">
            <h2>Halo, {{ $user->name }}! 👋</h2>
            
            <p>Terima kasih telah mendaftar menjadi anggota Perpustakaan SMAN 1 Tambang.</p>
            
            <p>Untuk melanjutkan proses pendaftaran dan verifikasi oleh petugas, silakan verifikasi alamat email Anda terlebih dahulu dengan mengklik tombol di bawah ini:</p>
            
            <div style="text-align: center;">
                <a href="{{ $verificationUrl }}" class="button">✅ Verifikasi Email Saya</a>
            </div>
            
            <div class="info-box">
                <strong>📝 Informasi Penting:</strong>
                <ul style="margin: 10px 0 0 20px; padding: 0;">
                    <li>Setelah verifikasi email, petugas akan memverifikasi data Anda</li>
                    <li>Proses verifikasi oleh petugas maksimal 1x24 jam</li>
                    <li>Password default menggunakan NIK yang Anda daftarkan</li>
                    <li>Anda akan menerima email konfirmasi setelah disetujui</li>
                </ul>
            </div>
            
            <p>Jika tombol di atas tidak berfungsi, silakan salin dan tempel link berikut ke browser Anda:</p>
            <p style="background-color: #f3f4f6; padding: 10px; border-radius: 5px; word-break: break-all; font-size: 12px;">
                {{ $verificationUrl }}
            </p>
            
            <p class="warning">
                ⚠️ Link verifikasi ini akan kadaluarsa dalam 24 jam. Jika Anda tidak melakukan verifikasi dalam waktu tersebut, silakan daftar ulang.
            </p>
            
            <p>Jika Anda tidak merasa mendaftar di Perpustakaan SMAN 1 Tambang, abaikan email ini.</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Perpustakaan SMAN 1 Tambang. All rights reserved.</p>
            <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
        </div>
    </div>
</body>
</html>