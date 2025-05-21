<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');
$routes->get('testdb', 'TestDb::index');

// Disable auto-routing for security
$routes->setAutoRoute(false);

// REST-style resource controllers
$routes->resource('survey');
$routes->resource('question');
$routes->resource('answer');
$routes->resource('user');

// Auth routes
$routes->get('auth/login', 'Auth::login');
$routes->post('auth/login', 'Auth::login');
$routes->get('auth/logout', 'Auth::logout');

// Survey custom routes (safe and clean)
$routes->get('survey', 'Survey::index');                // Show all surveys
$routes->get('survey/new', 'Survey::new');              // Show form to create survey
$routes->post('survey/create', 'Survey::create');       // Handle creation
$routes->get('survey/edit/(:num)', 'Survey::edit/$1');  // Edit survey by ID
$routes->get('survey/show/(:num)', 'Survey::show/$1');  // View single survey
$routes->post('survey/update/(:num)', 'Survey::update/$1');
$routes->post('question/create', 'Question::create');
$routes->delete('question/delete/(:num)', 'Question::delete/$1');

// Optional test route (safe fallback)
$routes->get('survey/test', function () {
    echo "🎉 Welcome to the survey dashboard!";
});
