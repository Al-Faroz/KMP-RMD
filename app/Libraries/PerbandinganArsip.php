<?php

namespace App\Libraries;

use App\Models\RiwayatArsipModel;
use App\Models\SiswaModel;

final class PerbandinganArsip
{
    public function baca(array $riwayat): array
    {
        $lama = json_decode($riwayat['sebelum'], true, 512, JSON_THROW_ON_ERROR);
        $baru = json_decode($riwayat['sesudah'], true, 512, JSON_THROW_ON_ERROR);
        if ((int) $riwayat['semester'] === RiwayatArsipModel::IDENTITAS) {
            return $this->kolom('Identitas', SiswaModel::kolomIdentitas(), $lama, $baru);
        }

        $raporLama = $lama['rapor'] ?? [];
        $raporBaru = $baru['rapor'] ?? [];
        $meta = ['status_arsip' => ['Status arsip', 'string'], 'format_cetak_id' => ['ID format cetak', 'integer']]
            + PengesahanRapor::WALI + RaporFields::definitions('rapor');
        $raporLama['status_arsip'] ??= 'Belum ditemukan';
        $raporBaru['status_arsip'] ??= 'Belum ditemukan';
        $hasil = $this->kolom('Rapor', $meta, $raporLama, $raporBaru);

        $pengesahan = array_fill_keys(array_keys(PengesahanRapor::KEPALA), 'Mengikuti arsip');
        array_push($hasil, ...$this->kolom('Pengesahan arsip', PengesahanRapor::KEPALA,
            PengesahanRapor::koreksi($raporLama) + $pengesahan,
            PengesahanRapor::koreksi($raporBaru) + $pengesahan));

        foreach (array_keys(RaporFields::TABLES) as $bagian) {
            $sebelum = array_column($lama[$bagian], null, 'id');
            $sesudah = array_column($baru[$bagian], null, 'id');
            $definisi = RaporFields::definitions($bagian);
            $label = RaporFields::LABELS[$bagian];
            foreach ($sebelum + $sesudah as $id => $baris) {
                $nama = $baris['nama_mapel_asli'] ?? $baris['nama_item'] ?? $baris['jenis_item'];
                array_push($hasil, ...$this->kolom("{$label} · {$nama}", $definisi, $sebelum[$id] ?? [], $sesudah[$id] ?? []));
            }
        }

        return $hasil;
    }

    private function kolom(string $bagian, array $definisi, array $lama, array $baru): array
    {
        $hasil = [];
        foreach ($definisi as $key => [$label, $tipe]) {
            $sebelum = $lama[$key] ?? null;
            $sesudah = $baru[$key] ?? null;
            if ($sebelum === $sesudah) {
                continue;
            }
            $hasil[] = [
                'bagian' => $bagian,
                'kolom' => $label,
                'sebelum' => $tipe === 'number' && $sebelum !== null ? RaporFields::number((string) $sebelum) : $sebelum,
                'sesudah' => $tipe === 'number' && $sesudah !== null ? RaporFields::number((string) $sesudah) : $sesudah,
            ];
        }

        return $hasil;
    }
}
