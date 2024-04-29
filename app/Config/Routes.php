<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->post('/api/login', 'Authentication::login');
$routes->post('/api/register', 'Authentication::register');
$routes->post('/api/forgot-password', 'Authentication::forgotPassword');

$routes->post('/api/image', 'Authentication::uploadImage');
$routes->get('/api/auth-face', 'Authentication::compareImages');
