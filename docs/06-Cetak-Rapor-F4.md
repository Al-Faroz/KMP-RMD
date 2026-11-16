# Cetak Rapor F4

RMD membuat PDF dari data terbaru di database saat operator menjalankan tindakan cetak. Semua halaman **Cetak Rapor** memakai F4. Sampul, halaman identitas, dan dokumen pendukung memiliki tindakan cetak tersendiri.

| Aksi | Keluaran |
|---|---|
| **Satu siswa, satu semester** | Satu PDF untuk rapor semester pilihan jika arsip tersedia. |
| **Satu siswa, semua semester** | Satu PDF berisi semua rapor yang tersedia, urut nomor semester. |
| **Satu rombel, satu semester** | Satu PDF gabungan berisi rapor yang tersedia di rombel pada semester pilihan. Daftar dan status arsip dapat dilihat sebelum membuat PDF. |

## Format historis

Setiap rapor memilih tata letak dari kode format pada entri rapornya. Dalam satu semester, kode bisa berbeda antar siswa. Contohnya, data angkatan 2019 semester 6 memuat ARD dan RDM K13. PDF gabungan tetap mengikuti format setiap rapor dan ukuran F4.

| Format | Acuan dan susunan |
|---|---|
| `EXCEL_KHS1` | Sheet **KHS1**. Sikap, tabel gabungan Pengetahuan dan Keterampilan dengan SKS, rekap semester, data pendukung, dan tanda tangan. Acuan awal angkatan 2016 berasal dari workbook Excel kelas IX 2018/2019. |
| `ARD_KHS2` | Sheet **KHS2**. Sikap, tabel nilai dan predikat, bagian deskripsi Pengetahuan dan Keterampilan, data pendukung, dan tanda tangan. |
| `RDM_PDF_2023_VIIA` | RDM Kurikulum Merdeka. Bagian **Capaian Hasil Belajar** memuat JP, nilai akhir, JP × nilai, dan capaian kompetensi. Acuan visual yang sudah diperiksa adalah VII A semester 1 tahun pelajaran 2023/2024. |
| `RDM_K13` | Data tersedia di workbook final. Tata letak rapor Kurikulum 2013 masih perlu dicocokkan dengan contoh asli dari periode yang relevan. |

Pada pratinjau F4, cocokkan ukuran halaman, susunan kolom, pemisah halaman, keterbacaan deskripsi panjang, dan posisi tanda tangan terhadap acuan format. Nama, NIS dan NISN pada kepala rapor diambil dari Data Siswa sesuai bidang yang dipakai format tersebut.

## Implementasi layout dan pemeriksaan

Gunakan view cetak CI4 dengan stylesheet CSS biasa khusus dokumen. Uji dimensi fisik F4 pada keluaran PDF dan lakukan satu uji cetak dari XAMPP menggunakan kertas yang dipakai madrasah. Letakkan tiap rapor pada halaman baru dalam PDF gabungan. Cocokkan isi dan urutan PDF dengan pilihan pada layar.

Untuk RDM Kurikulum 2013, tunggu satu contoh asli sebelum menetapkan tata letaknya. Pastikan pemeriksaan RDM Kurikulum Merdeka diawali dari contoh VII A yang sudah tersedia.

- [Workbook Excel KHS1 kelas IX 2018/2019](https://chatgpt.com/library?file_id=libfile_869df16be2d8819182f0a0fb03fb462e&account_id=personal)
- [Workbook ARD KHS2 semester 1 2019/2020](https://chatgpt.com/library?file_id=libfile_797f95ecdf448191a7be0028a323a078&account_id=personal)
- [Contoh cetak RDM Kurikulum Merdeka VII A 2023/2024](https://chatgpt.com/library?file_id=libfile_a81825ed933081919e8b2dfb00fe0fcd&account_id=personal)
