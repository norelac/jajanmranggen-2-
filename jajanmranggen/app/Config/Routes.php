<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
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

// Admin routes (protected)
$routes->group('admin', ['filter' => 'admin'], function ($routes) {
    $routes->get('dashboard', 'Admin\Dashboard::index');
    $routes->get('kuliner', 'Admin\KulinerAdmin::index');
    $routes->post('kuliner/approve/(:num)', 'Admin\KulinerAdmin::approve/$1');
    $routes->post('kuliner/reject/(:num)', 'Admin\KulinerAdmin::reject/$1');
    $routes->get('kategori', 'Admin\Kategori::index');
    $routes->get('users', 'Admin\Users::index');
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
});

// API routes
$routes->group('api', ['filter' => 'apikey'], function ($routes) {
    $routes->get('kuliner', 'Api\KulinerApi::index');
});
$routes->post('api/payment/notification', 'Api\PaymentNotification::handle');