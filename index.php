<?php
define("CSRF_TOKEN",     "CSRF_TOKEN");
date_default_timezone_set("Europe/Berlin");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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


if (count($parts) == 3 && ($parts [1]) != "" && ($parts [2]) != "") {


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

            header('Location: index.php/Pages/home', true, 301);
            break;

    }

    $response = $controller->{$actionName}($request);
    $response->send();
} else {

    header('Location: index.php/Pages/home', true, 301);

}





