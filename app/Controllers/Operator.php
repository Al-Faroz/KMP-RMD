<?php

namespace App\Controllers;

class Operator extends BaseController
{
    protected $helpers = ['url'];

    public function cetakRombel(): string
    {
        return view('operator/cetak_rombel', [
            'pageTitle' => 'Cetak Rombel',
            'activeMenu' => 'cetak-rombel',
        ]);
    }
}
