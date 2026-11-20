# Database dan Impor Excel

Delapan workbook final angkatan 2016–2023 menjadi data awal. Pemutakhiran oleh operator memakai Excel satu siswa dan satu semester atau **Edit Rapor** dari Detail Semester.

## Database lokal XAMPP

CI4 mengambil konfigurasi database default dari `.env` di `E:\xampp\htdocs\kmp-rdm`. Nama database lokal adalah **`dt_rdm`**.

| Kunci konfigurasi | Nilai lokal |
|---|---|
| `database.default.hostname` | `localhost` |
| `database.default.database` | `dt_rdm` |
| `database.default.DBDriver` | `MySQLi` |
| `database.default.DBPrefix` | Kosong |
| `database.default.port` | `3306` |
| `database.default.charset` | `utf8mb4` |
| `database.default.DBCollat` | `utf8mb4_general_ci` |

Nilai `database.default.username` dan `database.default.password` tetap diatur di `.env` lokal. Sebelum migrasi atau impor awal, cek bahwa koneksi dari CI4 berhasil dan `SELECT DATABASE()` mengembalikan `dt_rdm`. Untuk database lokal pasca-Patch V2, lihat [hasil audit dan backup](07-Tahapan-Pengembangan-dan-QA.md#serah-terima-lokal-pasca-patch-v2).

## Rancangan data dan pengisian awal

Nama entitas di bawah ini bersifat konseptual. Nama tabel, tipe kolom, dan cara menjalankan pengisian awal ditetapkan pada rancangan teknis.

| Entitas | Isi dan relasi |
|---|---|
| **Angkatan** | Tahun masuk. Menjadi kelompok filter dan konteks pencocokan saat impor awal. |
| **Siswa** | Identitas utama dan biodata. Memiliki ID internal. Simpan NIS dan NISN sebagai teks agar nol di depan terjaga. |
| **Rapor Semester** | Satu entri per siswa dan nomor semester. Menyimpan tahun pelajaran, kelas, rombel saat itu, status arsip, kurikulum, kode format, dan ringkasan rapor. |
| **Nilai Mapel** | Merujuk ke rapor semester. Menyimpan kelompok, urutan dan nama mapel asli, beserta nilai, predikat, deskripsi, SKS atau JP sesuai sumber. |
| **Data Pendukung Rapor** | Merujuk ke rapor semester. Mencakup sikap, ekstrakurikuler, prestasi, absensi, dan catatan yang tersedia. |
| **Pengaturan Cetak** | Rujukan rapor untuk identitas madrasah, tempat dan tanggal terbit, serta data penandatangan sesuai periode dan sumber. |
| **Format Cetak** | Kode pada rapor yang menunjuk ke tata letak cetaknya. |

### Pemetaan workbook

- Muat siswa, konfigurasi format, dan pengaturan cetak sebelum memuat rapor semester. Setelah relasi rapor terbentuk, muat nilai mapel dan data pendukung.
- Saat impor awal, cocokkan siswa melalui **angkatan + `IdFinal`**. Batasi pencocokan `IdRapor` dan rujukan baris turunannya pada workbook angkatan yang sama. Sebelas nilai `IdFinal` muncul di kedua workbook angkatan 2018 dan 2019. Setelah pencocokan, gunakan ID internal siswa dan nomor semester untuk mengidentifikasi rapor di RMD.
- Pertahankan status **Ditemukan** dan **Belum ditemukan** dari workbook. Nama, NIS, atau NISN historis yang tercatat pada rapor sumber dapat dipertahankan sebagai rujukan. Data Siswa menentukan identitas yang muncul saat cetak ulang.
- Verifikasi seluruh relasi siswa, rapor, nilai mapel, data pendukung dan pengaturan cetak yang dirujuk sebelum pengisian disahkan. Untuk semester berstatus **Belum ditemukan**, rombel, kode format, dan pengaturan cetak mengikuti informasi yang tersedia pada sumber.

### Angka pembanding impor awal

Angka di bawah berasal dari hitungan baris identitas dan rapor pada workbook final. Satu entri siswa di sini adalah satu entri pada workbook angkatan.

| Angkatan | Entri siswa | Rapor ditemukan | Rapor belum ditemukan |
|---:|---:|---:|---:|
| 2016 | 510 | 3.005 | 55 |
| 2017 | 597 | 3.299 | 283 |
| 2018 | 572 | 3.185 | 247 |
| 2019 | 590 | 3.416 | 124 |
| 2020 | 648 | 3.720 | 168 |
| 2021 | 586 | 3.282 | 234 |
| 2022 | 511 | 1.705 | 1.361 |
| 2023 | 478 | 490 | 2.378 |
| **Total** | **4.492** | **22.102** | **4.850** |

## Instalasi skema dan impor awal di XAMPP

Jalankan dari `E:\xampp\htdocs\kmp-rdm` setelah menyalakan MySQL XAMPP. Pastikan database aktif melalui koneksi CI4 adalah `dt_rdm`.

```powershell
composer install
composer check-platform-reqs
& 'E:\xampp\php\php.exe' spark migrate
& 'E:\xampp\php\php.exe' spark migrate:status
```

`migrate:status` menampilkan `CreateRmdSchema` beserta waktu migrasinya. Migrasi membuat delapan tabel InnoDB. Jika target sudah punya tabel bernama sama tanpa riwayat migrasi yang sesuai, cek skema yang ada sebelum mencoba lagi.

Ekstrak delapan workbook dari ZIP di luar folder publik aplikasi. Contoh ini memakai `E:\RMD-Workbook`. Simulasikan satu angkatan terlebih dahulu dan cocokkan ringkasannya dengan tabel pembanding.

```powershell
& 'E:\xampp\php\php.exe' spark rmd:impor-awal 'E:\RMD-Workbook\RMD-Angkatan-2023.xlsx' --dry-run
```

Untuk menyimpan setelah hasil sesuai, jalankan perintah yang sama tanpa `--dry-run`. Ulangi untuk angkatan lain. Impor menggunakan satu transaksi per angkatan. Impor yang sudah berhasil ditandai dan otomatis dilewati pada pengulangan. Jika satu workbook gagal, perbaiki penyebab yang dilaporkan lalu jalankan lagi untuk angkatan itu.

Skema memuat data dari sheet **Data Siswa**, **Rapor Semester**, **Nilai Mapel**, **Data Pendukung**, **Pengaturan Cetak**, **Format Cetak**, dan **Rentang Predikat**. Tahun masuk dibaca dari **Pengaturan**. ID sumber dalam tiap angkatan disimpan bersama ID internal, dan format cetak mengikuti acuan workbook angkatan masing-masing.

## Impor Excel satu siswa dan satu semester

1. Operator membuka **Detail Semester** dan mengunduh Excel untuk siswa dan semester tersebut. Jika rapornya tersedia, file memuat data RMD saat ini. Untuk rapor yang baru ditemukan, gunakan template yang sesuai dengan format arsipnya.
2. RMD memeriksa apakah identitas target dan semester di file sesuai dengan halaman yang sedang dibuka. Pratinjau merinci penambahan dan perubahan sebelum penyimpanan, misalnya `IPA: 80 → 85`.
3. Saat operator mengonfirmasi, simpan perubahan untuk satu rapor semester dalam satu transaksi. Sel kosong dilewati. Angka `0` disimpan sebagai nilai. **Edit Rapor** memperbarui entri semester yang sama.
4. Pratinjau cetak berikutnya membaca data terbaru. Untuk rapor baru, penyimpanan mengisi status ketersediaan dan kode format berdasarkan data yang dikonfirmasi.

Unduhan RMD berisi sheet **Identitas**, **Rapor Semester**, **Nilai Mapel**, **Data Pendukung**, dan **Acuan Cetak**. Untuk memilih format template arsip yang baru ditemukan, buka **Edit Rapor** dan gunakan pilihan **Format template Excel**. Edit isian data pada workbook, pertahankan identitas target dan judul kolom, lalu kosongkan **ID baris** saat menambah mapel atau data pendukung baru. Isi nilai secara langsung. Impor menerima file `.xlsx` berukuran hingga 1 MB.

Pratinjau berlaku selama 30 menit untuk siswa dan semester terkait dalam sesi operator. Konfirmasi memeriksa versi rapor saat ini. Jika rapor sudah berubah, unggah kembali file untuk membuat pratinjau baru.

[Workbook final angkatan 2016–2023](https://chatgpt.com/library?file_id=libfile_3824903a06dc8191935a112cbf272a7a&account_id=personal)
