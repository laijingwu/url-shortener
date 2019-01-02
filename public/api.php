<?php
require __DIR__."/../vendor/autoload.php";
header("Content-Type: application/json");

$raw_post_data = file_get_contents('php://input', 'r');
$data = json_decode($raw_post_data);
if (!$data || !isset($data->type) || !isset($data->uri)) {
    echo json_encode([
        'code' => 1000,
        'errmsg' => "invalid request."
    ]);
    exit;
}

$dotenv = new Dotenv\Dotenv(__DIR__."/../");
$dotenv->load();
$dotenv->required(['DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD', 'DB_TABLE']);
$config = require __DIR__."/../config.php";
$shortener = UrlShortener\UrlShortener::getInstance($config['db'], $config['options']);

$type = $data->type;
$uri = $data->uri;
if ($type == "short") {
    $long = $shortener->toLong($uri);
    if ($long == false) {
        echo json_encode([
            'code' => 1001,
            'errmsg' => "invalid short uri."
        ]);
        exit;
    }
    echo json_encode([
        'code' => 0,
        'data' => $long
    ]);
} elseif ($type == "long") {
    $short = $shortener->toShort($uri);
    if ($short == false) {
        echo json_encode([
            'code' => -1,
            'errmsg' => "database error."
        ]);
        exit;
    }
    echo json_encode([
        'code' => 0,
        'data' => $short
    ]);
} else {
    echo json_encode([
        'code' => 1000,
        'errmsg' => "invalid request."
    ]);
    exit;
}

