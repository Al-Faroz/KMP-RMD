# KMP-RMD — Raport Digital Madrasah

KMP-RMD mengelola arsip rapor historis angkatan 2016–2023. Pengunjung internal dapat mencari siswa dan mencetak PDF tanpa login. Pengelolaan data memerlukan akun operator.

- Cari siswa berdasarkan nama, NIS, atau NISN, lalu buka arsip per semester.
- Koreksi identitas dan rapor melalui formulir atau impor Excel dengan pratinjau. Perubahan tercatat dalam riwayat dan dapat dipulihkan melalui aplikasi.
- Cetak rapor PDF F4 per semester, semua semester satu siswa, atau satu rombel. Sampul, identitas, lembar nilai, transkrip, dan leger tersedia sebagai dokumen terpisah.
- Format cetak mengikuti arsip semester. Aplikasi mendukung KHS1, KHS2, RDM K13, dan RDM Kurikulum Merdeka.

## Menjalankan di XAMPP

Gunakan PHP yang memenuhi `composer.lock`, Composer, serta Apache dan MySQL dari XAMPP. PHP minimum yang ditetapkan proyek adalah 8.2.

1. Tempatkan checkout di `E:\xampp\htdocs\kmp-rdm`. Pada checkout baru, salin `env` menjadi `.env`. Atur `app.baseURL` ke `http://localhost/kmp-rdm/` dan koneksi database ke `dt_rdm`. [Konfigurasi aplikasi](docs/02-Arsitektur-CodeIgniter-4.md#konfigurasi-aplikasi-lokal)
2. Jalankan pemeriksaan berikut dari PowerShell.

   ```powershell
   Set-Location 'E:\xampp\htdocs\kmp-rdm'
   composer install
   composer check-platform-reqs
   & 'E:\xampp\php\php.exe' spark migrate:status
   ```

3. Sambungkan database `dt_rdm` yang berisi arsip terkini. Untuk mengisi database baru dari workbook, ikuti petunjuk migrasi dan simulasi impor. [Database dan Impor Excel](docs/05-Database-dan-Impor-Excel.md)
4. Buka [KMP-RMD di XAMPP](http://localhost/kmp-rdm/) untuk mencari dan mencetak PDF. Gunakan [Masuk Operator](http://localhost/kmp-rdm/login) untuk mengelola arsip. [Cara membuat akun operator pertama](docs/02-Arsitektur-CodeIgniter-4.md#membuat-akun-operator-di-xampp)

## Dokumentasi

- [Master Dokumen RMD](docs/README.md)
- [Format cetak PDF F4](docs/06-Cetak-Rapor-F4.md)
- [Tahapan pengembangan dan hasil QA pasca-Patch V2](docs/07-Tahapan-Pengembangan-dan-QA.md)
