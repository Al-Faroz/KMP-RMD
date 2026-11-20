# Arsitektur CodeIgniter 4

CI4 menangani permintaan halaman, validasi input, transaksi data, serta pembuatan keluaran Excel dan PDF. Aturan fungsional mengacu pada [01 · Aturan Bisnis dan Alur Operator](01-Aturan-Bisnis-dan-Alur-Operator.md).

## Lingkungan CI4 di XAMPP

- Proyek lokal berada di `E:\xampp\htdocs\kmp-rdm`. Buka [KMP-RMD di XAMPP](http://localhost/kmp-rdm/) untuk mencari siswa dan mencetak dokumen. Gunakan **Masuk Operator** untuk mengelola data.
- File publik awal telah disalin ke root proyek. Di branch `main`, `index.php` berada sejajar dengan `app/` dan memuat `app/Config/Paths.php` dari root. Apache memakai entry point ini untuk URL lokal yang sudah berjalan. [index.php](https://github.com/Al-Faroz/KMP-RMD/blob/main/index.php)
- Repository menetapkan PHP `^8.2` dan CI4 `^4.7` di `composer.json`. Versi CI4 yang terkunci adalah `4.7.6`. [composer.json](https://github.com/Al-Faroz/KMP-RMD/blob/main/composer.json) · [composer.lock](https://github.com/Al-Faroz/KMP-RMD/blob/main/composer.lock)
- Jalankan pengembangan dari checkout tersebut. Untuk instalasi dependensi pada checkout baru, gunakan `composer install` agar versinya mengikuti `composer.lock`. [Instalasi Composer](https://codeigniter.com/user_guide/installation/installing_composer.html)

## Struktur proyek

| Lokasi | Tanggung jawab |
|---|---|
| `index.php` | Entry point aplikasi di root proyek, sejajar dengan `app/`. |
| `.htaccess` | Aturan Apache pada root proyek. |
| `app/Config/Routes.php` | Daftar rute eksplisit untuk halaman operator, perubahan data, impor, dan cetak. |
| `app/Controllers/` | Menerima permintaan, menjalankan validasi dan pemeriksaan akses, memanggil proses aplikasi, lalu mengembalikan view, redirect, atau unduhan. |
| `app/Models/` | Akses siswa, rapor semester, nilai mapel, dan pengaturan cetak. |
| `app/Libraries/` | Proses impor data awal, pratinjau dan konfirmasi Excel, penyusunan data cetak, dan integrasi pembuat PDF. |
| `app/Database/Migrations/` | Perubahan skema yang dapat dijalankan melalui CLI. |
| `app/Views/` | Layout operator, halaman kerja, dan layout dokumen cetak. |
| `public/assets/` | Aset web yang dipakai browser. |
| `writable/` | Cache, log, dan area kerja internal saat dibutuhkan. |
| `tests/` | Uji relasi data, impor, edit, dan penyusunan keluaran cetak. |
| `docs/` | Master Dokumen dan referensi pengembangan. |

## Konfigurasi aplikasi lokal

File `.env` berada di root proyek lokal. Setelan aplikasi yang dilaporkan pengguna:

```dotenv
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost/kmp-rdm/'
app.forceGlobalSecureRequests = false
app.CSPEnabled = false
session.driver = 'CodeIgniter\Session\Handlers\FileHandler'
logger.threshold = 4
```

Konfigurasi database tercatat pada [05 · Database dan Impor Excel](05-Database-dan-Impor-Excel.md). `.env` merupakan konfigurasi lokal yang dikecualikan dari Git. Pada uji baseline Apache, pastikan akses HTTP langsung ke `.env`, `app/`, `vendor/`, dan `writable/` tertutup karena URL lokal memakai entry point di root. [Aturan Git](https://github.com/Al-Faroz/KMP-RMD/blob/main/.gitignore) · [Aturan Apache](https://github.com/Al-Faroz/KMP-RMD/blob/main/.htaccess)

## Rute usulan

Parameter `{siswa}` merujuk ke ID internal dan `{semester}` ke nomor 1–6. Satu entri semester mengikat operasi baca, edit, impor, dan cetak. Detail format dan keluaran mengikuti [06 · Cetak Rapor F4](06-Cetak-Rapor-F4.md).

| Metode | Rute | Tindakan |
|---|---|---|
| GET | `/` | Tampilkan statistik dan pencarian siswa. |
| GET | `/arsip-cetak/saran` | Beri saran nama dan angkatan siswa. |
| GET | `/arsip-cetak/siswa/{siswa}` | Buka pilihan dokumen cetak. Permintaan POST untuk PDF per siswa memvalidasi kode cetak yang dikirim dalam formulir. |
| GET / POST | `/login` | Buka form dan mulai sesi operator. |
| POST | `/keluar` | Akhiri sesi operator. |
| GET / POST | `/operator/kode-akses-cetak` | Buat dan salin kode akses cetak universal. |
| POST | `/operator/kode-akses-cetak/whatsapp` | Simpan nomor operator untuk modal cetak publik. |
| GET | `/operator` | Lihat daftar operator. |
| GET / POST | `/operator/tambah` | Buka form dan buat akun operator. |
| GET / POST | `/operator/{id}/edit` | Buka form dan simpan perubahan akun. |
| GET / POST | `/operator/{id}/hapus` | Buka konfirmasi dan hapus akun. |
| GET | `/siswa` | Cari siswa dan filter angkatan. |
| GET | `/siswa/{siswa}` | Buka detail dan riwayat semester. |
| GET / POST | `/siswa/{siswa}/edit` | Buka form dan simpan identitas. |
| GET | `/siswa/{siswa}/riwayat` | Daftar perubahan identitas. |
| GET | `/siswa/{siswa}/semester/{semester}/riwayat` | Daftar perubahan rapor semester. |
| GET / POST | `/siswa/{siswa}/riwayat/perubahan/{id}` | Bandingkan data dan pulihkan perubahan terbaru. |
| GET | `/siswa/{siswa}/semester/{semester}` | Buka detail semester. |
| GET / POST | `/siswa/{siswa}/semester/{semester}/edit` | Buka form dan simpan rapor semester. |
| GET | `/siswa/{siswa}/semester/{semester}/excel` | Unduh Excel satu siswa dan satu semester. |
| POST | `/siswa/{siswa}/semester/{semester}/impor/pratinjau` | Validasi file dan tampilkan perubahan. |
| POST | `/siswa/{siswa}/semester/{semester}/impor/konfirmasi` | Simpan perubahan yang dikonfirmasi. |
| GET | `/siswa/{siswa}/semester/{semester}/cetak` | Buat PDF satu rapor dari data terkini. |
| GET | `/siswa/{siswa}/transkrip` | Buat PDF transkrip nilai Semester 1–6. |
| GET | `/siswa/{siswa}/sampul` | Buat PDF sampul laporan hasil belajar. |
| GET | `/siswa/{siswa}/identitas` | Buat PDF biodata peserta didik. |
| GET | `/siswa/{siswa}/semester/{semester}/nilai` | Buat PDF lembar nilai dari arsip semester yang ditemukan. |
| GET | `/siswa/{siswa}/cetak-semua-semester` | Buat PDF gabungan rapor yang tersedia. |
| GET | `/cetak-rombel` | Pilih angkatan, semester, dan rombel, lalu tinjau daftar rapor. |
| GET | `/cetak-rombel/pdf` | Buat PDF gabungan dari pilihan rombel. |

Sampul dan halaman identitas memiliki aksi tersendiri dari Detail Siswa. Lembar Nilai dibuka dari Detail Semester. Dokumen PDF tersebut dibuka di tab baru.

## Validasi dan akses

- Validasi bahwa siswa dan semester pada rute sesuai dengan data yang dibuka. Untuk impor, cocokkan juga identitas target dan semester file dengan rute.
- Terapkan validasi server untuk perubahan data dan CSRF untuk form yang menulis data. Preview impor memuat perubahan yang terkait dengan sesi operator dan target yang sama saat dikonfirmasi.
- Beranda, pencarian publik, dan halaman Masuk dapat dibuka tanpa sesi operator. Setiap permintaan PDF pada `/arsip-cetak/` memakai kode cetak dan dibatasi jika percobaan gagal berulang. Rute operator memerlukan sesi, termasuk rute PDF dan Excel pada area tersebut. Form masuk, keluar, dan perubahan data menggunakan token CSRF yang tersimpan dalam sesi.
- Pembuatan PDF membaca data terkini dan mengirim hasil ke pengguna. Database menyimpan data rapor dan konfigurasi cetak.

Excel menggunakan PhpSpreadsheet. PDF dokumen siswa menggunakan mPDF dengan direktori sementara `writable/cache/mpdf`. Akun operator pertama dibuat melalui perintah Spark yang membaca kata sandi dari stdin. Kriteria penerimaan setiap tahap tersedia di [07 · Tahapan Pengembangan dan QA](07-Tahapan-Pengembangan-dan-QA.md).

## Membuat akun operator di XAMPP

Jalankan dari root proyek. Ganti nama dan username pada contoh. Username memakai 1–64 huruf, angka, titik, garis bawah, atau tanda hubung. Masukkan kata sandi minimal 8 karakter dengan panjang maksimal 72 byte.

```powershell
& 'E:\xampp\php\php.exe' spark migrate
$kataSandiRmd = Read-Host 'Kata sandi operator' -AsSecureString
& {
    $OutputEncoding = [System.Text.UTF8Encoding]::new($false)
    [System.Net.NetworkCredential]::new('', $kataSandiRmd).Password |
        & 'E:\xampp\php\php.exe' spark rmd:operator-buat 'Nama Lengkap Operator' 'operator01'
}
Remove-Variable kataSandiRmd
```

Buka [RMD di XAMPP](http://localhost/kmp-rdm/), lalu masuk dengan akun tersebut. Kelola akun berikutnya dari menu **Operator**. Username harus unik. Kosongkan kata sandi saat mengedit untuk mempertahankannya. Perubahan kata sandi meminta akun terkait masuk kembali. Penghapusan akun mewajibkan konfirmasi dan mengakhiri akses akun tersebut pada permintaan berikutnya.

## Riwayat perubahan dan pemulihan

Jalankan migrasi `CreateRiwayatArsip`. Riwayat mulai tercatat saat operator menyimpan perubahan melalui aplikasi. Tabel `riwayat_arsip` menyimpan versi sebelum dan sesudah dalam transaksi yang sama dengan pembaruan data.

- Buka **Riwayat Identitas** dari **Detail Siswa** atau **Riwayat Perubahan** dari halaman semester. Catatan menyimpan nama operator pada saat tindakan dan menampilkan waktu dalam WIB.
- Pemulihan berlaku per identitas atau semester. Aplikasi memeriksa ulang perubahan terbaru dan kondisi data saat menerima konfirmasi. Data rapor yang dipulihkan meliputi status arsip, nilai mapel, data pendukung, dan pengesahan.
- Sistem mencatat hasil pemulihan sebagai perubahan baru. Riwayat mencakup penyimpanan yang benar-benar mengubah data setelah fitur ini diaktifkan.
