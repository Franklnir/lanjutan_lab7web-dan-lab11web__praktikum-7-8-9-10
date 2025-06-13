<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Nonaktifkan auto routing legacy (disarankan demi keamanan)
$routes->setAutoRoute(false);

// Routing untuk halaman utama
$routes->get('/', 'Home::index');

// Routing untuk halaman statis dari Page controller
$routes->get('/contact', 'Page::contact');
$routes->get('/faqs', 'Page::faqs');
$routes->get('/tos', 'Page::tos');




// Rute untuk AJAX
$routes->get('ajax', 'AjaxController::index');
$routes->get('ajax/getData', 'AjaxController::getData');
$routes->post('ajax/create', 'AjaxController::create');
$routes->post('ajax/update/(:num)', 'AjaxController::update/$1');
$routes->delete('ajax/delete/(:num)', 'AjaxController::delete/$1'); // Menggunakan DELETE method

// Routing artikel publik
$routes->get('/about', 'Artikel::about');
$routes->get('/artikel', 'Artikel::index');
$routes->get('/artikel/(:any)', 'Artikel::view/$1');
$routes->get('/artikel', 'Artikel::index');
$routes->get('/artikel/(:segment)', 'Artikel::detail/$1');
$routes->get('/', 'ArtikelController::index');
$routes->get('/artikel/cari', 'ArtikelController::cari');

$routes->get('/admin/artikel/create', 'Artikel::create'); // Form tambah artikel
$routes->post('/admin/artikel/create', 'Artikel::create'); // Simpan artikel

$routes->get('/ajax/getCategories', 'AjaxController::getCategories'); // New route for categories
$routes->post('/ajax/update/(:num)', 'AjaxController::update/$1'); // Tetap POST untuk update karena ada file
$routes->delete('/ajax/delete/(:num)', 'AjaxController::delete/$1');
$routes->post('/ajax/removeImage/(:num)', 'AjaxController::removeImage/$1'); // Route baru untuk menghapus gambar

// Routing user (login & logout)
$routes->match(['get', 'post'], 'login', 'Auth::login');
$routes->get('logout', 'Auth::logout');

$routes->get('user/login', 'User::login');
$routes->post('user/login', 'User::login');
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::register');

$routes->get('user/logout', 'User::logout');


// Routing admin dengan filter auth
$routes->group('admin', ['filter' => 'auth'], function($routes) {
    $routes->get('artikel', 'Artikel::admin_index');
    $routes->add('artikel/add', 'Artikel::add');
    $routes->add('artikel/edit/(:any)', 'Artikel::edit/$1');
    
    $routes->get('artikel/delete/(:any)', 'Artikel::delete/$1');
});
