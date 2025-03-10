<?php

declare(strict_types=1);

use App\App;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Authorization, X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Access-Control-Allow-Headers');
header('Access-Control-Allow-Methods: GET, POST, PATCH, OPTIONS, PUT, DELETE');
header('Access-Control-Max-Age: 86400');
header('Access-Control-Allow-Credentials: true');

// Manejo de preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header('HTTP/1.1 200 OK');
    exit();
}

date_default_timezone_set('America/Bogota');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/helpers/helpers.php';
require __DIR__ . '/../app/routes/api.php';


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$app = new App();
$app->send();
