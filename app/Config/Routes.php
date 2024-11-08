<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'TaskController::index');
$routes->get('tasks', 'TaskController::getTasks');
$routes->get('tasks/(:num)', 'TaskController::getTaskById/$1');
$routes->post('tasks', 'TaskController::addTask');
$routes->put('tasks/(:num)', 'TaskController::updateTask/$1');
$routes->delete('tasks/(:num)', 'TaskController::deleteTask/$1');
$routes->post('tasks/update-status/(:num)', 'TaskController::updateTaskStatus/$1');


