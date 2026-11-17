# Cetak Rapor F4

RMD membuat PDF dari data terbaru di database saat operator menjalankan tindakan cetak. Semua dokumen memakai kertas **F4 (210 × 330 mm)**, termasuk rapor, sampul, identitas, transkrip, lembar nilai, dan leger. Sampul, identitas, dan dokumen pendukung memiliki tindakan cetak tersendiri.

| Aksi | Keluaran |
|---|---|
| **Satu siswa, satu semester** | Tombol **Cetak Rapor** di Detail Semester membuka PDF F4 di tab baru saat arsip berstatus Ditemukan. |
| **Satu siswa, semua semester** | Dari Detail Siswa, **Cetak Semua Semester** membuka PDF gabungan dari rapor yang siap cetak, urut nomor semester. |
| **Satu rombel, satu semester** | Pilih angkatan, semester, dan rombel. **Tampilkan** membuka daftar status siswa. **Buat PDF** membuka rapor siap cetak dalam satu PDF, urut nama siswa. |

## Leger per rombel

Pilih angkatan, semester, dan rombel pada halaman **Cetak Rombel**. Setelah daftar siswa tampil, **Cetak Leger** membuka PDF F4 lanskap (330 × 210 mm) dengan satu baris per siswa pada setiap bagian tabel dan satu kolom per mapel. Nama dan urutan siswa mengikuti daftar rombel. Arsip yang belum ditemukan tetap muncul dengan statusnya.

Nilai diambil dari arsip. Kurikulum Merdeka memakai **Nilai Akhir**, ARD memakai **Rata-rata ARD** jika tersedia, dan nilai Pengetahuan serta Keterampilan yang tersimpan terpisah dicetak sebagai pasangan angka **Pengetahuan/Keterampilan**. Rombel dengan banyak mapel dibagi menjadi bagian kolom dengan pengulangan identitas siswa. Kepala tabel diulangi pada halaman lanjutan.

## Format historis

Setiap rapor memilih tata letak dari kode format pada entri rapornya. Dalam satu semester, kode bisa berbeda antar siswa. Contohnya, data angkatan 2019 semester 6 memuat ARD dan RDM K13. PDF gabungan tetap mengikuti format setiap rapor dan ukuran F4.

Pada hasil Cetak Rombel, arsip yang belum ditemukan dan format yang belum tersedia ditampilkan bersama statusnya. PDF memuat arsip yang ditemukan dan mempunyai format siap cetak. Setiap rapor dimulai pada halaman baru.

| Format | Acuan dan susunan |
|---|---|
| `EXCEL_KHS1` | Sheet **KHS1**. Sikap, tabel gabungan Pengetahuan dan Keterampilan dengan SKS, rekap semester, data pendukung, dan tanda tangan. Acuan awal angkatan 2016 berasal dari workbook Excel kelas IX 2018/2019. |
| `ARD_KHS2` | Sheet **KHS2**. Sikap, tabel nilai dan predikat, bagian deskripsi Pengetahuan dan Keterampilan, data pendukung, dan tanda tangan. |
| `RDM_PDF_2023_VIIA` | RDM Kurikulum Merdeka. Bagian **Capaian Hasil Belajar** memuat JP, nilai akhir, JP × nilai, dan capaian kompetensi. Acuan visual yang sudah diperiksa adalah VII A semester 1 tahun pelajaran 2023/2024. |
| `RDM_K13` | Acuan cetak 8A semester 3 tahun pelajaran 2020/2021. Sikap, tabel JP dan nilai Pengetahuan serta Keterampilan, deskripsi terpisah, data pendukung, dan pengesahan. |

Pada pratinjau F4, cocokkan ukuran halaman, susunan kolom, pemisah halaman, keterbacaan deskripsi panjang, dan posisi tanda tangan terhadap acuan format. Nama, NIS dan NISN pada kepala rapor diambil dari Data Siswa sesuai bidang yang dipakai format tersebut.

## Transkrip Nilai Enam Semester

Dari **Detail Siswa**, operator membuka **Cetak Transkrip** untuk melihat PDF satu siswa dengan nilai terbaru dari Semester 1–6. Transkrip memakai F4 potret (210 × 330 mm), kop MTsN 4 Jombang mengikuti RDM Kurikulum Merdeka, identitas terbaru (Nama Siswa dan No. Induk / NISN), serta dua tabel sesuai halaman pertama draf yang disetujui.

- Excel KHS1, ARD KHS2, dan RDM K13 mengisi **A. Nilai Pengetahuan** dan **B. Nilai Keterampilan**. Tiap semester menampilkan angka dan predikat yang tercatat.
- Semester RDM Kurikulum Merdeka menampilkan **Nilai Akhir** pada tabel A. Judul tabel dan keterangan semester menyatakan jenis nilai yang ditampilkan. Kolom huruf dan tabel keterampilan tetap kosong untuk semester tersebut jika tidak ada komponen padanannya.
- Rata-rata per mata pelajaran dihitung dari angka yang tersedia pada tiap tabel dan ditampilkan hingga dua desimal. Sel kosong tidak ikut dihitung. Nilai nol tetap dihitung.
- Nama mapel menggunakan padanan transkrip dari arsip jika tersedia. Sel yang tidak tersedia dibiarkan kosong dan daftar semester yang arsipnya belum ditemukan dicantumkan di bawah tabel.
- Ruang pengesahan memakai nama, NIP, tempat dan tanggal dari pengaturan cetak rapor terakhir yang tersedia bagi siswa. Ruang tanda tangan disediakan pada akhir dokumen.

## Dokumen per siswa

| Dokumen | Aksi | Kertas dan isi |
|---|---|---|
| Sampul | **Detail Siswa → Cetak Sampul** | F4 potret. Logo Kementerian Agama, judul laporan hasil belajar, nama, NIS dan NISN siswa. |
| Identitas | **Detail Siswa → Cetak Identitas** | F4 potret. Biodata terkini siswa, penerimaan, orang tua dan wali, dengan ruang pasfoto 3 × 4 cm. Pengesahan memakai pengaturan cetak semester paling awal yang ditemukan. |
| Lembar Nilai | **Detail Semester → Cetak Lembar Nilai** | F4 potret. Tampilkan nilai dan predikat tersimpan, beban belajar, dan ringkasan yang tersedia untuk semester pilihan. Tombol tersedia ketika arsip berstatus Ditemukan. |

Satu tata letak per dokumen berlaku untuk keempat tipe rapor. Kolom Lembar Nilai menyesuaikan data arsip tiap semester. Transkrip, Sampul, Identitas, dan Lembar Nilai dibuka di tab baru.

## Implementasi layout dan pemeriksaan

Gunakan view cetak CI4 dengan stylesheet CSS biasa khusus dokumen. Uji dimensi fisik F4 pada keluaran PDF dan lakukan satu uji cetak dari XAMPP menggunakan kertas yang dipakai madrasah. Letakkan tiap rapor pada halaman baru dalam PDF gabungan. Cocokkan isi dan urutan PDF dengan pilihan pada layar.

Pada ARD KHS2 dan RDM K13, bagian Deskripsi dimulai di halaman baru. Tabel panjang mengulang kepala kolom pada halaman lanjutan. Ringkasan nilai dan rentang predikat dikelompokkan setelah tabel nilai. Data pendukung dan pengesahan ditempatkan bersama jika keduanya muat di satu halaman.

Semua format menyediakan sedikitnya dua baris Ekstrakurikuler dan tiga baris Prestasi. Baris dengan kolom keterangan kosong memberi ruang tulis tangan 8 mm. Jika belum terisi, Catatan Wali Kelas menyediakan ruang minimal 22 mm dan Tanggapan Orang Tua / Wali 27 mm.

Cetak Rapor membaca nilai, predikat, deskripsi, ringkasan, dan pengesahan yang tersimpan pada semester yang dipilih. Format memakai susunan bagian dari arsip dan penyesuaian lebar kolom serta pemisah halaman untuk F4. Kolom Rata-rata dan Beban × Nilai pada RDM K13 muncul jika nilainya tersimpan. Bagian Sikap disediakan pada format K13 dan arsip lama, dengan sel kosong jika isian tidak tersedia. Pengesahan menampilkan jabatan, nama, NIP, dan ruang tanda tangan. Data impor saat ini belum menyimpan kode verifikasi QR dari RDM Merdeka.

- [Workbook Excel KHS1 kelas IX 2018/2019](https://chatgpt.com/library?file_id=libfile_869df16be2d8819182f0a0fb03fb462e&account_id=personal)
- [Workbook ARD KHS2 semester 1 2019/2020](https://chatgpt.com/library?file_id=libfile_797f95ecdf448191a7be0028a323a078&account_id=personal)
- [Contoh cetak RDM Kurikulum Merdeka VII A 2023/2024](https://chatgpt.com/library?file_id=libfile_a81825ed933081919e8b2dfb00fe0fcd&account_id=personal)
