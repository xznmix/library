<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DigitalAccess;
use App\Models\BukuDigital;

class DigitalCollectionController extends Controller
{

    public function reader($token)
    {
        $akses = DigitalAccess::where('token_akses', $token)
            ->where('status', 'active')
            ->first();

        if (!$akses) {
            abort(403, 'Token tidak valid');
        }

        if (now() > $akses->tanggal_expired) {
            abort(403, 'Akses sudah expired');
        }

        $buku = $akses->buku;

        return view('digital.reader', [
            'akses' => $akses,
            'buku' => $buku
        ]);
    }

}