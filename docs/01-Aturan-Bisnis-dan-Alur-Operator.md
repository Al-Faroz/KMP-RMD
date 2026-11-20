# Aturan Bisnis dan Alur Operator

## Aturan bisnis yang disepakati

| ID | Keputusan |
|---|---|
| AB-01 | Riwayat siswa mencakup semester 1–6. Tampilkan kelas, rombel, dan status arsip berdasarkan data yang tersedia untuk setiap semester. |
| AB-02 | Operator menyunting identitas utama, termasuk nama, NIS, NISN, dan tanggal lahir, melalui **Data Siswa**. Cetak ulang menggunakan identitas terbaru untuk seluruh semester. Kelas dan rombel mengikuti riwayat semester. |
| AB-03 | Operator dapat memperbarui nilai, absensi, dan catatan melalui **Edit Rapor** pada semester yang dibuka. |
| AB-04 | Penambahan dan koreksi lewat Excel memakai **satu file untuk satu siswa dan satu semester**. File diunggah dari **Detail Semester** dan perubahan ditampilkan sebelum disimpan. |
| AB-05 | Saat impor Excel, sel kosong melewati isian terkait. Angka `0` diperlakukan sebagai nilai yang diisi. Operator dapat mengosongkan isian secara langsung melalui **Edit Rapor**. |
| AB-06 | RMD mencetak dari data terbaru di database. Tata letak mengikuti format asli yang tercatat pada rapor semester itu. PDF dibuat saat cetak dan dikirim ke pengguna. Database menyimpan data rapor. |
| AB-07 | Tersedia tiga pilihan cetak rapor. **Satu siswa satu semester** menghasilkan rapor semester pilihan. **Satu siswa semua semester** menghasilkan satu PDF berisi rapor yang tersedia, urut semester. **Satu rombel satu semester** menghasilkan satu PDF gabungan. |
| AB-08 | Cetak rombel memakai angkatan, semester, dan rombel pada periode pilihan. Sebelum membuat PDF, tampilkan rapor yang tersedia dan siswa pada rombel tersebut yang arsipnya belum ditemukan. PDF gabungan memuat rapor yang tersedia. |
| AB-09 | Semua dokumen cetak memakai kertas **F4 (210 × 330 mm)**, termasuk rapor, sampul, identitas, transkrip, lembar nilai, dan leger. |
| AB-10 | Antarmuka operator memakai **Bootstrap 5 dan Sneat Free**. Penyesuaian RMD dan tata letak dokumen cetak memakai **CSS biasa**. |
| AB-11 | Operator masuk dengan username dan kata sandi. Setiap akun hanya memuat nama lengkap, username unik, dan hash kata sandi. Semua akun operator dapat mengelola arsip dan akun operator. Operator dapat mengedit akunnya sendiri. Penghapusan akun dilakukan dari akun operator lain dan memutus akses akun yang dihapus. |
| AB-12 | Simpan riwayat perubahan identitas siswa dan rapor semester, termasuk impor Excel dan pemulihan. Tampilkan operator, waktu, serta nilai sebelum dan sesudah. Operator dapat memulihkan versi sebelum perubahan terbaru untuk identitas atau semester yang dibuka. |
| AB-13 | Beranda menampilkan statistik arsip serta pencarian nama, NIS, atau NISN dengan filter angkatan. Pengunjung internal dapat memilih siswa dan mencetak rapor yang tersedia serta dokumen cetak per siswa tanpa login. PDF memakai proses cetak yang sama dengan area operator. Pengelolaan arsip melalui halaman Masuk Operator. |

## Halaman dan alur operator

| Halaman | Isi dan tindakan |
|---|---|
| **Masuk** | Masukkan username dan kata sandi operator. Setelah masuk, aplikasi membuka Data Siswa. |
| **Operator** | Lihat daftar, tambah, edit, atau hapus akun operator. Biarkan kata sandi kosong saat edit untuk mempertahankan kata sandi saat ini. |
| **Data Siswa** | Halaman awal operator. Cari dengan nama, NIS, atau NISN. Filter berdasarkan angkatan. Buka detail siswa. |
| **Detail Siswa** | Tampilkan identitas dan riwayat semester 1–6. Sediakan **Edit Identitas**, **Cetak Semua Semester**, serta **Cetak Transkrip**, **Cetak Sampul**, dan **Cetak Identitas** dalam tab baru. |
| **Detail Semester** | Tampilkan status arsip, kelas, rombel, periode, nilai, serta Data Pendukung yang memuat pengesahan arsip. Koreksi nama, jabatan, NIP, dan tempat serta tanggal terbit hanya dilakukan melalui **Edit Rapor** saat diperlukan untuk mencocokkan data dengan dokumen asli semester itu. Sediakan **Unduh Excel**, **Impor Excel**, **Edit Rapor**, **Cetak Lembar Nilai**, dan **Cetak Rapor** sesuai data semester. |
| **Cetak Rombel** | Pilih angkatan, semester, lalu rombel. Tampilkan daftar dan jumlah rapor tersedia serta daftar siswa yang arsipnya belum ditemukan. Buat satu PDF gabungan dari daftar yang tersedia. |

Pada riwayat siswa, semester yang berstatus **Belum ditemukan** tetap tampil dan bisa dibuka untuk melengkapi arsip. Daftar rombel hanya bisa menampilkan siswa yang keanggotaannya diketahui untuk semester itu.

Langkah impor dari Detail Semester ada di [05 · Database dan Impor Excel](05-Database-dan-Impor-Excel.md). Susunan hasil cetak ada di [06 · Cetak Rapor F4](06-Cetak-Rapor-F4.md).
