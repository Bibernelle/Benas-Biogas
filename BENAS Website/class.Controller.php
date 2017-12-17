<?php
require_once __DIR__ . '/vendor/autoload.php';


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class Controller
{
    private $twig;
    private $Programs = array();

    function __construct()
    {
        $loader = new \Twig_Loader_Filesystem(
            realpath(dirname(__FILE__)) . '/templates');
        $this->twig = new \Twig_Environment($loader, [
            'cache' => false,
            'debug' => true
        ]);
        $files = glob("*.yml");

        foreach ($files as $item) {
            $entry = new Programm($item);
            array_push($this->Programs, $entry);
        }
    }

    public function listAction()
    {
        $html = $this->twig->render('list.twig',
            ['programs' => $this->Programs]);
        return new Response($html, Response::HTTP_OK);
    }

    public function programmAction($id)
    {
        foreach ($this->Programs as $program) {
            if ($program->id == $id) {
                $html = $this->twig->render('program.twig',['program' => $program]);
                break;
            }
        }
        return new Response($html, Response::HTTP_OK);
    }
}


