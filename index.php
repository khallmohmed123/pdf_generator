<?php
define("Application_path",realpath(__DIR__));
require Application_path.'/vendor/autoload.php';
include Application_path . "/vendor/shared.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    include Application_path . '/Routes/Routes.php';
    Routes::Resolve();
}
catch (\Throwable $e)
{
    echo "<pre />";
    var_dump($e->getMessage());
    var_dump($e->getLine());
    var_dump($e->getTrace());
    echo "<pre />";
}
