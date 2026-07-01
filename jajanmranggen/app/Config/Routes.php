<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Auth routes
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::loginPost');
$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::registerPost');
$routes->get('/logout', 'Auth::logout');

// Public routes
$routes->get('/', 'Home::index');
$routes->get('/kuliner', 'Kuliner::index');
$routes->get('/kuliner/(:segment)', 'Kuliner::show/$1');
$routes->post('/kuliner/storeReview', 'Kuliner::storeReview');

// Payment invoice public (bukti pembayaran)
$routes->get('payment/invoice/(:segment)', 'PaymentInvoice::show/$1');

// Favorites (AJAX, requires auth)
$routes->post('/favorites/toggle', 'Favorites::toggle', ['filter' => 'auth']);
$routes->get('/favorites', 'Favorites::index', ['filter' => 'auth']);

// Admin routes (protected)
$routes->get('/admin', 'Admin\Dashboard::index', ['filter' => 'admin']);
$routes->group('admin', ['filter' => 'admin'], function ($routes) {
    $routes->get('dashboard', 'Admin\Dashboard::index');

    //
    // Kuliner moderation
    $routes->get('kuliner', 'Admin\KulinerAdmin::index');
    $routes->get('kuliner/(:num)', 'Admin\KulinerAdmin::show/$1');
    $routes->post('kuliner/approve/(:num)', 'Admin\KulinerAdmin::approve/$1');
    $routes->post('kuliner/reject/(:num)', 'Admin\KulinerAdmin::reject/$1');
    $routes->post('kuliner/delete/(:num)', 'Admin\KulinerAdmin::delete/$1');

    // Kategori CRUD
    $routes->get('kategori', 'Admin\Kategori::index');
    $routes->get('kategori/create', 'Admin\Kategori::create');
    $routes->post('kategori/store', 'Admin\Kategori::store');
    $routes->get('kategori/edit/(:num)', 'Admin\Kategori::edit/$1');
    $routes->post('kategori/update/(:num)', 'Admin\Kategori::update/$1');
    $routes->post('kategori/delete/(:num)', 'Admin\Kategori::delete/$1');

    // Tags CRUD
    $routes->get('tags', 'Admin\Tags::index');
    $routes->get('tags/create', 'Admin\Tags::create');
    $routes->post('tags/store', 'Admin\Tags::store');
    $routes->get('tags/edit/(:num)', 'Admin\Tags::edit/$1');
    $routes->post('tags/update/(:num)', 'Admin\Tags::update/$1');
    $routes->post('tags/delete/(:num)', 'Admin\Tags::delete/$1');

    // Users management
    $routes->get('users', 'Admin\Users::index');
    $routes->post('users/delete/(:num)', 'Admin\Users::delete/$1');

    // Reviews moderation
    $routes->get('reviews', 'Admin\Reviews::index');
    $routes->post('reviews/delete/(:num)', 'Admin\Reviews::delete/$1');

    // Payments
    $routes->get('payments', 'Admin\Payments::index');
});

// Contributor routes (protected)
$routes->group('contributor', ['filter' => 'contributor'], function ($routes) {
    $routes->get('dashboard', 'Contributor\Dashboard::index');
    $routes->get('kuliner', 'Contributor\KulinerContributor::index');
    $routes->get('kuliner/create', 'Contributor\KulinerContributor::create');
    $routes->post('kuliner/store', 'Contributor\KulinerContributor::store');
    $routes->get('kuliner/edit/(:num)', 'Contributor\KulinerContributor::edit/$1');
    $routes->post('kuliner/update/(:num)', 'Contributor\KulinerContributor::update/$1');
    $routes->post('kuliner/delete/(:num)', 'Contributor\KulinerContributor::delete/$1');
    $routes->get('kuliner/geocode', 'Contributor\KulinerContributor::geocode');
    $routes->get('payment/sponsor/(:num)', 'Contributor\Payment::sponsor/$1');
    $routes->post('payment/checkout', 'Contributor\Payment::checkout');
    $routes->get('payment/finish', 'Contributor\Payment::finish');
    $routes->get('payment/check-status/(:segment)', 'Contributor\Payment::checkStatus/$1');
});
// 
// API routes (public endpoint + key protected)
$routes->group('api', ['filter' => 'apikey'], function ($routes) {
    $routes->get('kuliner', 'Api\KulinerApi::index');
});
$routes->post('api/payment/notification', 'Api\PaymentNotification::handle');