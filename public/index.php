<?php

declare(strict_types=1);

use App\App;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method');
header('Access-Control-Allow-Methods: GET, POST, PATCH, OPTIONS, PUT, DELETE');
header('Allow: GET, POST, PATCH, OPTIONS, PUT, DELETE');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/helpers/helpers.php';
require __DIR__ . '/../app/routes/api.php';

$method = $_SERVER['REQUEST_METHOD'];
if ($method == 'OPTIONS') {
    die();
}

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();


$app = new App();
$app->send();
