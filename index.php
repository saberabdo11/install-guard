<?php


require_once __DIR__ . '/vendor/autoload.php';
use App\routes\Router;

$router = new Router();

// include routes 
require_once __DIR__ . '/routes/api.php';


$router->resolve();