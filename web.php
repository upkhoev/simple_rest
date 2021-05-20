<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include 'app/bootstrap.php';


try {

    $apiName = array_shift(explode('/', trim($_REQUEST['action'],'/')));
    if (!$apiName) {
        throw new Exception('collection not found');
    }
    $connection = new \App\Db();
    $className = "\\App\\Collection\\". ucfirst($apiName);
    if (!class_exists($className)) {
        throw new Exception("Collection $apiName not found");
    }
    $api = new $className();
    $api->setDb($connection);
    echo $api->run();
} catch (Exception $e) {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(['error' => $e->getMessage()]);
}
