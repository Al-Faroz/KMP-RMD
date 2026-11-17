<?php

namespace App\Libraries;

use Mpdf\Mpdf;
use Mpdf\Output\Destination;

final class PdfRmd
{
    private const F4_MM = [210, 330];

    public function buat(string $dokumen, string $judul, array $data, bool $lanskap = false): string
    {
        return $this->gabungkan($dokumen, $judul, [$data], $lanskap);
    }

    public function gabungkan(string $dokumen, string $judul, iterable $isi, bool $lanskap = false): string
    {
        $pdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => self::F4_MM,
            'orientation' => $lanskap ? 'L' : 'P',
            'default_font' => 'dejavusans',
            'margin_left' => 12,
            'margin_right' => 12,
            'margin_top' => 12,
            'margin_bottom' => 12,
            'tempDir' => WRITEPATH . 'cache/mpdf',
        ]);
        $pdf->imageVars['kopKemenag'] = file_get_contents(ROOTPATH . 'public/assets/rmd/logo-kemenag.jpg');
        $pdf->SetTitle($judul);
        $halamanPertama = true;
        foreach ($isi as $data) {
            if (! $halamanPertama) {
                $pdf->AddPage();
            }
            $pdf->WriteHTML(view('cetak/' . $dokumen, $data, ['saveData' => false]));
            $halamanPertama = false;
        }

        return $pdf->Output('', Destination::STRING_RETURN);
    }
}
