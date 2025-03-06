<?php

declare(strict_types=1);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method');
header('Access-Control-Allow-Methods: GET, POST, PATCH, OPTIONS, PUT, DELETE');
header('Allow: GET, POST, PATCH, OPTIONS, PUT, DELETE');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/helpers/helpers.php';
require __DIR__ . '/../app/routes/web.php';

$method = $_SERVER['REQUEST_METHOD'];
if ($method == 'OPTIONS') {
    die();
}
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();


$app = new Api\Maicoldev\App();
$app->send();
