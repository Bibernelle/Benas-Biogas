<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once("class.Programm.php");
require_once("class.Controller.php");
require_once("class.DataAccess.php");
$dal = new DataAccess("Database.db");
$dal -> AddUser("michi", "krankarsch");
if($dal -> LoginUser("michi", "krankarsch"))
{
    echo "logged in";
}
else {
    echo "not logged in";
}
require_once __DIR__ . '/vendor/autoload.php';


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$controller = new Controller();
$request = Request::createFromGlobals();

$uri = $request->getPathInfo();


if (null != $request->query->get('id')) {

    $response = $controller->programmAction($request->query->get('id'));
}
else {
    $response = $controller->listAction();


}
// echo the headers and send the response
$response->send();



