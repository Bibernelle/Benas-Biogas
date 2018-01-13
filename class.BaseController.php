<?php
require_once __DIR__ . '/vendor/autoload.php';


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController
{
    private $twig;
    protected $dataAccess;

    function __construct($filename)
    {
        $this -> dataAccess = new DataAccess($filename);
        $loader = new \Twig_Loader_Filesystem(
            realpath(dirname(__FILE__)) . '/templates');
        $this->twig = new \Twig_Environment($loader, [
            'cache' => false,
            'debug' => true
        ]);
    }

    protected function Render($viewPath, $parameters = array()) {
        if(isset($_SESSION[CSRF_TOKEN])) {
                $parameters[CSRF_TOKEN]=$_SESSION[CSRF_TOKEN];
        }
        $parameters['IsAdmin']=$this->IsAdmin();
        print_r($parameters);
        return $this->twig->render($viewPath, $parameters);
    }

    protected function IsAdmin(){
        if(isset($_GET[CSRF_TOKEN], $_SESSION[CSRF_TOKEN])){
            print("get token isset");
            if($_GET[CSRF_TOKEN] !== $_SESSION[CSRF_TOKEN]) {
                print("get token is wrong");
                return false;
            }

        }
        elseif(isset($_POST[CSRF_TOKEN], $_SESSION[CSRF_TOKEN])){
            print("post token isset");
            if($_POST[CSRF_TOKEN] !== $_SESSION[CSRF_TOKEN]) {
                print("post token is wrong");
                print("$_POST[CSRF_TOKEN] !== $_SESSION[CSRF_TOKEN]");
                return false;
            }
        }
        else{
            print("no token set");
            return false;
        }


        if(!isset($_SESSION['username'])){
            return false;
        }
        return $this->dataAccess->isAdministrator($_SESSION['username']);
    }

    protected function IsLoggedIn(){
        if(isset($_GET[CSRF_TOKEN], $_SESSION[CSRF_TOKEN])){
            if($_GET[CSRF_TOKEN] !== $_SESSION[CSRF_TOKEN]) {
                return false;
            }

        }
        elseif(isset($_POST[CSRF_TOKEN], $_SESSION[CSRF_TOKEN])){
            if($_POST[CSRF_TOKEN] !== $_SESSION[CSRF_TOKEN]) {
                return false;
            }
        }
        else{
            return false;
        }


        if(!isset($_SESSION['username'])){
            return false;
        }
        return true;
    }
}



