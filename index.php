<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once("class.Programm.php");
<<<<<<< HEAD
//require_once("class.Controller.php");
require_once("class.DataAccess.php");
require_once("class.AdminController.php");
=======
require_once("class.BaseController.php");
require_once("class.DataAccess.php");
require_once("class.AdminController.php");
require_once("class.PagesController.php");

>>>>>>> 7f20ead68cadab5d2ca497d2246678891bc613cf
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


<<<<<<< HEAD
if (null != $request->query->get('Controller')) {
    switch($request->query->get('Controller'))
    {
        case 'class.AdminController':
            $controller = new AdminController('Database.db');
            switch($request->query->get('Action'))
            {
=======
if (null != $request->query->get('PagesController')) {
    switch ($request->query->get('PagesController')) {
        case 'Admincontroller':
            $controller = new Admincontroller('Database.db');
            switch ($request->query->get('Action')) {
>>>>>>> 7f20ead68cadab5d2ca497d2246678891bc613cf
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
<<<<<<< HEAD
                case 'BlogPost':
                    $response = $controller->AddArticle($request);
                    break;
=======

                default:

                    die("Die Action " . $request->query->get('Action') . " wurde nicht gefunden!");
>>>>>>> 7f20ead68cadab5d2ca497d2246678891bc613cf
            }
            break;

        case 'Pagescontroller':

            $controller = new Pagescontroller('Database.db');
            switch ($request->query->get('Action')) {
                case 'home':

                    $response = $controller->CreateUser($request);

                    break;

                default:
                    die("Die Action:  " . $request->query->get('Action') . " wurde nicht gefunden!");

            }

        default:

            die("Der Controller " . $request->query->get('PagesController') . " wurde nicht gefunden!");
    }

}

$response->send();



