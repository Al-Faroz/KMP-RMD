<?php

namespace App\Controllers;

class Operator extends BaseController
{
    protected $helpers = ['url'];

    public function siswa(): string
    {
        return view('operator/siswa', [
            'pageTitle' => 'Data Siswa',
            'activeMenu' => 'data-siswa',
        ]);
    }

    public function cetakRombel(): string
    {
        return view('operator/cetak_rombel', [
            'pageTitle' => 'Cetak Rombel',
            'activeMenu' => 'cetak-rombel',
        ]);
    }
}
