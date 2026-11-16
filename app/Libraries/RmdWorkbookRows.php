<?php

namespace App\Libraries;

use Generator;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use RuntimeException;
use SimpleXMLElement;
use XMLReader;

final class RmdWorkbookRows
{
    private const SPREADSHEET_NS = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';

    private array $sheets = [];
    private array $sharedStrings = [];

    public function __construct(private readonly string $file)
    {
        $archive = new \ZipArchive();
        if (! is_file($file) || $archive->open($file, \ZipArchive::RDONLY) !== true) {
            throw new RuntimeException('Workbook XLSX tidak dapat dibuka.');
        }

        try {
            $workbook = $this->xml($archive, 'xl/workbook.xml');
            $relationships = $this->xml($archive, 'xl/_rels/workbook.xml.rels');
            $targets = [];
            foreach ($relationships->Relationship as $relationship) {
                $target = ltrim((string) $relationship['Target'], '/');
                if ((string) $relationship['TargetMode'] !== 'External') {
                    $targets[(string) $relationship['Id']] = str_starts_with($target, 'xl/') ? $target : 'xl/' . $target;
                }
            }

            foreach ($workbook->children(self::SPREADSHEET_NS)->sheets->sheet as $sheet) {
                $relation = $sheet->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships');
                $name = (string) $sheet->attributes()['name'];
                $target = $targets[(string) $relation['id']] ?? '';
                if (preg_match('~^xl/worksheets/[^/]+\.xml$~D', $target) === 1) {
                    $this->sheets[$name] = $target;
                }
            }

            if ($archive->locateName('xl/sharedStrings.xml') !== false) {
                foreach ($this->xml($archive, 'xl/sharedStrings.xml')->children(self::SPREADSHEET_NS)->si as $item) {
                    $this->sharedStrings[] = self::richText($item);
                }
            }
        } finally {
            $archive->close();
        }
    }

    public function angkatan(): int
    {
        $reader = new Xlsx();
        $reader->setReadDataOnly(true)->setLoadSheetsOnly('Pengaturan');
        $workbook = $reader->load($this->file);

        try {
            $sheet = $workbook->getSheetByName('Pengaturan');
            $year = (string) $sheet?->getCell('A5')->getValue();
            if ($sheet?->getCell('A4')->getValue() !== 'Angkatan' || preg_match('/^20[0-9]{2}$/D', $year) !== 1) {
                throw new RuntimeException('Sheet Pengaturan tidak memuat tahun angkatan yang valid.');
            }

            return (int) $year;
        } finally {
            $workbook->disconnectWorksheets();
        }
    }

    public function read(string $sheet, string $key, array $requiredColumns): Generator
    {
        $entry = $this->sheets[$sheet] ?? null;
        $file = str_replace(DIRECTORY_SEPARATOR, '/', $this->file);
        $reader = new XMLReader();
        if ($entry === null || ! $reader->open("zip://{$file}#{$entry}", null, LIBXML_NONET)) {
            throw new RuntimeException("Sheet {$sheet} tidak dapat dibuka.");
        }

        try {
            $header = [];
            while ($reader->read()) {
                if ($reader->nodeType !== XMLReader::ELEMENT
                    || $reader->localName !== 'row' || $reader->namespaceURI !== self::SPREADSHEET_NS) {
                    continue;
                }

                $rowNumber = (int) $reader->getAttribute('r');
                if ($rowNumber < 4) {
                    continue;
                }

                try {
                    $values = $this->cells(new SimpleXMLElement($reader->readOuterXml(), LIBXML_NONET));
                } catch (RuntimeException $error) {
                    throw new RuntimeException("Sheet {$sheet}, baris {$rowNumber}: " . $error->getMessage(), 0, $error);
                }
                if ($rowNumber === 4) {
                    $header = $values;
                    if (array_diff($requiredColumns, $header) !== [] || count($header) !== count(array_unique($header))) {
                        throw new RuntimeException("Judul kolom sheet {$sheet} tidak sesuai.");
                    }
                    continue;
                }

                if ($header === []) {
                    break;
                }

                $row = [];
                foreach (array_intersect_key($values, $header) as $column => $value) {
                    $row[$header[$column]] = $value;
                }
                if ($row === []) {
                    continue;
                }
                if (! isset($row[$key])) {
                    throw new RuntimeException("Sheet {$sheet}, baris {$rowNumber}: {$key} kosong.");
                }

                yield $rowNumber => $row;
            }

            if ($header === []) {
                throw new RuntimeException("Judul kolom sheet {$sheet} tidak ditemukan.");
            }
        } finally {
            $reader->close();
        }
    }

    private function cells(SimpleXMLElement $row): array
    {
        $values = [];
        foreach ($row->children(self::SPREADSHEET_NS)->c as $cell) {
            $attributes = $cell->attributes();
            $column = rtrim((string) $attributes['r'], '0123456789');
            $content = $cell->children(self::SPREADSHEET_NS);
            $value = match ((string) $attributes['t']) {
                's' => $this->sharedStrings[(int) $content->v] ?? throw new RuntimeException("Indeks teks di kolom {$column} tidak valid."),
                'e' => throw new RuntimeException("Sel di kolom {$column} berisi galat Excel."),
                'inlineStr' => isset($content->is) ? self::richText($content->is) : null,
                default => isset($content->v) ? (string) $content->v : null,
            };
            if ($value !== null && trim($value) !== '') {
                $values[$column] = $value;
            }
        }

        return $values;
    }

    private static function richText(SimpleXMLElement $item): string
    {
        $content = $item->children(self::SPREADSHEET_NS);
        if (isset($content->t)) {
            return (string) $content->t;
        }

        $text = '';
        foreach ($content->r as $run) {
            $text .= (string) $run->children(self::SPREADSHEET_NS)->t;
        }

        return $text;
    }

    private function xml(\ZipArchive $archive, string $name): SimpleXMLElement
    {
        $contents = $archive->getFromName($name);
        if ($contents === false) {
            throw new RuntimeException("Komponen XLSX {$name} tidak ditemukan.");
        }

        return new SimpleXMLElement($contents, LIBXML_NONET);
    }
}
