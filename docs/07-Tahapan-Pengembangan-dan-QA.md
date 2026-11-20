# Tahapan Pengembangan dan QA

## Lingkungan lokal

KMP-RMD berjalan di XAMPP dari `E:\xampp\htdocs\kmp-rdm` dengan database `dt_rdm`. Panduan pemasangan dan penggunaan awal tersedia di [README KMP-RMD](../README.md).

## Tahapan pengembangan dan kriteria uji

| Tahap | Ruang lingkup | Kriteria uji di XAMPP |
|---|---|---|
| **1. Fondasi aplikasi** | Integrasikan layout operator Sneat Free dan Bootstrap 5 ke proyek CI4 yang sudah berjalan di XAMPP. | Navigasi **Data Siswa** dan **Cetak Rombel** menuju halaman yang sesuai. Menu berfungsi pada layar lebar dan sempit. Periksa pembatasan HTTP untuk file dan direktori internal. |
| **2. Database dan data awal** | Uji koneksi CI4 ke `dt_rdm`, siapkan skema, dan muat workbook awal per angkatan. | Database aktif adalah `dt_rdm`. Jumlah impor cocok dengan tabel pembanding. ID sumber yang sama pada dua angkatan memuat entri berbeda. Relasi siswa, rapor, nilai dan data pendukung sesuai. |
| **3. Data Siswa dan riwayat** | Pencarian, filter angkatan, detail siswa, semester 1–6, serta edit identitas. | Pencarian dan filter membuka siswa yang tepat. Perubahan identitas muncul pada detail siswa dan tercermin di cetak saat fitur cetak tersedia. |
| **4. Pemutakhiran rapor** | Edit Rapor, unduh Excel, impor dengan pratinjau, dan penambahan arsip yang ditemukan. | Uji isi baru, penggantian nilai, angka nol, sel kosong, dan file untuk siswa atau semester lain. Pastikan perubahan masuk ke semester yang dipilih. |
| **5. Cetak satu semester** | Implementasi layout F4 mulai dari KHS1, lalu KHS2 dan RDM Kurikulum Merdeka. Tambahkan RDM K13 setelah acuannya bisa diverifikasi. | Cocokkan setiap format dengan sumber yang relevan. Periksa hasil koreksi identitas dan nilai, pemisah halaman, teks panjang, dan tanda tangan. |
| **6. Cetak gabungan** | Cetak semua semester satu siswa dan satu rombel satu semester. | Uji satu siswa angkatan 2017 dengan format campuran serta hasil cetak rombel yang memuat format berbeda jika datanya tersedia. Uji satu rombel yang sebagian arsipnya belum tersedia. Urutan semester, format per rapor, dan jumlah rapor di PDF sesuai pilihan. |
| **7. Cetak dokumen terpisah dan regresi** | Tindakan cetak sampul, halaman identitas, dan dokumen pendukung yang telah dipetakan. Uji ulang alur pencarian, edit, impor, dan cetak. | Setiap keluaran mencetak dokumen yang dipilih pada kertas F4 (210 × 330 mm). |
| **8. Akses operator** | Login, logout, dan pengelolaan akun operator. | Halaman operator memerlukan login. Uji penambahan akun dan pencabutan akses setelah akun dihapus. |
| **9. Riwayat dan pemulihan arsip** | Catat edit identitas, edit rapor, impor Excel, dan pemulihan. | Cocokkan nilai sebelum dan sesudah beserta nama operator. Pulihkan perubahan terbaru pada satu semester. Pastikan perubahan lain tetap tersimpan. |
| **10. Area publik dan cetak dengan kode** | Beranda dan pencarian siswa, cetak dokumen per siswa dengan kode, permintaan kode sementara lewat WhatsApp, dan navigasi login operator. | Uji pencarian dengan filter angkatan, cetak tanpa login dengan kedua kode, penolakan kode yang salah atau kedaluwarsa, tautan WhatsApp, serta tombol **Kembali ke Beranda** di desktop dan ponsel. |

### Cara kerja per tahap

1. Gunakan Master Dokumen yang disetujui sebagai acuan. Jika keputusan bisnis berubah, perbarui dokumen sebelum menyesuaikan kode.
2. Kerjakan satu tahap melalui branch dan PR. Sertakan instruksi pull, cara uji XAMPP, serta migrasi atau patch SQL jika tahap tersebut mengubah database.
3. Pengguna menguji di localhost dan menyampaikan hasil. Perbaiki temuan pada branch yang sama. Setelah pengguna menyatakan **FIX**, lakukan merge sesuai alur GitHub dan lanjutkan tahap berikutnya.

## Serah terima setiap tahap

Catat ruang lingkup PR, instruksi uji, hasil pemeriksaan, dan temuan yang masih terbuka. Acuan fondasi ada di [02 · Arsitektur CodeIgniter 4](02-Arsitektur-CodeIgniter-4.md), [03 · Standar Bootstrap 5](03-Standar-Bootstrap-5.md), dan [04 · Integrasi Sneat Free](04-Integrasi-Sneat-Free.md).

## Serah terima lokal pasca-Patch V2

- [PR #12](https://github.com/Al-Faroz/KMP-RMD/pull/12) sudah masuk ke `main` pada commit `f9e97cb` setelah uji cetak XAMPP. Perubahan mencakup pembuatan PDF dengan mPDF untuk KHS1 dan KHS2 serta perapian tata letak.
- Audit pada salinan database mencatat **22.103 rapor Ditemukan** dan **4.849 posisi semester Belum Ditemukan**. Pada 11 rapor KHS1 angkatan 2017, 31 isian nilai masih kosong atau bernilai nol pada salinan arsip yang cocok.
- Pemeriksaan pada salinan database meluluskan **19 PDF F4**, termasuk keempat format rapor, cetak rombel, leger, dan transkrip. Paket internal `RMD_QA_PASCA_V2.zip` memuat hasil uji, kueri audit baca-saja, dan rujukan sumber per isian.
- Backup SQL lokal pasca-Patch V2 dibuat pada 20 November 2026 di `%USERPROFILE%\Backup-RMD`. Pembuatan file dan checksum SHA-256 telah dikonfirmasi. Dump dan arsip audit disimpan sebagai dokumen internal karena memuat data siswa.

## Serah terima area publik

- Uji XAMPP oleh pengguna mengonfirmasi kedua kode akses dan tombol **Kembali ke Beranda** pada halaman login. [PR #14](https://github.com/Al-Faroz/KMP-RMD/pull/14)
- Uji browser di Apache dan MariaDB mencakup pencarian siswa, pembuatan dan rotasi kode sementara, penolakan kode kedaluwarsa, akses cetak dengan kode universal tanpa login, salin kedua kode, pembatasan percobaan, dan draf WhatsApp. Navigasi login dan beranda diuji pada lebar layar 1.366 px dan 360 px.
- Teks dan hasil render PDF publik cocok dengan keluaran operator pada empat format rapor dan lima keluaran lain, yaitu gabungan semua semester, transkrip, sampul, identitas, dan lembar nilai.
- Untuk memasang tahap ini pada XAMPP, jalankan migrasi dan isi konfigurasi kode universal pada `.env` lokal. Simpan nomor WhatsApp operator melalui menu **Kode Akses Cetak** agar tombol permintaan kode bisa digunakan. [Panduan pemasangan](../README.md#mengaktifkan-cetak-publik)
