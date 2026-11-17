<?php

namespace App\Models;

use App\Libraries\RaporFields;
use CodeIgniter\Model;
use DomainException;
use RuntimeException;
use Throwable;

final class RiwayatArsipModel extends Model
{
    public const IDENTITAS = 0;
    public const AKSI = [
        'identitas' => 'Edit Identitas',
        'edit' => 'Edit Rapor',
        'impor' => 'Impor Excel',
        'pulihkan' => 'Pemulihan',
    ];

    protected $table = 'riwayat_arsip';

    public function daftar(string $siswaId, int $semester): array
    {
        return $this->select('id, nama_operator, aksi, waktu')
            ->where('siswa_id', $siswaId)->where('semester', $semester)
            ->orderBy('id', 'DESC')->paginate(20);
    }

    public function detail(string $siswaId, string $riwayatId): ?array
    {
        return $this->where('siswa_id', $siswaId)->find($riwayatId);
    }

    public function terakhir(string $siswaId, int $semester): ?string
    {
        $baris = $this->select('id')->where('siswa_id', $siswaId)->where('semester', $semester)->orderBy('id', 'DESC')->first();

        return $baris === null ? null : (string) $baris['id'];
    }

    public function catat(string $siswaId, int $semester, int $operatorId, string $aksi, callable $tulis, ?string $asal = null): void
    {
        $this->db->transException(true);
        if (! $this->db->transBegin()) {
            throw new RuntimeException('Transaksi arsip tidak dapat dimulai.');
        }

        try {
            // The same lock covers initial creation, edits, imports and recovery.
            $tabel = $this->db->protectIdentifiers('siswa', true);
            $kunci = $this->db->query("SELECT id FROM {$tabel} WHERE id = ? FOR UPDATE", [$siswaId]);
            if ($kunci === false || $kunci->getNumRows() !== 1) {
                throw new DomainException('Siswa tidak ditemukan.');
            }
            $operator = $this->db->table('operator')->select('nama_lengkap')->where('id', $operatorId)->get()->getRowArray();
            if ($operator === null) {
                throw new DomainException('Sesi operator sudah tidak berlaku.');
            }

            $sebelum = $this->keadaan($siswaId, $semester);
            $tulis($sebelum);
            $sesudah = $this->keadaan($siswaId, $semester);
            if ($sebelum !== $sesudah) {
                $this->wajib($this->db->table($this->table)->insert([
                    'siswa_id' => $siswaId,
                    'semester' => $semester,
                    'operator_id' => $operatorId,
                    'nama_operator' => $operator['nama_lengkap'],
                    'aksi' => $aksi,
                    'dipulihkan_dari' => $asal,
                    'sebelum' => json_encode($sebelum, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
                    'sesudah' => json_encode($sesudah, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
                    'waktu' => gmdate('Y-m-d H:i:s'),
                ]));
            }

            if (! $this->db->transStatus() || ! $this->db->transCommit()) {
                throw new RuntimeException('Transaksi arsip gagal disimpan.');
            }
        } catch (Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    public function pulihkan(string $siswaId, array $riwayat, int $operatorId): void
    {
        $semester = (int) $riwayat['semester'];
        $asal = (string) $riwayat['id'];
        $this->catat($siswaId, $semester, $operatorId, 'pulihkan', function (array $saatIni) use ($siswaId, $semester, $riwayat, $asal): void {
            if ($this->terakhir($siswaId, $semester) !== $asal) {
                throw new DomainException('Hanya perubahan terbaru yang bisa dipulihkan.');
            }

            $sesudah = json_decode($riwayat['sesudah'], true, 512, JSON_THROW_ON_ERROR);
            if ($saatIni !== $sesudah) {
                throw new DomainException('Data saat ini berbeda dari riwayat. Pemulihan dibatalkan.');
            }

            $sebelum = json_decode($riwayat['sebelum'], true, 512, JSON_THROW_ON_ERROR);
            if ($semester === self::IDENTITAS) {
                $this->wajib(model(SiswaModel::class, false, $this->db)->simpanIdentitas($siswaId, $sebelum));
            } else {
                $this->pulihkanRapor($saatIni, $sebelum);
            }
        }, $asal);
    }

    private function keadaan(string $siswaId, int $semester): array
    {
        if ($semester === self::IDENTITAS) {
            return $this->db->table('siswa')->select(array_keys(SiswaModel::kolomIdentitas()))
                ->where('id', $siswaId)->get()->getRowArray();
        }

        $rapor = $this->db->table('rapor_semester')
            ->where('siswa_id', $siswaId)->where('semester', $semester)->get()->getRowArray();
        $keadaan = ['rapor' => $rapor];
        foreach (RaporFields::TABLES as $bagian => $tabel) {
            $keadaan[$bagian] = $rapor === null ? [] : $this->db->table($tabel)
                ->where('rapor_semester_id', $rapor['id'])->orderBy('id')->get()->getResultArray();
        }

        return $keadaan;
    }

    private function pulihkanRapor(array $saatIni, array $sebelum): void
    {
        $raporLama = $sebelum['rapor'];
        $raporKini = $saatIni['rapor'];
        if ($raporLama !== null) {
            $this->pulihkanBaris('rapor_semester', $raporLama, $raporKini);
        }

        foreach (RaporFields::TABLES as $bagian => $tabel) {
            $lama = array_column($sebelum[$bagian], null, 'id');
            $kini = array_column($saatIni[$bagian], null, 'id');
            $dihapus = array_keys(array_diff_key($kini, $lama));
            if ($dihapus !== []) {
                $this->wajib($this->db->table($tabel)->where('rapor_semester_id', $raporKini['id'])
                    ->whereIn('id', $dihapus)->delete());
            }
            foreach ($lama as $id => $baris) {
                $this->pulihkanBaris($tabel, $baris, $kini[$id] ?? null);
            }
        }

        if ($raporLama === null && $raporKini !== null) {
            $this->wajib($this->db->table('rapor_semester')->where('id', $raporKini['id'])->delete());
        }
    }

    private function pulihkanBaris(string $tabel, array $lama, ?array $kini): void
    {
        if ($kini === null) {
            $this->wajib($this->db->table($tabel)->insert($lama));
        } elseif ($lama !== $kini) {
            $id = $lama['id'];
            unset($lama['id']);
            $this->wajib($this->db->table($tabel)->where('id', $id)->update($lama));
        }
    }

    private function wajib(bool $berhasil): void
    {
        if (! $berhasil) {
            throw new RuntimeException('Data arsip dan riwayat gagal disimpan. Silakan coba lagi.');
        }
    }
}
