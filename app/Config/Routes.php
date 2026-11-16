<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Siswa::index');
$routes->get('siswa', 'Siswa::index', ['as' => 'data-siswa']);
$routes->get('siswa/saran', 'Siswa::saran', ['as' => 'saran-siswa']);
$routes->get('siswa/(:num)', 'Siswa::show/$1', ['as' => 'detail-siswa']);
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
