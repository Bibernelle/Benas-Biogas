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
    public function dasTeam()
    {
        $html = $this->twig->render('dasTeam.twig');
        return new Response($html, Response::HTTP_OK);
    }


    public function biogas()
    {
        $html = $this->twig->render('biogas.twig');
        return new Response($html, Response::HTTP_OK);
    }

    public function anlage()
    {
        $html = $this->twig->render('anlage.twig');
        return new Response($html, Response::HTTP_OK);
    }
    public function rohstoffmanagement()
    {
        $html = $this->twig->render('rohstoffmanagement.twig');
        return new Response($html, Response::HTTP_OK);
    }

    public function weitereInfos()
    {
        $html = $this->twig->render('weitereInfos.twig');
        return new Response($html, Response::HTTP_OK);
    }


    public function kontakt()
    {
        $html = $this->twig->render('kontakt.twig');
        return new Response($html, Response::HTTP_OK);
    }


    public function aktuelles()
    {
        $html = $this->twig->render('aktuelles.twig');
        return new Response($html, Response::HTTP_OK);
    }

    public function anfahrt()
{
    $html = $this->twig->render('anfahrt.twig');
    return new Response($html, Response::HTTP_OK);
}
    public function impressum()
    {
        $html = $this->twig->render('impressum.twig');
        return new Response($html, Response::HTTP_OK);
    }

}



