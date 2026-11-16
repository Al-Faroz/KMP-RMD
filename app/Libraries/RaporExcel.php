<?php

namespace App\Libraries;

use DomainException;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use ZipArchive;

final class RaporExcel
{
    private const MAX_ROWS = 151;

    public function unduh(array $siswa, int $semester, array $snapshot, ?string $kodeFormat): string
    {
        $book = new Spreadsheet();
        $petunjuk = $book->getActiveSheet()->setTitle('Identitas');
        $petunjuk->fromArray([
            ['RMD-RAPOR-1', null],
            ['ID siswa', (string) $siswa['id']],
            ['Semester', (string) $semester],
            ['Nama siswa', $siswa['nama']],
            ['Angkatan', $siswa['tahun_masuk']],
            ['Petunjuk', 'Pertahankan ID siswa, semester, judul kolom, dan ID baris yang sudah ada.'],
            ['Perubahan', 'Sel kosong dilewati. Angka 0 disimpan. Tambah baris baru dengan ID baris kosong.'],
            ['ID siswa sumber', null],
            ['ID rapor sumber', null],
        ]);
        $petunjuk->setCellValueExplicit('B4', $siswa['nama'], DataType::TYPE_STRING);
        $petunjuk->setCellValueExplicit('B8', $siswa['id_sumber'], DataType::TYPE_STRING);
        $petunjuk->setCellValueExplicit('B9', $snapshot['rapor']['id_sumber'] ?? '', DataType::TYPE_STRING);
        $petunjuk->getColumnDimension('A')->setWidth(18);
        $petunjuk->getColumnDimension('B')->setWidth(88);

        foreach (RaporFields::LABELS as $bagian => $judul) {
            $sheet = $book->createSheet()->setTitle($judul);
            $definitions = RaporFields::definitions($bagian);
            if ($bagian === 'rapor') {
                $rows = [($snapshot['rapor'] ?? []) + ['kode_format' => $kodeFormat]];
                if ($rows[0]['kode_format'] === null) {
                    $rows[0]['kode_format'] = $kodeFormat;
                }
            } else {
                $definitions = ['id' => ['ID baris', 'string']] + $definitions;
                $rows = $snapshot[$bagian];
            }
            $this->isiSheet($sheet, $definitions, $rows);
        }

        $acuan = $book->createSheet()->setTitle('Acuan Cetak');
        $acuan->fromArray([['Kode format', 'Nama format'], ...array_map(
            static fn (array $item): array => [$item['id_sumber'], $item['nama_format']],
            array_values($snapshot['format'])
        )]);
        $baris = count($snapshot['format']) + 4;
        $acuan->fromArray([['ID pengaturan', 'Kode format', 'Tahun pelajaran', 'Rombel'], ...array_map(
            static fn (array $item): array => [$item['id'], $item['kode_format'], $item['tahun_pelajaran'], $item['rombel']],
            array_values($snapshot['pengaturan'])
        )], null, 'A' . $baris);
        foreach (['A', 'B', 'C', 'D'] as $kolom) {
            $acuan->getColumnDimension($kolom)->setWidth(28);
        }

        ob_start();
        try {
            (new XlsxWriter($book))->save('php://output');

            return (string) ob_get_contents();
        } finally {
            ob_end_clean();
            $book->disconnectWorksheets();
        }
    }

    public function baca(string $path, array $siswa, int $semester, ?array $rapor): array
    {
        $this->periksaUkuranArsip($path);
        $reader = new XlsxReader();
        $reader->setReadDataOnly(true)->setReadEmptyCells(false);
        $sheets = ['Identitas', ...array_values(RaporFields::LABELS)];
        $reader->setLoadSheetsOnly($sheets);
        $book = $reader->load($path);
        try {
            $identitas = $book->getSheetByName('Identitas');
            if ($identitas === null || $this->nilaiSel($identitas, 'A1') !== 'RMD-RAPOR-1'
                || (string) $this->nilaiSel($identitas, 'B2') !== (string) $siswa['id']
                || (string) $this->nilaiSel($identitas, 'B3') !== (string) $semester
                || (string) $this->nilaiSel($identitas, 'B5') !== (string) $siswa['tahun_masuk']
                || (string) $this->nilaiSel($identitas, 'B8') !== $siswa['id_sumber']
                || (string) $this->nilaiSel($identitas, 'B9') !== ($rapor['id_sumber'] ?? '')) {
                throw new DomainException('File bukan template untuk siswa dan semester ini.');
            }

            $hasil = [];
            foreach (RaporFields::LABELS as $bagian => $judul) {
                $sheet = $book->getSheetByName($judul);
                if ($sheet === null) {
                    throw new DomainException("Sheet {$judul} tidak ditemukan.");
                }
                $definitions = RaporFields::definitions($bagian);
                if ($bagian !== 'rapor') {
                    $definitions = ['id' => ['ID baris', 'string']] + $definitions;
                }
                $rows = $this->bacaSheet($sheet, $definitions, $bagian === 'rapor' ? 2 : self::MAX_ROWS);
                $hasil[$bagian] = $bagian === 'rapor' ? ($rows[0] ?? []) : $rows;
            }

            return $hasil;
        } finally {
            $book->disconnectWorksheets();
        }
    }

    private function isiSheet(Worksheet $sheet, array $definitions, array $rows): void
    {
        foreach (array_keys($definitions) as $index => $field) {
            [$label, $type] = $definitions[$field];
            $column = Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($column . '1', $label);
            $sheet->getColumnDimension($column)->setWidth($type === 'text' ? 48 : 23);
            foreach ($rows as $row => $values) {
                if (isset($values[$field])) {
                    $value = (string) $values[$field];
                    if ($type === 'number') {
                        $value = RaporFields::number($value);
                    }
                    $sheet->setCellValueExplicit($column . ($row + 2), $value, DataType::TYPE_STRING);
                }
            }
        }
        $last = $sheet->getHighestColumn();
        $sheet->getStyle('A1:' . $last . '1')->getFont()->setBold(true);
        $sheet->freezePane('A2');
    }

    private function bacaSheet(Worksheet $sheet, array $definitions, int $limit): array
    {
        $judul = $sheet->getTitle();
        $columns = array_keys($definitions);
        if ($sheet->getHighestDataRow() > $limit
            || Coordinate::columnIndexFromString($sheet->getHighestDataColumn()) > count($columns)) {
            throw new DomainException("Sheet {$judul} melebihi jumlah baris atau kolom pada template.");
        }
        foreach ($columns as $index => $field) {
            $address = Coordinate::stringFromColumnIndex($index + 1) . '1';
            if ($this->nilaiSel($sheet, $address) !== $definitions[$field][0]) {
                throw new DomainException("Judul kolom {$address} pada sheet {$judul} berubah.");
            }
        }

        $rows = [];
        for ($row = 2; $row <= $sheet->getHighestDataRow(); $row++) {
            $values = [];
            foreach ($columns as $index => $field) {
                $address = Coordinate::stringFromColumnIndex($index + 1) . $row;
                $values[$field] = $this->nilaiSel($sheet, $address);
            }
            if (array_filter($values, static fn ($value) => $value !== null && $value !== '') !== []) {
                $rows[] = $values;
            }
        }

        return $rows;
    }

    private function nilaiSel(Worksheet $sheet, string $address): mixed
    {
        $cell = $sheet->getCell($address);
        if (in_array($cell->getDataType(), [DataType::TYPE_FORMULA, DataType::TYPE_ERROR], true)) {
            throw new DomainException($sheet->getTitle() . " {$address}: masukkan nilai sel langsung.");
        }

        return $cell->getValue();
    }

    private function periksaUkuranArsip(string $path): void
    {
        $archive = new ZipArchive();
        if ($archive->open($path, ZipArchive::RDONLY) !== true) {
            throw new DomainException('File XLSX tidak dapat dibuka.');
        }
        try {
            $size = 0;
            for ($i = 0; $i < $archive->numFiles; $i++) {
                $size += $archive->statIndex($i)['size'];
                if ($size > 12 * 1024 * 1024) {
                    throw new DomainException('Isi workbook melebihi batas untuk satu rapor.');
                }
            }
        } finally {
            $archive->close();
        }
    }
}
