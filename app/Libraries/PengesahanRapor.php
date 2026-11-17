<?php

namespace App\Libraries;

use CodeIgniter\I18n\Time;

final class PengesahanRapor
{
    public const KEPALA = [
        'jabatan_kepala_cetak' => ['Jabatan kepala madrasah', 'string'],
        'nama_kepala_madrasah' => ['Nama kepala madrasah', 'string'],
        'nip_kepala_madrasah' => ['NIP kepala madrasah', 'string'],
        'tempat_tanggal_cetak_asli' => ['Tempat dan tanggal terbit', 'string'],
    ];

    public const WALI = [
        'jabatan_cetak' => ['Jabatan penandatangan', 'string'],
        'nama_penandatangan' => ['Nama wali kelas / pembimbing akademik', 'string'],
        'nip_penandatangan' => ['NIP wali kelas / pembimbing akademik', 'string'],
    ];

    public static function koreksi(array $rapor): array
    {
        return array_intersect_key(json_decode($rapor['pengesahan_arsip'] ?? '{}', true, 512, JSON_THROW_ON_ERROR), self::KEPALA);
    }

    public static function baca(array $rapor, ?array $rujukan): array
    {
        $asli = (string) ($rujukan['tempat_tanggal_cetak_asli'] ?? '');
        if ($asli === '') {
            $tanggal = $rujukan['tanggal_terbit'] ?? null;
            $asli = implode(', ', array_filter([
                $rujukan['tempat_terbit'] ?? null,
                $tanggal === null ? null : Time::parse($tanggal, 'Asia/Jakarta', 'id_ID')->toLocalizedString('d MMMM y'),
            ]));
        }

        $dasar = array_fill_keys(array_keys(self::KEPALA + self::WALI), null);

        return self::koreksi($rapor) + ['tempat_tanggal_cetak_asli' => $asli === '' ? null : $asli]
            + array_intersect_key($rujukan ?? [], self::KEPALA)
            + array_intersect_key($rapor, self::WALI) + $dasar;
    }
}
