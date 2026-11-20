<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Siswa::berandaPublik', ['as' => 'beranda-publik']);
$routes->group('arsip-cetak', static function (RouteCollection $routes): void {
    $routes->get('saran', 'Siswa::saran/dokumen-publik', ['as' => 'saran-publik']);
    $routes->get('siswa/(:num)', 'Siswa::dokumenPublik/$1', ['as' => 'dokumen-publik']);
    $routes->group('siswa/(:num)', ['filter' => 'kode-cetak'], static function (RouteCollection $routes): void {
        $routes->post('transkrip', 'Siswa::transkrip/$1', ['as' => 'transkrip-publik']);
        $routes->post('semua-semester', 'Siswa::cetakSemuaSemester/$1', ['as' => 'semua-semester-publik']);
        $routes->post('sampul', 'Siswa::sampul/$1', ['as' => 'sampul-publik']);
        $routes->post('identitas', 'Siswa::cetakIdentitas/$1', ['as' => 'identitas-publik']);
        $routes->post('semester/(:num)/nilai', 'Siswa::lembarNilai/$1/$2', ['as' => 'nilai-publik']);
        $routes->post('semester/(:num)/rapor', 'Siswa::cetakRapor/$1/$2', ['as' => 'rapor-publik']);
    });
});

$routes->get('login', 'AkunOperator::masuk', ['as' => 'masuk']);
$routes->post('login', 'AkunOperator::autentikasi', ['filter' => 'csrf']);
$routes->get('masuk', 'AkunOperator::masuk', ['as' => 'masuk-lama']);
$routes->post('masuk', 'AkunOperator::autentikasi', ['filter' => 'csrf']);
$routes->post('keluar', 'AkunOperator::keluar', ['as' => 'keluar', 'filter' => 'csrf']);
$routes->get('operator/kode-akses-cetak', 'PengaturanCetakPublik::index', ['as' => 'kode-akses-cetak']);
$routes->post('operator/kode-akses-cetak', 'PengaturanCetakPublik::simpan', ['filter' => 'csrf']);
$routes->get('operator', 'KelolaOperator::index', ['as' => 'daftar-operator']);
$routes->get('operator/tambah', 'KelolaOperator::tambah', ['as' => 'tambah-operator']);
$routes->post('operator/tambah', 'KelolaOperator::simpan', ['filter' => 'csrf']);
$routes->get('operator/(:num)/edit', 'KelolaOperator::edit/$1', ['as' => 'edit-operator']);
$routes->post('operator/(:num)/edit', 'KelolaOperator::simpan/$1', ['filter' => 'csrf']);
$routes->get('operator/(:num)/hapus', 'KelolaOperator::konfirmasiHapus/$1', ['as' => 'hapus-operator']);
$routes->post('operator/(:num)/hapus', 'KelolaOperator::hapus/$1', ['filter' => 'csrf']);
$routes->get('siswa', 'Siswa::index', ['as' => 'data-siswa']);
$routes->get('siswa/saran', 'Siswa::saran', ['as' => 'saran-siswa']);
$routes->get('siswa/(:num)', 'Siswa::show/$1', ['as' => 'detail-siswa']);
$routes->get('siswa/(:num)/riwayat', 'RiwayatArsip::index/$1', ['as' => 'riwayat-identitas']);
$routes->get('siswa/(:num)/semester/(:num)/riwayat', 'RiwayatArsip::index/$1/$2', ['as' => 'riwayat-rapor']);
$routes->get('siswa/(:num)/riwayat/perubahan/(:num)', 'RiwayatArsip::detail/$1/$2', ['as' => 'detail-riwayat']);
$routes->post('siswa/(:num)/riwayat/perubahan/(:num)', 'RiwayatArsip::pulihkan/$1/$2', ['filter' => 'csrf']);
$routes->get('siswa/(:num)/transkrip', 'Siswa::transkrip/$1', ['as' => 'cetak-transkrip']);
$routes->get('siswa/(:num)/cetak-semua-semester', 'Siswa::cetakSemuaSemester/$1', ['as' => 'cetak-semua-semester']);
$routes->get('siswa/(:num)/sampul', 'Siswa::sampul/$1', ['as' => 'cetak-sampul']);
$routes->get('siswa/(:num)/identitas', 'Siswa::cetakIdentitas/$1', ['as' => 'cetak-identitas']);
$routes->get('siswa/(:num)/edit', 'Siswa::edit/$1', ['as' => 'edit-siswa']);
$routes->post('siswa/(:num)/edit', 'Siswa::update/$1', ['filter' => 'csrf']);
$routes->get('siswa/(:num)/semester/(:num)', 'Siswa::semester/$1/$2', ['as' => 'detail-semester']);
$routes->get('siswa/(:num)/semester/(:num)/nilai', 'Siswa::lembarNilai/$1/$2', ['as' => 'cetak-lembar-nilai']);
$routes->get('siswa/(:num)/semester/(:num)/cetak', 'Siswa::cetakRapor/$1/$2', ['as' => 'cetak-rapor']);
$routes->get('siswa/(:num)/semester/(:num)/edit', 'Rapor::edit/$1/$2', ['as' => 'edit-rapor']);
$routes->post('siswa/(:num)/semester/(:num)/edit', 'Rapor::simpan/$1/$2', ['filter' => 'csrf']);
$routes->get('siswa/(:num)/semester/(:num)/excel', 'Rapor::excel/$1/$2', ['as' => 'excel-rapor']);
$routes->post('siswa/(:num)/semester/(:num)/impor/pratinjau', 'Rapor::pratinjau/$1/$2', ['as' => 'pratinjau-rapor', 'filter' => 'csrf']);
$routes->post('siswa/(:num)/semester/(:num)/impor/konfirmasi', 'Rapor::konfirmasi/$1/$2', ['as' => 'konfirmasi-rapor', 'filter' => 'csrf']);
$routes->get('cetak-rombel', 'Operator::cetakRombel', ['as' => 'cetak-rombel']);
$routes->get('cetak-rombel/pdf', 'Operator::pdfRombel', ['as' => 'pdf-rombel']);
$routes->get('cetak-rombel/leger', 'Operator::pdfLeger', ['as' => 'pdf-leger']);
