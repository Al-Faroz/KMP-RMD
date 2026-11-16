<?php

namespace App\Models;

use CodeIgniter\Model;

final class SiswaModel extends Model
{
    public const IDENTITAS = [
        'Identitas siswa' => [
            'nama' => ['Nama lengkap', 'text'],
            'nis' => ['NIS', 'text'],
            'nisn' => ['NISN', 'text'],
            'tempat_lahir' => ['Tempat lahir', 'text'],
            'tanggal_lahir' => ['Tanggal lahir', 'date'],
            'jenis_kelamin' => ['Jenis kelamin', 'text'],
            'agama' => ['Agama', 'text'],
            'status_dalam_keluarga' => ['Status dalam keluarga', 'text'],
            'anak_ke' => ['Anak ke', 'number'],
            'alamat_siswa' => ['Alamat siswa', 'textarea'],
            'telepon_siswa' => ['Telepon siswa', 'tel'],
        ],
        'Penerimaan' => [
            'sekolah_asal' => ['Sekolah asal', 'text'],
            'kelas_diterima' => ['Kelas saat diterima', 'text'],
            'tanggal_diterima' => ['Tanggal diterima', 'date'],
        ],
        'Orang tua dan wali' => [
            'nama_ayah' => ['Nama ayah', 'text'],
            'pekerjaan_ayah' => ['Pekerjaan ayah', 'text'],
            'nama_ibu' => ['Nama ibu', 'text'],
            'pekerjaan_ibu' => ['Pekerjaan ibu', 'text'],
            'alamat_orang_tua' => ['Alamat orang tua', 'textarea'],
            'telepon_orang_tua' => ['Telepon orang tua', 'tel'],
            'nama_wali' => ['Nama wali', 'text'],
            'pekerjaan_wali' => ['Pekerjaan wali', 'text'],
            'alamat_wali' => ['Alamat wali', 'textarea'],
            'telepon_wali' => ['Telepon wali', 'tel'],
        ],
    ];

    protected $table = 'siswa';

    public function cari(string $kata, string $angkatan): array
    {
        $this->select('siswa.id, siswa.nama, siswa.nis, siswa.nisn, angkatan.tahun_masuk')
            ->join('angkatan', 'angkatan.id = siswa.angkatan_id');

        if ($angkatan !== '') {
            $this->where('angkatan.tahun_masuk', (int) $angkatan);
        }
        if ($kata !== '') {
            // Escape user-supplied wildcards before the builder adds its LIKE pattern.
            $literal = strtr($kata, ['!' => '!!', '%' => '!%', '_' => '!_']);
            $this->groupStart()
                ->like('siswa.nama', $literal, 'both', true)
                ->orLike('siswa.nis', $literal, 'both', true)
                ->orLike('siswa.nisn', $literal, 'both', true)
                ->groupEnd();
        }

        return $this->orderBy('siswa.nama')->orderBy('siswa.id')->paginate(20);
    }

    public function daftarAngkatan(): array
    {
        return $this->db->table('angkatan')->select('tahun_masuk')
            ->orderBy('tahun_masuk', 'DESC')->get()->getResultArray();
    }

    public function identitas(string $id): ?array
    {
        return $this->select('siswa.*, angkatan.tahun_masuk')
            ->join('angkatan', 'angkatan.id = siswa.angkatan_id')
            ->where('siswa.id', $id)->first();
    }

    public function simpanIdentitas(string $id, array $identitas): bool
    {
        $this->setAllowedFields(array_keys(array_merge(...array_values(self::IDENTITAS))));

        return $this->update($id, $identitas);
    }
}
