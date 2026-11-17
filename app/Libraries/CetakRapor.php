<?php

namespace App\Libraries;

use App\Models\RaporSemesterModel;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

final class CetakRapor
{
    private const FORMAT = [
        'RDM_K13' => [
            'judul' => 'CAPAIAN HASIL BELAJAR',
            'nilai' => [
                'JP' => 'jp',
                'Pengetahuan' => ['Nilai' => 'nilai_pengetahuan', 'Predikat' => 'predikat_pengetahuan'],
                'Keterampilan' => ['Nilai' => 'nilai_keterampilan', 'Predikat' => 'predikat_keterampilan'],
                'Rata-rata' => 'rata_rata_ard', 'Beban × Nilai' => 'bebanx_n',
            ],
        ],
        'RDM_PDF_2023_VIIA' => [
            'judul' => 'CAPAIAN HASIL BELAJAR',
            'nilai' => [
                'JP' => 'jp', 'Nilai Akhir' => 'nilai_akhir', 'JP × Nilai' => 'j_px_ni',
                'Capaian Kompetensi' => 'capaian_kompetensi',
            ],
        ],
    ];

    private const RINGKASAN = [
        'Total SKS' => 'total_sks',
        'Total beban ARD' => 'total_beban_ard',
        'Total JP' => 'total_jp',
        'Total Nilai Pengetahuan' => 'total_nilai_pengetahuan',
        'Total Nilai Keterampilan' => 'total_nilai_keterampilan',
        'Total SKS × N1' => 'total_sk_sx_n1',
        'Total SKS × N2' => 'total_sk_sx_n2',
        'Total Beban × Nilai' => 'total_bebanx_n',
        'Total Nilai Akhir' => 'total_nilai_akhir',
        'Total JP × Nilai' => 'total_j_px_ni',
        'IP Semester' => 'ip_semester',
        'SKS Berikutnya' => 'sks_berikutnya',
        'Peringkat' => 'peringkat',
    ];

    public function mendukung(?string $kode): bool
    {
        return CetakKhsWord::mendukung($kode) || isset(self::FORMAT[$kode]);
    }

    public function respons(ResponseInterface $response, string $nama, string $judul, array $daftar, RaporSemesterModel $model): ResponseInterface
    {
        try {
            $pdf = $this->buat($judul, $daftar, $model);
        } catch (GagalCetakKhs $error) {
            log_message('error', 'Cetak KHS gagal: {pesan}', ['pesan' => $error->getMessage()]);

            return $response->setStatusCode(503)->setContentType('text/plain')
                ->setHeader('Cache-Control', 'private, no-store')->setBody($error->getMessage());
        }

        return $response->setContentType('application/pdf')->setHeader('Cache-Control', 'private, no-store')
            ->setHeader('Content-Disposition', 'inline; filename="' . $nama . '"')->setBody($pdf);
    }

    public function buat(string $judul, array $daftar, RaporSemesterModel $model): string
    {
        $konverter = new KonverterLibreOffice();
        $word = new CetakKhsWord();
        $folder = null;
        $dokumen = $bagian = [];
        $pengaturan = $rentang = [];
        try {
            foreach ($daftar as $rapor) {
                $id = (string) $rapor['id'];
                $idPengaturan = $rapor['pengaturan_cetak_id'];
                if ($idPengaturan !== null && ! array_key_exists($idPengaturan, $pengaturan)) {
                    $pengaturan[$idPengaturan] = $model->pengaturanCetak($idPengaturan);
                    $rentang[$idPengaturan] = $model->rentangPredikat($idPengaturan);
                }

                $data = $this->data($rapor, $model->nilai($id), $model->dataPendukung($id),
                    $pengaturan[$idPengaturan] ?? null, $rentang[$idPengaturan] ?? []);
                if (! CetakKhsWord::mendukung($rapor['kode_format'])) {
                    $bagian[] = $data;
                    continue;
                }

                $folder ??= $konverter->folderSementara();
                $dasar = 'rapor-' . count($dokumen);
                try {
                    $word->simpan($data, $folder . '/' . $dasar . '.docx');
                } catch (Throwable $error) {
                    throw new GagalCetakKhs('Template Word KHS gagal diisi. Periksa paket aplikasi dan data rapor.', 0, $error);
                }
                $dokumen[] = $dasar . '.docx';
                $bagian[] = $folder . '/' . $dasar . '.pdf';
            }
            if ($folder !== null) {
                $konverter->konversi($folder, $dokumen);
            }

            return (new PdfRmd())->gabungkan('rapor', $judul, $bagian);
        } finally {
            if ($folder !== null) {
                $konverter->bersihkan($folder);
            }
        }
    }

    private function data(array $rapor, array $mapel, array $pendukung, ?array $pengaturan, array $rentang): array
    {
        $kode = $rapor['kode_format'];
        $format = self::FORMAT[$kode] ?? [];
        $kolom = $format['nilai'] ?? [];
        if ($kode === 'RDM_K13') {
            foreach (['Rata-rata', 'Beban × Nilai'] as $label) {
                if (array_filter(array_column($mapel, $kolom[$label]), static fn ($nilai): bool => $nilai !== null) === []) {
                    unset($kolom[$label]);
                }
            }
        }

        $ringkasan = [];
        foreach (self::RINGKASAN as $label => $field) {
            if ($rapor[$field] !== null) {
                $ringkasan[$label] = $rapor[$field];
            }
        }

        $batas = [];
        foreach (['KBM', 'KKM', 'KKTP'] as $label) {
            $field = strtolower($label);
            $nilai = $rapor[$field] ?? $pengaturan[$field] ?? null;
            if ($nilai !== null) {
                $batas[$label] = $nilai;
            }
        }

        $dataPendukung = [];
        foreach ($pendukung as $item) {
            $dataPendukung[mb_strtolower(trim($item['jenis_item']))][] = $item;
        }

        $pengesahan = PengesahanRapor::baca($rapor, $pengaturan);

        return [
            'siswa' => ['nama' => $rapor['nama'], 'nis' => $rapor['nis'], 'nisn' => $rapor['nisn']],
            'rapor' => $rapor,
            'mapel' => $mapel,
            'judul' => $format['judul'] ?? '',
            'kolom' => $kolom,
            'ringkasan' => $ringkasan,
            'batas' => $batas,
            'pendukung' => $dataPendukung,
            'rentang' => $rentang,
            'pengaturan' => $pengaturan,
            'pengesahan' => $pengesahan,
            'merdeka' => $kode === 'RDM_PDF_2023_VIIA',
            'deskripsiTerpisah' => $kode === 'RDM_K13',
        ];
    }
}
