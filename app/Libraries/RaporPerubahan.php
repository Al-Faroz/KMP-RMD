<?php

namespace App\Libraries;

use DomainException;

final class RaporPerubahan
{
    public function siapkan(array $snapshot, array $masukan, bool $lewatiKosong): array
    {
        $patch = ['rapor' => [], 'nilai' => [], 'pendukung' => []];
        $preview = [];
        $rapor = $snapshot['rapor'] ?? [];
        $patch['rapor'] = $this->bandingkan('rapor', 'Rapor', $rapor, $masukan['rapor'] ?? [], $lewatiKosong, $preview);

        foreach (array_keys(RaporFields::TABLES) as $bagian) {
            $tersedia = array_column($snapshot[$bagian], null, 'id');
            $required = RaporFields::REQUIRED[$bagian];
            $seen = [];
            foreach (array_values($masukan[$bagian] ?? []) as $index => $baris) {
                if (! is_array($baris)) {
                    throw new DomainException('Baris rapor tidak valid.');
                }
                $id = RaporFields::value($baris['id'] ?? null, 'string');
                if ($id !== null && (! isset($tersedia[$id]) || isset($seen[$id]))) {
                    throw new DomainException(RaporFields::LABELS[$bagian] . ': ada ID baris ganda atau berasal dari rapor lain.');
                }
                if ($id !== null) {
                    $seen[$id] = true;
                }

                $sebelum = $tersedia[$id] ?? [];
                $label = $sebelum[$required] ?? (RaporFields::LABELS[$bagian] . ' · baris baru ' . ($index + 1));
                $fields = $this->bandingkan($bagian, $label, $sebelum, $baris, $lewatiKosong, $preview);
                if ($fields === []) {
                    continue;
                }
                if ((($fields + $sebelum)[$required] ?? null) !== null) {
                    $patch[$bagian][] = ['id' => $id, 'fields' => $fields];
                } else {
                    [$judul] = RaporFields::definitions($bagian)[$required];
                    throw new DomainException("{$label}: {$judul} wajib diisi.");
                }
            }
        }

        if ($preview !== []) {
            $meta = $patch['rapor'] + $rapor;
            if (! isset($snapshot['format'][$meta['kode_format'] ?? ''])) {
                throw new DomainException('Pilih kode format rapor yang tersedia untuk angkatan ini.');
            }

            $pengaturanId = $meta['pengaturan_cetak_id'] ?? null;
            if ($pengaturanId !== null
                && ($snapshot['pengaturan'][$pengaturanId]['kode_format'] ?? null) !== $meta['kode_format']) {
                throw new DomainException('Pengaturan cetak harus sesuai dengan format dan semester yang dipilih.');
            }

            if (($rapor['status_arsip'] ?? null) !== 'Ditemukan') {
                $preview[] = [
                    'baris' => 'Rapor',
                    'kolom' => 'Status arsip',
                    'lama' => $rapor['status_arsip'] ?? 'Belum ditemukan',
                    'baru' => 'Ditemukan',
                ];
            }
        }

        return ['patch' => $patch, 'preview' => $preview];
    }

    private function bandingkan(string $bagian, string $baris, array $sebelum, array $masukan, bool $lewatiKosong, array &$preview): array
    {
        $fields = [];
        foreach (RaporFields::definitions($bagian) as $key => [$judul, $tipe]) {
            if (! array_key_exists($key, $masukan)) {
                continue;
            }
            try {
                $baru = RaporFields::value($masukan[$key], $tipe);
            } catch (DomainException $error) {
                throw new DomainException("{$baris} · {$judul}: " . $error->getMessage(), 0, $error);
            }
            if ($baru === null && $lewatiKosong) {
                continue;
            }

            $lama = isset($sebelum[$key]) ? (string) $sebelum[$key] : null;
            if ($lama !== null && $tipe === 'number') {
                $lama = RaporFields::number($lama);
            }
            if ($baru !== $lama) {
                $fields[$key] = $baru;
                $preview[] = ['baris' => $baris, 'kolom' => $judul, 'lama' => $lama, 'baru' => $baru];
            }
        }

        return $fields;
    }
}
