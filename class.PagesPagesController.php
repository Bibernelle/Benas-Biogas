<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once("class.BaseController.php");

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class PagesController extends BaseController
{


    public function home()
    {
        $html = $this->twig->render('home.twig');
        return new Response($html, Response::HTTP_OK);
    }
}


