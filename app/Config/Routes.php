<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;
use Config\Services;

/** @var RouteCollection $routes */
$routes = Services::routes();

// --------------------------------------------------------------------
// Router Setup
// --------------------------------------------------------------------
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

// --------------------------------------------------------------------
// Public (no login required)
// --------------------------------------------------------------------
$routes->get('/',            'Home::index');
$routes->get('testdb',       'TestDb::index');

// Auth
$routes->get('login',        'Auth::loginForm',    ['as' => 'login']);
$routes->post('login',       'Auth::login');
$routes->get('register',     'Auth::registerForm', ['as' => 'register']);
$routes->post('register',    'Auth::register');
$routes->get('logout',       'Auth::logout');

// --------------------------------------------------------------------
// Protected Routes (must be logged in)
// --------------------------------------------------------------------
$routes->group('', ['filter' => 'auth'], function($routes){

    // Surveys
    $routes->get('survey',                   'Survey::index');
    $routes->get('survey/new',               'Survey::new');
    $routes->post('survey/create',           'Survey::create');
    $routes->get('survey/(:num)',            'Survey::show/$1');
    $routes->get('survey/(:num)/edit',       'Survey::edit/$1');
    $routes->post('survey/update/(:num)',    'Survey::update/$1');
    $routes->post('survey/delete/(:num)',    'Survey::delete/$1');

    // Sections (AJAX)
    $routes->post('section/create',          'Section::create');
    $routes->post('section/update/(:num)',   'Section::update/$1');
    $routes->post('section/delete/(:num)',   'Section::delete/$1');

    // Questions
    $routes->get('question',                 'Question::index');
    $routes->get('question/new',             'Question::new');
    $routes->post('question/create',         'Question::create');
    $routes->get('question/(:num)',          'Question::show/$1');
    $routes->get('question/(:num)/edit',     'Question::edit/$1');
    $routes->post('question/update/(:num)',  'Question::update/$1');
    $routes->post('question/delete/(:num)',  'Question::delete/$1');

    // Answers (survey takers)
    $routes->get('answer',                   'Answer::index');
    $routes->get('answer/(:num)',            'Answer::takeSurvey/$1');
    $routes->post('answer/(:num)',           'Answer::saveAnswer/$1');

    //User
    $routes->get('profile',       'User::profile');
    $routes->post('profile',      'User::updateProfile');

    //Notification
    $routes->post('notifications/mark-read', 'Notification::markAllRead', ['filter' => 'auth']);
    $routes->get('notifications', 'Notification::index', ['filter'=>'auth']);


    // Reports (all logged-in users)
    $routes->get('report/individual',        'Report::individual');

    // ----------------------------------------------------------------
    // Profile (only regular users)
    // ----------------------------------------------------------------
    $routes->group('', ['filter' => 'role:user'], function($routes){
        $routes->get('profile',           'User::profile');
        $routes->post('profile',          'User::updateProfile');
    });

    // ----------------------------------------------------------------
    // Admin-only
    // ----------------------------------------------------------------
    $routes->group('', ['filter' => 'role:admin'], function($routes){
        // User Management
        $routes->get('user',               'User::index');
        $routes->get('user/new',           'User::new');
        $routes->post('user/create',       'User::create');
        $routes->get('user/(:num)/edit',   'User::edit/$1');
        $routes->post('user/update/(:num)','User::update/$1');
        $routes->post('user/delete/(:num)','User::delete/$1');
        $routes->post('user/notify/(:num)','User::notify/$1');
        $routes->get('user/(:num)',        'User::show/$1');
        $routes->get('user/(:num)/edit',   'User::edit/$1');
        $routes->post('user/update/(:num)', 'User::update/$1');     

        // Cumulative Report
        $routes->get('report/cumulative',  'Report::cumulative');
    });

});
