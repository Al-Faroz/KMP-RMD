<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Siswa::index');
$routes->get('siswa', 'Siswa::index', ['as' => 'data-siswa']);
$routes->get('siswa/saran', 'Siswa::saran', ['as' => 'saran-siswa']);
$routes->get('siswa/(:num)', 'Siswa::show/$1', ['as' => 'detail-siswa']);
$routes->get('siswa/(:num)/edit', 'Siswa::edit/$1', ['as' => 'edit-siswa']);
$routes->post('siswa/(:num)/edit', 'Siswa::update/$1', ['filter' => 'csrf']);
$routes->get('siswa/(:num)/semester/(:num)', 'Siswa::semester/$1/$2', ['as' => 'detail-semester']);
$routes->get('cetak-rombel', 'Operator::cetakRombel', ['as' => 'cetak-rombel']);
