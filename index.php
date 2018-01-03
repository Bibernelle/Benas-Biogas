<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once("class.Programm.php");
//require_once("class.Controller.php");
require_once("class.DataAccess.php");
require_once("class.AdminController.php");
$dal = new DataAccess("Database.db");
$dal -> AddUser("michi", "krankarsch");
$dal -> AddRole("Administrator");
$dal -> AddRole("Employee");
$dal -> AssignUserRole("michi", "Administrator");

require_once __DIR__ . '/vendor/autoload.php';


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


$request = Request::createFromGlobals();
$uri = $request->getPathInfo();


if (null != $request->query->get('Controller')) {
    switch($request->query->get('Controller'))
    {
        case 'class.AdminController':
            $controller = new AdminController('Database.db');
            switch($request->query->get('Action'))
            {
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
                case 'BlogPost':
                    $response = $controller->AddArticle($request);
                    break;
            }
            break;
        case 'Contentcontroller':
            $controller = new Contentcontroller();
         //   $response = $controller->
    }
    //$response = $controller->programmAction($request->query->get('id'));
}
else {
    $controller = new Controller('Database.db');
    $response = $controller->listAction();


}
// echo the headers and send the response
$response->send();



