<?php

namespace App\Libraries;

use DomainException;

final class RaporFields
{
    public const LABELS = [
        'rapor' => 'Rapor Semester',
        'nilai' => 'Nilai Mapel',
        'pendukung' => 'Data Pendukung',
    ];
    public const TABLES = ['nilai' => 'nilai_mapel', 'pendukung' => 'data_pendukung'];
    public const REQUIRED = ['nilai' => 'nama_mapel_asli', 'pendukung' => 'jenis_item'];

    public static function definitions(string $section): array
    {
        $fields = [];
        if ($section === 'rapor') {
            $fields = [
                'kode_format' => ['Kode format', 'string'],
                'pengaturan_cetak_id' => ['ID pengaturan cetak', 'integer'],
            ];
        }
        foreach (RmdWorkbookSchema::SHEETS[self::LABELS[$section]]['fields'] as $label => $type) {
            if (in_array($label, ['Semester', 'StatusArsip', 'NamaTercetak', 'NISTercetak', 'NISNTercetak'], true)) {
                continue;
            }
            $name = RmdWorkbookSchema::column($label);
            $title = ucfirst(str_replace('_', ' ', $name));
            $fields[$name] = [strtr($title, [' sks' => ' SKS', ' jp' => ' JP', 'Kbm' => 'KBM', 'Kkm' => 'KKM', 'Kktp' => 'KKTP', 'Nip ' => 'NIP ', 'Ip ' => 'IP ', ' ard' => ' ARD']), $type];
        }

        return $fields;
    }

    public static function number(string $value): string
    {
        if (str_contains($value, '.')) {
            $value = rtrim(rtrim($value, '0'), '.');
        }

        return $value === '-0' ? '0' : $value;
    }

    public static function value(mixed $raw, string $type): ?string
    {
        if ($raw === null || ((is_string($raw) || is_int($raw) || is_float($raw)) && trim((string) $raw) === '')) {
            return null;
        }
        if (! is_string($raw) && ! is_int($raw) && ! is_float($raw)) {
            throw new DomainException('Gunakan teks atau angka.');
        }

        $value = trim((string) $raw);
        if ($type === 'integer') {
            if (! preg_match('/^-?[0-9]{1,10}$/D', $value) || (float) $value < -2147483648 || (float) $value > 2147483647) {
                throw new DomainException('Gunakan bilangan bulat yang valid.');
            }

            return (string) (int) $value;
        }
        if ($type === 'number') {
            $value = str_replace(',', '.', $value);
            if (! preg_match('/^-?[0-9]{1,13}(?:\.[0-9]{1,15})?$/D', $value)) {
                throw new DomainException('Gunakan angka dengan paling banyak 15 tempat desimal.');
            }

            $negative = str_starts_with($value, '-');
            $parts = explode('.', ltrim($value, '-'), 2);
            $whole = ltrim($parts[0], '0') ?: '0';
            $normalized = self::number($whole . (isset($parts[1]) ? '.' . $parts[1] : ''));

            return $negative && $normalized !== '0' ? '-' . $normalized : $normalized;
        }
        if (mb_strlen($value) > ($type === 'text' ? 16000 : 255)) {
            throw new DomainException('Isian terlalu panjang.');
        }

        return $value;
    }
}
