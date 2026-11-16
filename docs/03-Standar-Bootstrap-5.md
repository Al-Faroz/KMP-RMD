# Standar Bootstrap 5

Bootstrap 5 menyediakan grid dan komponen untuk layar operator. Sneat Free menyediakan tampilan dan layout dasar komponen tersebut. Implementasi memakai satu rangkaian aset Bootstrap yang kompatibel dengan rilis Sneat terpilih. [Bootstrap 5](https://getbootstrap.com/docs/5.3/getting-started/introduction/) · [Breakpoints](https://getbootstrap.com/docs/5.3/layout/breakpoints/)

## Komponen per halaman

| Halaman | Komponen yang digunakan |
|---|---|
| **Data Siswa** | Form pencarian dengan label, filter angkatan, tabel daftar siswa yang bisa digulir horizontal, dan pagination sesuai jumlah hasil. |
| **Detail Siswa** | Bagian identitas, tombol **Edit Identitas**, tabel riwayat semester, dan tombol cetak yang terkait dengan siswa. |
| **Detail Semester** | Ringkasan periode, tabel nilai dan data pendukung, aksi unduh dan unggah Excel, serta aksi edit dan cetak. |
| **Pratinjau Impor** | Ringkasan validasi dan tabel berisi bidang, data saat ini, dan data yang akan disimpan. Tombol konfirmasi tersedia ketika validasi berhasil. |
| **Cetak Rombel** | Filter angkatan, semester, dan rombel, diikuti ringkasan jumlah rapor serta daftar status siswa. Tombol **Buat PDF** muncul pada hasil yang memiliki rapor tersedia. |

## Pedoman tampilan

- Susun filter dan form dengan grid Bootstrap. Pada layar kecil, bidang dapat turun ke baris berikutnya. Pada layar lebar, kelompokkan bidang yang digunakan bersama.
- Bungkus tabel lebar dengan `.table-responsive` agar gulir terjadi di area tabel. Pertahankan nama bidang dan tombol tindakan dalam tampilan sempit.
- Gunakan label form yang terhubung ke input. Tampilkan pesan validasi dekat bidang terkait dan tampilkan ringkasan jika beberapa baris impor bermasalah. Tulis status arsip dengan teks.
- Pakai tombol dan tautan sesuai fungsinya, beri nama yang jelas untuk tombol ikon, dan cek navigasi keyboard. Aksi utama mengikuti konteks halaman, seperti **Cari**, **Simpan**, **Konfirmasi Impor**, atau **Buat PDF**.
- Simpan penyesuaian CSS biasa dalam `app.css` dan muat setelah aset antarmuka. Layout dokumen memakai stylesheet cetak tersendiri sebagaimana ditetapkan pada [06 · Cetak Rapor F4](06-Cetak-Rapor-F4.md).

## Pemeriksaan tampilan

Periksa tampilan lebar dan sempit untuk tabel panjang, wrap label, urutan fokus keyboard, pesan validasi, hasil pencarian kosong, serta daftar cetak yang berisi arsip tersedia dan belum ditemukan. Periksa juga bahwa menu dan interaksi Bootstrap hanya bereaksi sekali per tindakan.
