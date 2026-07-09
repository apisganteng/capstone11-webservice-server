<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index', ['filter' => 'auth']);

// Route untuk autentikasi (Login/Logout)
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');

// Route khusus untuk fitur Keranjang Belanja
$routes->group('keranjang', ['filter' => 'auth'], function ($routes) {
    // Menampilkan isi keranjang
    $routes->get('/', 'TransaksiController::index');
    
    // Menambah, mengedit, menghapus isi keranjang
    $routes->post('/', 'TransaksiController::cart_add');
    $routes->post('edit', 'TransaksiController::cart_edit');
    $routes->get('delete/(:any)', 'TransaksiController::cart_delete/$1');
    $routes->get('clear', 'TransaksiController::cart_clear');
});

// Route halaman profil
$routes->get('profile', 'ProfileController::index', ['filter' => 'auth']);

// Route halaman produk & fitur tambah/hapus produk
$routes->get('produk', 'ProdukController::index', ['filter' => 'auth']);
$routes->post('produk', 'ProdukController::store', ['filter' => 'auth']); 
$routes->post('produk/edit/(:any)', 'ProdukController::update/$1', ['filter' => 'auth']);
$routes->get('produk/delete/(:any)', 'ProdukController::delete/$1', ['filter' => 'auth']);
$routes->post('produk/delete/(:any)', 'ProdukController::delete/$1', ['filter' => 'auth']);


// Route halaman checkout & proses pesanan
$routes->get('checkout', 'TransaksiController::checkout', ['filter' => 'auth']);
$routes->post('transaksi/buat_pesanan', 'TransaksiController::buat_pesanan', ['filter' => 'auth']);
$routes->post('buy', 'TransaksiController::buy', ['filter' => 'auth']); 

//history
$routes->get('history', 'TransaksiController::history');

// Route API RajaOngkir Komerce
$routes->get('api/cari_lokasi', 'RajaOngkirController::cari_lokasi');
$routes->post('api/cost', 'RajaOngkirController::hitung_ongkir');

$routes->get('ajax/destinations','TransaksiController::destinations', ['filter' => 'auth']);
$routes->get('ajax/costs','TransaksiController::costs', ['filter' => 'auth']);

$routes->resource('api/products', ['controller' => 'Api\ProdukController']);
$routes->get('api/transactions', 'Api\TransaksiController::index');