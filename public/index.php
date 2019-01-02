<?php
require __DIR__."/../vendor/autoload.php";

$dotenv = new Dotenv\Dotenv(__DIR__."/../");
$dotenv->load();
$dotenv->required(['DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD', 'DB_TABLE']);

$uri = getUri();

$config = require __DIR__."/../config.php";
$shortener = UrlShortener\UrlShortener::getInstance($config['db'], $config['options']);

$long = $shortener->toLong($uri);
if ($long != false) {
    header("Location: ".$long, true, 302);
    exit;
}

http_response_code(404);
