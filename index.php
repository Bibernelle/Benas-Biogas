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
$uri = $request->getPathInfo();


if (null != $request->query->get('Controller')) {
    switch ($request->query->get('Controller')) {
        case 'Admincontroller':
            $controller = new Admincontroller('Database.db');
            switch ($request->query->get('Action')) {
                case 'CreateUser':
                    $response = $controller->CreateUser($request);
                    break;
                case 'LoginUser':
                    $response = $controller->LoginUser($request);
                    break;
                case 'AssignRole':
                    $response = $controller->AssignRole($request);
                    break;
                case 'DeleteUser':
                    $response = $controller->DeleteUser($request);
                    break;
                case 'LogoutUser':
                    $response = $controller->LogoutUser($request);
                    break;

                default:

                    echo "Die Action:  " . $request->query->get('Action') . " wurde nicht gefunden!";
                    die();

                    break;
            }
            break;

        case 'PagesController':

            $controller = new PagesController('Database.db');
            switch ($request->query->get('Action')) {
                case 'home':

                    $response = $controller->home($request);

                    break;

                default:

                    echo "Die Action:  " . $request->query->get('Action') . " wurde nicht gefunden!";
                    die();

                    break;
            }

            break;
        default:

            echo "Der Controller:  " . $request->query->get('Controller') . " wurde nicht gefunden!";
            die();

break;
    }

}

$response->send();



