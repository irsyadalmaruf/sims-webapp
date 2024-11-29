<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection 
 */
$routes->get('/login', 'Auth::login');
$routes->post('/auth/process', 'Auth::process'); 
$routes->get('/logout', 'Auth::logout');

$routes->get('/', 'Produk::index'); 
$routes->get('/produk', 'Produk::index'); 
$routes->post('/produk/filter', 'Produk::filter');
$routes->get('/produk/create', 'Produk::create'); 
$routes->post('/produk/store', 'Produk::store');  
$routes->get('/produk/edit/(:num)', 'Produk::edit/$1'); 
$routes->post('/produk/update/(:num)', 'Produk::update/$1');
$routes->post('/produk/delete/(:num)', 'Produk::delete/$1');
$routes->get('/produk/exportExcel', 'Produk::exportExcel');
$routes->get('/profil', 'Profil::index'); 
$routes->post('/profil/update', 'Profil::update');