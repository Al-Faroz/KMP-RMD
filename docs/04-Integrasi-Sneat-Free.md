# Integrasi Sneat Free

RMD menggunakan **Sneat – Free Responsive Bootstrap 5 HTML5 Admin Template** dari ThemeSelection. Jadikan layout menu vertikal edisi Free sebagai dasar tampilan operator dan gunakan komponen yang tersedia dalam edisi tersebut. [Repository Sneat Free](https://github.com/themeselection/sneat-bootstrap-html-admin-template-free) · [Demo resmi](https://demos.themeselection.com/sneat-bootstrap-html-admin-template-free/html/)

## Layout operator

| Bagian | Rancangan |
|---|---|
| **Menu vertikal** | Tampilkan identitas RMD dan menu **Data Siswa** serta **Cetak Rombel**. Sorot menu aktif pada halaman detail yang terkait. |
| **Bilah atas** | Gunakan judul atau konteks halaman serta kontrol buka/tutup menu saat layar sempit. |
| **Konten** | Tampilkan form, tabel, dan pesan dari view CI4. **Data Siswa** menjadi halaman awal. |
| **Footer** | Letakkan atribusi ThemeSelection sesuai panduan distribusi Sneat Free. |

## Integrasi view dan aset

1. Pilih satu tag atau commit edisi Free. Catat sumber, versi atau commit, dan versi Bootstrap yang dipakai oleh rilis tersebut.
2. Jadikan layout HTML Sneat sebagai layout CI4, misalnya `app/Views/layouts/operator.php`. Pindahkan menu, bilah atas, pesan aplikasi, dan footer menjadi partial yang dipakai halaman operator.
3. Gunakan aset terbitan Sneat yang diperlukan dan simpan di `public/assets/sneat/`. Pertahankan urutan CSS dan JavaScript yang diperlukan rilis itu, kemudian muat aset RMD di lapisan aplikasi.
4. Arahkan referensi aset ke URL aset CI4, termasuk konfigurasi jalur aset jika diperlukan oleh rilis tersebut. Muat satu runtime Bootstrap yang sesuai agar interaksi seperti dropdown dan collapse terdaftar satu kali.
5. Simpan lisensi yang menyertai paket. README resmi menyebut lisensi MIT dan meminta atribusi ThemeSelection pada footer. [Sneat Free · Lisensi dan atribusi](https://github.com/themeselection/sneat-bootstrap-html-admin-template-free#license-)

## Kriteria tahap fondasi

- Halaman awal menampilkan **Data Siswa**. Menu **Cetak Rombel** membuka halaman yang sesuai.
- Menu vertikal berfungsi di layar lebar dan dapat dibuka serta ditutup pada layar sempit. Status menu aktif mengikuti halaman.
- CSS, ikon, dan JavaScript yang digunakan termuat dari jalur aset proyek di XAMPP.
- View cetak menggunakan layout dokumen dan stylesheet cetak RMD. Detailnya dicatat pada [06 · Cetak Rapor F4](06-Cetak-Rapor-F4.md).

Komponen form dan tabel mengikuti [03 · Standar Bootstrap 5](03-Standar-Bootstrap-5.md).
