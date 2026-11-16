# Master Dokumen RMD

**Raport Digital Madrasah · Draf v0.3 · 16 November 2026**

RMD digunakan untuk mencari, memperbarui, dan mencetak ulang rapor historis. Delapan workbook final angkatan 2016–2023 menjadi sumber pengisian awal database. Draf v0.3 mencatat fondasi CI4 dan konfigurasi XAMPP yang sudah disiapkan untuk KMP-RMD.

## Lingkungan pengembangan

| Bagian | Konfigurasi |
|---|---|
| Repository | [Al-Faroz/KMP-RMD](https://github.com/Al-Faroz/KMP-RMD), branch utama `main` |
| URL clone | `https://github.com/Al-Faroz/KMP-RMD.git` |
| Folder lokal | `E:\xampp\htdocs\kmp-rdm` |
| URL aplikasi | [http://localhost/kmp-rdm/](http://localhost/kmp-rdm/) |
| Status awal | Halaman **Welcome CodeIgniter** sudah tampil berdasarkan uji lokal pengguna. |
| Database lokal | `dt_rdm`, host `localhost`, port `3306`, driver `MySQLi` |

Detail penempatan entry point dan konfigurasi lokal ada di [02 · Arsitektur CodeIgniter 4](02-Arsitektur-CodeIgniter-4.md). Pemeriksaan koneksi database ada di [05 · Database dan Impor Excel](05-Database-dan-Impor-Excel.md).

## Daftar dokumen

| Dokumen | Acuan untuk |
|---|---|
| [01 · Aturan Bisnis dan Alur Operator](01-Aturan-Bisnis-dan-Alur-Operator.md) | Keputusan fungsional, halaman, serta tindakan operator. |
| [02 · Arsitektur CodeIgniter 4](02-Arsitektur-CodeIgniter-4.md) | Struktur CI4, tanggung jawab komponen, rute, lingkungan XAMPP, dan kebutuhan akses. |
| [03 · Standar Bootstrap 5](03-Standar-Bootstrap-5.md) | Grid, komponen, form, aksesibilitas, dan CSS kustom RMD. |
| [04 · Integrasi Sneat Free](04-Integrasi-Sneat-Free.md) | Shell operator, integrasi view CI4, aset lokal, dan atribusi template. |
| [05 · Database dan Impor Excel](05-Database-dan-Impor-Excel.md) | Entitas dan relasi, pemetaan workbook, pengisian awal, serta impor koreksi. |
| [06 · Cetak Rapor F4](06-Cetak-Rapor-F4.md) | Jenis keluaran, pemilihan format historis, dan kriteria pemeriksaan PDF. |
| [07 · Tahapan Pengembangan dan QA](07-Tahapan-Pengembangan-dan-QA.md) | Tujuh tahap, kriteria uji XAMPP, dan alur branch hingga merge. |

## Stack aplikasi

| Teknologi | Tanggung jawab |
|---|---|
| **CodeIgniter 4 (CI4)** | Framework PHP untuk routing, validasi, akses data, dan proses aplikasi. `composer.lock` mengunci v4.7.6. |
| **Bootstrap 5** | Grid, form, tabel, tombol, dan komponen antarmuka operator. |
| **Sneat – Free Responsive Bootstrap 5 HTML5 Admin Template** | Template HTML5 untuk shell operator, menu vertikal, dan tampilan dasar antarmuka. |
| **CSS biasa** | Penyesuaian tampilan RMD dan stylesheet khusus cetak historis. |

## Keputusan yang masih terbuka

| Topik | Keterangan |
|---|---|
| Dependensi antarmuka | Tag atau commit Sneat Free dan versi Bootstrap 5 yang menyertainya. |
| Akun dan hak akses | Mekanisme autentikasi serta siapa yang boleh melihat, mengubah, mengimpor, dan mencetak. |
| Dokumen cetak terpisah | Cakupan dokumen pendukung di luar sampul dan halaman identitas. |

[Draf Master Dokumen RMD v0.1](https://chatgpt.com/library?file_id=libfile_36c2e286f8c8819183acf94241a0b306&account_id=personal)
