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

## Halaman dan alur operator

| Halaman | Isi dan tindakan |
|---|---|
| **Data Siswa** | Halaman awal. Cari dengan nama, NIS, atau NISN. Filter berdasarkan angkatan. Buka detail siswa. |
| **Detail Siswa** | Tampilkan identitas dan riwayat semester 1–6. Sediakan **Edit Identitas**, **Cetak Semua Semester**, serta **Cetak Transkrip**, **Cetak Sampul**, dan **Cetak Identitas** dalam tab baru. |
| **Detail Semester** | Tampilkan status arsip, kelas, rombel, periode, nilai, dan data pendukung yang tersedia. Sediakan **Unduh Excel**, **Impor Excel**, **Edit Rapor**, **Cetak Lembar Nilai**, dan **Cetak Rapor** sesuai data semester. |
| **Cetak Rombel** | Pilih angkatan, semester, lalu rombel. Tampilkan daftar dan jumlah rapor tersedia serta daftar siswa yang arsipnya belum ditemukan. Buat satu PDF gabungan dari daftar yang tersedia. |

Pada riwayat siswa, semester yang berstatus **Belum ditemukan** tetap tampil dan bisa dibuka untuk melengkapi arsip. Daftar rombel hanya bisa menampilkan siswa yang keanggotaannya diketahui untuk semester itu.

Langkah impor dari Detail Semester ada di [05 · Database dan Impor Excel](05-Database-dan-Impor-Excel.md). Susunan hasil cetak ada di [06 · Cetak Rapor F4](06-Cetak-Rapor-F4.md).
