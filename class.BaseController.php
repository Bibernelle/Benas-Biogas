<?php
require_once __DIR__ . '/vendor/autoload.php';


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController
{
    protected $twig;
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

    protected function IsAdmin(){
        if(!isset($_SESSION['username'])){
            return false;
        }
        return $this->dataAccess->isAdministrator($_SESSION['username']);
    }
}



