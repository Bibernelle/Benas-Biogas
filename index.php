<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once("class.Programm.php");
require_once("class.BaseController.php");
require_once("class.DataAccess.php");
require_once("class.AdminController.php");
require_once("class.PagesController.php");

$dal = new DataAccess("Database.db");
$dal->AddUser("michi", "krankarsch");
$dal->AddRole("Administrator");
$dal->AddRole("Employee");
$dal->AssignUserRole("michi", "Administrator");

require_once __DIR__ . '/vendor/autoload.php';


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$request = Request::createFromGlobals();

// $uri = "Pages/home"
$uri = $request->getPathInfo();

$parts = explode("/", $uri);

$controllerName = $parts[1];

$actionName = $parts[2];

print($uri);
switch ($controllerName) {

    case 'Admin':
        $controller = new AdminController('Database.db');

        break;

    case 'Pages':

        $controller = new PagesController('Database.db');

        break;

    default:
        echo "Der Controller:  " . $controllerName . " wurde nicht gefunden!";
        die();

        break;

}

$response = $controller->{$actionName}($request);


$response->send();



