<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Operator::siswa');
$routes->get('siswa', 'Operator::siswa', ['as' => 'data-siswa']);
$routes->get('cetak-rombel', 'Operator::cetakRombel', ['as' => 'cetak-rombel']);
