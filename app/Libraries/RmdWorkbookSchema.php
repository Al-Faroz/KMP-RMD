<?php

namespace App\Libraries;

final class RmdWorkbookSchema
{
    // Kolom sumber di baris keempat. Urutan tabel mengikuti relasi impor.
    public const SHEETS = [
        'Format Cetak' => [
            'table' => 'format_cetak',
            'key' => 'KodeFormatCetak',
            'fields' => [
                'NamaFormat' => 'string', 'AcuanKeluaran' => 'text', 'CakupanAcuan' => 'text',
            ],
        ],
        'Data Siswa' => [
            'table' => 'siswa',
            'key' => 'IdFinal',
            'fields' => [
                'Nama' => 'string', 'NIS' => 'string', 'NISN' => 'string',
                'TempatLahir' => 'string', 'TanggalLahir' => 'date', 'JenisKelamin' => 'string',
                'Agama' => 'string', 'StatusDalamKeluarga' => 'string', 'AnakKe' => 'integer',
                'AlamatSiswa' => 'text', 'TeleponSiswa' => 'string', 'SekolahAsal' => 'string',
                'KelasDiterima' => 'string', 'TanggalDiterima' => 'date',
                'NamaAyah' => 'string', 'NamaIbu' => 'string', 'AlamatOrangTua' => 'text',
                'TeleponOrangTua' => 'string', 'PekerjaanAyah' => 'string', 'PekerjaanIbu' => 'string',
                'NamaWali' => 'string', 'PekerjaanWali' => 'string', 'AlamatWali' => 'text',
                'TeleponWali' => 'string',
            ],
        ],
        'Pengaturan Cetak' => [
            'table' => 'pengaturan_cetak',
            'key' => 'IdPengaturanCetak',
            'fields' => [
                'TahunPelajaran' => 'string', 'Semester' => 'integer', 'Rombel' => 'string',
                'NamaMadrasah' => 'string', 'AlamatMadrasah' => 'text', 'TempatTerbit' => 'string',
                'TempatTanggalCetakAsli' => 'string', 'TanggalTerbit' => 'date',
                'JabatanKepalaCetak' => 'string', 'NamaKepalaMadrasah' => 'string',
                'NIPKepalaMadrasah' => 'string', 'KBM' => 'number', 'KKM' => 'number', 'KKTP' => 'number',
            ],
        ],
        'Rapor Semester' => [
            'table' => 'rapor_semester',
            'key' => 'IdRapor',
            'fields' => [
                'Semester' => 'integer', 'TahunPelajaran' => 'string', 'Kelas' => 'string', 'Rombel' => 'string',
                'StatusArsip' => 'string', 'Kurikulum' => 'string', 'NamaTercetak' => 'string',
                'NISTercetak' => 'string', 'NISNTercetak' => 'string', 'IPSemester' => 'number',
                'TotalSKS' => 'number', 'TotalBebanARD' => 'number', 'TotalJP' => 'number',
                'TotalNilaiAkhir' => 'number', 'TotalNilaiPengetahuan' => 'number',
                'TotalNilaiKeterampilan' => 'number', 'TotalSKSxN1' => 'number', 'TotalSKSxN2' => 'number',
                'TotalBebanxN' => 'number', 'TotalJPxNi' => 'number', 'SKSBerikutnya' => 'number',
                'Peringkat' => 'integer', 'KBM' => 'number', 'KKM' => 'number', 'KKTP' => 'number',
                'JabatanCetak' => 'string', 'NamaPenandatangan' => 'string', 'NIPPenandatangan' => 'string',
            ],
        ],
        'Nilai Mapel' => [
            'table' => 'nilai_mapel',
            'key' => 'IdNilaiMapel',
            'fields' => [
                'KelompokMapel' => 'string', 'SubkelompokMapel' => 'string', 'UrutanMapel' => 'integer',
                'NamaMapelAsli' => 'string', 'NamaMapelLeger' => 'string', 'NamaMapelTranskrip' => 'string',
                'SKS' => 'number', 'BebanJP' => 'number', 'JP' => 'number',
                'NilaiPengetahuan' => 'number', 'PredikatPengetahuan' => 'string', 'DeskripsiPengetahuan' => 'text',
                'NilaiKeterampilan' => 'number', 'PredikatKeterampilan' => 'string', 'DeskripsiKeterampilan' => 'text',
                'SKSxN1' => 'number', 'SKSxN2' => 'number', 'RataRataARD' => 'number', 'BebanxN' => 'number',
                'NilaiAkhir' => 'number', 'JPxNi' => 'number', 'CapaianKompetensi' => 'text',
                'KBM' => 'number', 'KKM' => 'number', 'KKTP' => 'number',
            ],
        ],
        'Data Pendukung' => [
            'table' => 'data_pendukung',
            'key' => 'IdItem',
            'fields' => [
                'JenisItem' => 'string', 'Urutan' => 'integer', 'NamaItem' => 'string', 'Nilai' => 'number',
                'Predikat' => 'string', 'Jumlah' => 'number', 'Satuan' => 'string',
                'Keterangan' => 'text', 'IsianTercetakAsli' => 'text',
            ],
        ],
        'Rentang Predikat' => [
            'table' => 'rentang_predikat',
            'key' => 'IdRentangPredikat',
            'fields' => [
                'Urutan' => 'integer', 'Predikat' => 'string', 'RentangTercetak' => 'string',
            ],
        ],
    ];

    // [judul kolom sumber, kolom tujuan, tabel rujukan, nullable]
    public const REFERENCES = [
        'pengaturan_cetak' => [
            ['KodeFormatCetak', 'format_cetak_id', 'format_cetak', false],
        ],
        'rapor_semester' => [
            ['IdFinal', 'siswa_id', 'siswa', false],
            ['KodeFormatCetak', 'format_cetak_id', 'format_cetak', true],
            ['IdPengaturanCetak', 'pengaturan_cetak_id', 'pengaturan_cetak', true],
        ],
        'nilai_mapel' => [
            ['IdRapor', 'rapor_semester_id', 'rapor_semester', false],
        ],
        'data_pendukung' => [
            ['IdRapor', 'rapor_semester_id', 'rapor_semester', false],
        ],
        'rentang_predikat' => [
            ['IdPengaturanCetak', 'pengaturan_cetak_id', 'pengaturan_cetak', false],
        ],
    ];

    public static function column(string $heading): string
    {
        return strtolower(preg_replace('/(?<=[a-z0-9])[A-Z]|(?<=[A-Z])[A-Z](?=[a-z])/', '_$0', $heading));
    }
}
