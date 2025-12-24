<?php

namespace Dhiva\Core;

use CodeIgniter\Router\RouteCollection as BaseRouteCollection;

class DhivaRoutes extends BaseRouteCollection
{
    static function Route($routes, $path, $controller)
    {
        if (isset($_SERVER['REQUEST_METHOD'])) {
            $routes->post('endpoencode', 'EndpointController::encodeEndpoint');
            $routes->post('endpodecode', 'EndpointController::checkEncodeEndpoint');
            $routes->get('image/(:segment)', 'AssetsController::decodeImage/$1');

            $routes->post(Keys::$route . '/(:segment)', 'RoutesController::decodeEndpointPost/$1');
            $routes->get(Keys::$route . '/(:segment)', 'RoutesController::decodeEndpointGet/$1');
            $routes->put(Keys::$route . '/(:segment)', 'RoutesController::decodeEndpointPut/$1');
            $routes->delete(Keys::$route . '/(:segment)', 'RoutesController::decodeEndpointDelete/$1');

            $routes->post(Keys::$route . '/(:segment)/(:segment)', 'RoutesController::decodeEndpointPost/$1/$2');
            $routes->get(Keys::$route . '/(:segment)/(:segment)', 'RoutesController::decodeEndpointGet/$1/$2');
            $routes->put(Keys::$route . '/(:segment)/(:segment)', 'RoutesController::decodeEndpointPut/$1/$2');
            $routes->delete(Keys::$route . '/(:segment)/(:segment)', 'RoutesController::decodeEndpointDelete/$1/$2');

            if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                $routes->get($path, $controller . "::index");
                $routes->get($path . '/(:segment)', $controller . '::showAll/$1');
                $routes->get($path . '/pages/(:segment)/(:segment)', $controller . '::pagination/$1/$2');
                $routes->get($path . '/pagesbydate/(:segment)/(:segment)/(:segment)/(:segment)', $controller . '::paginationByDate/$1/$2/$3/$4');
                $routes->get($path . '/show_by/(:segment)/(:segment)', $controller . '::showBy/$1/$2');
                $routes->get($path . '/all_by/(:segment)/(:segment)', $controller . '::allBy/$1/$2');
            } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $routes->post($path, $controller . "::create");
                $routes->post($path . '/all-by', $controller . '::allByPost');
                $routes->post($path . '/show-by', $controller . '::showByPost');
                $routes->post($path . '/update/(:segment)', $controller . '::update/$1');
                $routes->post($path . '/pagination', $controller . '::paginationpost');
            } else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
                $routes->delete($path . '/delete/(:segment)', $controller . '::destroy/$1');
            }
        }
    }
}
