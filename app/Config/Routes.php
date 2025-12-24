<?php

use Dhiva\Core\DhivaRoutes;

$routes->set404Override('App\Controllers\RoutesController::notfound');
$routes->get('/', 'DevController::sukses');
$routes->get('/sukses', 'DevController::sukses');
$routes->get('sukses/(:segment)', 'DevController::sukses/$1');
$routes->post('auth', 'SuperUserController::auth');
$routes->get('logout', 'SuperUserController::logout');

DhivaRoutes::Route($routes, 'super-user', 'SuperUserController');
DhivaRoutes::Route($routes, 'group', 'SuperUserGroupController');
DhivaRoutes::Route($routes, 'endpoint', 'EndpointController');
DhivaRoutes::Route($routes, 'super-group', 'SuperGroupController');
DhivaRoutes::Route($routes, 'tool', 'ToolController');
DhivaRoutes::Route($routes, 'unit', 'UnitController');
DhivaRoutes::Route($routes, 'status', 'StatusController');
DhivaRoutes::Route($routes, 'application', 'ApplicationController');
