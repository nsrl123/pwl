<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index', ['filter' => 'auth']);

$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');

$routes->group('produk', ['filter' => 'auth'], function ($routes) { 
    $routes->get('', 'ProdukController::index');
    $routes->post('', 'ProdukController::create');
    $routes->post('edit/(:any)', 'ProdukController::edit/$1');
    $routes->get('delete/(:any)', 'ProdukController::delete/$1');
    $routes->get('download', 'ProdukController::download');
});

$routes->group('keranjang', ['filter' => 'auth'], function ($routes) {
    $routes->get('', 'TraksaksiController::index');
    $routes->post('', 'TraksaksiController::cart_add');
    $routes->post('edit', 'TraksaksiController::cart_edit');
    $routes->get('delete/(:any)', 'TraksaksiController::cart_delete/$1');
    $routes->get('clear', 'TraksaksiController::cart_clear');
}); 

$routes->get('ajax/destinations', 'TraksaksiController::destinations', ['filter' => 'auth']);
$routes->get('ajax/costs','TraksaksiController::costs', ['filter' => 'auth']);
$routes->post('buy', 'TraksaksiController::buy', ['filter' => 'auth']);
$routes->get('checkout', 'TraksaksiController::checkout', ['filter' => 'auth']);
$routes->get('produk', 'ProdukController::index', ['filter' => 'auth']);
$routes->get('keranjang', 'TraksaksiController::index', ['filter' => 'auth']);
$routes->get('profil', 'Home::profilPengguna', ['filter' => 'auth']);
