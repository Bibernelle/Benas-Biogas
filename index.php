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


if (count($parts) == 2 && ($parts [0]) != "" && ($parts [1]) != "") {


    print_r($parts);
    $controllerName = $parts[1];

    $actionName = $parts[2];

    switch ($controllerName) {

        case 'Admin':
            $controller = new AdminController('Database.db');

            break;

        case 'Pages':

            $controller = new PagesController('Database.db');

            break;

        default:

            $controller = new PagesController('Database.db');

            $controller->home($request);
            break;

    }

    $response = $controller->{$actionName}($request);
} else {

    $controller = new PagesController('Database.db');

    $response = $controller->home($request);

}

$response->send();



