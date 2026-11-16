<?php

namespace App\Libraries;

use Mpdf\Mpdf;
use Mpdf\Output\Destination;

final class PdfRmd
{
    private const F4_MM = [210, 330];

    public function buat(string $dokumen, string $judul, array $data): string
    {
        $pdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => self::F4_MM,
            'default_font' => 'dejavusans',
            'margin_left' => 12,
            'margin_right' => 12,
            'margin_top' => 12,
            'margin_bottom' => 12,
            'tempDir' => WRITEPATH . 'cache/mpdf',
        ]);
        $pdf->imageVars['kopKemenag'] = file_get_contents(APPPATH . 'Assets/logo-kemenag-transkrip.jpg');
        $pdf->SetTitle($judul);
        $pdf->WriteHTML(view('cetak/' . $dokumen, $data, ['saveData' => false]));

        return $pdf->Output('', Destination::STRING_RETURN);
    }
}
