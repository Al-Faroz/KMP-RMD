<?php

namespace App\Models;

use App\Libraries\PengesahanRapor;
use App\Libraries\RaporFields;
use CodeIgniter\Model;
use DomainException;
use RuntimeException;
use Throwable;

final class RaporPemutakhiranModel extends Model
{
    protected $table = 'rapor_semester';

    public function snapshot(array $siswa, int $semester): array
    {
        $model = model(RaporSemesterModel::class);
        $rapor = $model->detail((string) $siswa['id'], $semester);
        $format = $this->db->table('format_cetak')->select('id, id_sumber, nama_format')
            ->where('angkatan_id', $siswa['angkatan_id'])->orderBy('nama_format')->orderBy('id')->get()->getResultArray();
        $pengaturan = $this->db->table('pengaturan_cetak')
            ->select('pengaturan_cetak.id, pengaturan_cetak.tempat_terbit, pengaturan_cetak.tahun_pelajaran, pengaturan_cetak.rombel, format_cetak.id_sumber AS kode_format')
            ->join('format_cetak', 'format_cetak.id = pengaturan_cetak.format_cetak_id')
            ->where('pengaturan_cetak.angkatan_id', $siswa['angkatan_id'])->where('pengaturan_cetak.semester', $semester)
            ->orderBy('pengaturan_cetak.tahun_pelajaran')->orderBy('pengaturan_cetak.rombel')->orderBy('pengaturan_cetak.id')
            ->get()->getResultArray();

        if ($rapor !== null) {
            $rapor['kode_format'] = array_column($format, 'id_sumber', 'id')[$rapor['format_cetak_id']] ?? null;
        }

        return [
            'rapor' => $rapor,
            'pengesahan' => PengesahanRapor::baca($rapor ?? [], $model->pengaturanCetak($rapor['pengaturan_cetak_id'] ?? null)),
            'nilai' => $rapor === null ? [] : $model->nilai((string) $rapor['id']),
            'pendukung' => $rapor === null ? [] : $model->dataPendukung((string) $rapor['id']),
            'format' => array_column($format, null, 'id_sumber'),
            'pengaturan' => array_column($pengaturan, null, 'id'),
        ];
    }

    public function versi(array $snapshot): string
    {
        return hash('sha256', serialize($snapshot));
    }

    public function simpan(array $siswa, int $semester, string $versi, array $patch): void
    {
        $this->db->transException(true);
        if (! $this->db->transBegin()) {
            throw new RuntimeException('Transaksi rapor tidak dapat dimulai.');
        }

        try {
            // Lock the parent even when the semester row does not exist yet.
            $table = $this->db->protectIdentifiers('siswa', true);
            $locked = $this->db->query("SELECT id FROM {$table} WHERE id = ? FOR UPDATE", [$siswa['id']]);
            if ($locked->getNumRows() !== 1) {
                throw new DomainException('Siswa tidak ditemukan.');
            }
            $snapshot = $this->snapshot($siswa, $semester);
            if (! hash_equals($this->versi($snapshot), $versi)) {
                throw new DomainException('Rapor sudah berubah. Buka kembali halaman semester dan ulangi perubahan.');
            }

            $rapor = $snapshot['rapor'] ?? [];
            $meta = $patch['rapor'];
            if (! empty($patch['pengesahan'])) {
                $meta['pengesahan_arsip'] = json_encode(
                    $patch['pengesahan'] + PengesahanRapor::koreksi($rapor), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
                );
            }
            $kodeFormat = ($meta + $rapor)['kode_format'];
            unset($meta['kode_format']);
            $meta['format_cetak_id'] = $snapshot['format'][$kodeFormat]['id'];
            $meta['status_arsip'] = 'Ditemukan';
            $raporId = $rapor['id'] ?? null;
            if ($raporId === null) {
                $this->tulis($this->db->table($this->table)->insert($meta + [
                    'id_sumber' => 'RMD-' . bin2hex(random_bytes(12)),
                    'angkatan_id' => $siswa['angkatan_id'],
                    'siswa_id' => $siswa['id'],
                    'semester' => $semester,
                ]));
                $raporId = $this->db->insertID();
            } else {
                $this->tulis($this->db->table($this->table)
                    ->where('id', $raporId)->where('siswa_id', $siswa['id'])->update($meta));
            }

            foreach (RaporFields::TABLES as $key => $tableName) {
                foreach ($patch[$key] as $baris) {
                    $builder = $this->db->table($tableName);
                    if ($baris['id'] === null) {
                        $this->tulis($builder->insert($baris['fields'] + [
                            'id_sumber' => 'RMD-' . bin2hex(random_bytes(12)),
                            'angkatan_id' => $siswa['angkatan_id'],
                            'rapor_semester_id' => $raporId,
                        ]));
                    } else {
                        $this->tulis($builder->where('id', $baris['id'])->where('rapor_semester_id', $raporId)
                            ->update($baris['fields']));
                    }
                }
            }

            if (! $this->db->transStatus() || ! $this->db->transCommit()) {
                throw new RuntimeException('Transaksi rapor gagal disimpan.');
            }
        } catch (Throwable $error) {
            $this->db->transRollback();
            throw $error;
        }
    }

    private function tulis(bool $berhasil): void
    {
        if (! $berhasil) {
            throw new RuntimeException('Perubahan rapor gagal ditulis.');
        }
    }
}
