# Tahapan Pengembangan dan QA

## Baseline lokal

CI4 sudah terpasang di `E:\xampp\htdocs\kmp-rdm`. Pengguna sudah menguji URL lokal dan melihat halaman **Welcome CodeIgniter**. Lanjutkan Tahap 1 dari checkout ini. Konfigurasi database `kmp-rdm` sudah diisi di `.env` menurut pengguna. Uji koneksi dari CI4 dilakukan pada Tahap 2.

## Usulan tahapan pengembangan

| Tahap | Ruang lingkup | Kriteria uji di XAMPP |
|---|---|---|
| **1. Fondasi aplikasi** | Integrasikan layout operator Sneat Free dan Bootstrap 5 ke proyek CI4 yang sudah berjalan di XAMPP. | Navigasi **Data Siswa** dan **Cetak Rombel** menuju halaman yang sesuai. Menu berfungsi pada layar lebar dan sempit. Periksa pembatasan HTTP untuk file dan direktori internal. |
| **2. Database dan data awal** | Uji koneksi CI4 ke `kmp-rdm`, siapkan skema, dan muat workbook awal per angkatan. | Database aktif adalah `kmp-rdm`. Jumlah impor cocok dengan tabel pembanding. ID sumber yang sama pada dua angkatan memuat entri berbeda. Relasi siswa, rapor, nilai dan data pendukung sesuai. |
| **3. Data Siswa dan riwayat** | Pencarian, filter angkatan, detail siswa, semester 1–6, serta edit identitas. | Pencarian dan filter membuka siswa yang tepat. Perubahan identitas muncul pada detail siswa dan tercermin di cetak saat fitur cetak tersedia. |
| **4. Pemutakhiran rapor** | Edit Rapor, unduh Excel, impor dengan pratinjau, dan penambahan arsip yang ditemukan. | Uji isi baru, penggantian nilai, angka nol, sel kosong, dan file untuk siswa atau semester lain. Pastikan perubahan masuk ke semester yang dipilih. |
| **5. Cetak satu semester** | Implementasi layout F4 mulai dari KHS1, lalu KHS2 dan RDM Kurikulum Merdeka. Tambahkan RDM K13 setelah acuannya bisa diverifikasi. | Cocokkan setiap format dengan sumber yang relevan. Periksa hasil koreksi identitas dan nilai, pemisah halaman, teks panjang, dan tanda tangan. |
| **6. Cetak gabungan** | Cetak semua semester satu siswa dan satu rombel satu semester. | Uji satu siswa angkatan 2017 dengan format campuran serta hasil cetak rombel yang memuat format berbeda jika datanya tersedia. Uji satu rombel yang sebagian arsipnya belum tersedia. Urutan semester, format per rapor, dan jumlah rapor di PDF sesuai pilihan. |
| **7. Cetak dokumen terpisah dan regresi** | Tindakan cetak sampul, halaman identitas, dan dokumen pendukung yang telah dipetakan. Uji ulang alur pencarian, edit, impor, dan cetak. | Tindakan menghasilkan dokumen yang dipilih. Ketiga pilihan **Cetak Rapor** menghasilkan halaman rapor semester di F4. |

### Cara kerja per tahap

1. Gunakan Master Dokumen yang disetujui sebagai acuan. Jika keputusan bisnis berubah, perbarui dokumen sebelum menyesuaikan kode.
2. Kerjakan satu tahap melalui branch dan PR. Sertakan instruksi pull, cara uji XAMPP, serta migrasi atau patch SQL jika tahap tersebut mengubah database.
3. Pengguna menguji di localhost dan menyampaikan hasil. Perbaiki temuan pada branch yang sama. Setelah pengguna menyatakan **FIX**, lakukan merge sesuai alur GitHub dan lanjutkan tahap berikutnya.

## Serah terima setiap tahap

Catat ruang lingkup PR, instruksi uji, hasil pemeriksaan, dan temuan yang masih terbuka. Acuan fondasi ada di [02 · Arsitektur CodeIgniter 4](02-Arsitektur-CodeIgniter-4.md), [03 · Standar Bootstrap 5](03-Standar-Bootstrap-5.md), dan [04 · Integrasi Sneat Free](04-Integrasi-Sneat-Free.md).
