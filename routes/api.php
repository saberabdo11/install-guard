<?php

use App\controllers\InstallationController;
use App\routes\Router;

$router = new Router();


$router->post('/api/install', [InstallationController::class, 'install']);

$router->post('/api/install/completed', [InstallationController::class, 'updateStatus']);

$router->post('/api/install/check', [InstallationController::class, 'checkDomainInstallation']);