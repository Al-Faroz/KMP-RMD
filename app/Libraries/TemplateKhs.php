<?php

namespace App\Libraries;

use PhpOffice\PhpWord\TemplateProcessor;
use RuntimeException;

final class TemplateKhs extends TemplateProcessor
{
    public function isiMapel(string $tabel, array $baris): void
    {
        // The approved DOCX supplies the three row styles. Keep the archive's row order.
        $blok = [];
        foreach (['kelompok', 'subkelompok', 'mapel'] as $jenis) {
            $macro = $tabel . '_' . $jenis;
            $letak = $this->findContainingXmlBlockForMacro($macro, 'w:tr');
            if ($letak === false) {
                throw new RuntimeException('Struktur tabel pada template rapor KHS belum lengkap.');
            }
            $blok[$jenis] = $this->getSlice($letak['start'], $letak['end']);
        }

        $hasil = '';
        foreach ($baris as [$jenis, $data]) {
            $nilai = [];
            foreach ($data as $field => $isi) {
                $teks = htmlspecialchars((string) $isi, ENT_XML1 | ENT_QUOTES | ENT_SUBSTITUTE | ENT_DISALLOWED, 'UTF-8');
                $nilai['${' . $tabel . '_' . $field . '}'] = str_replace(
                    "\n", '</w:t><w:br/><w:t xml:space="preserve">', $teks
                );
            }
            $hasil .= strtr($blok[$jenis], $nilai);
        }

        $this->replaceXmlBlock($tabel . '_kelompok', '', 'w:tr');
        $this->replaceXmlBlock($tabel . '_subkelompok', '', 'w:tr');
        $this->replaceXmlBlock($tabel . '_mapel', $hasil, 'w:tr');
    }
}
