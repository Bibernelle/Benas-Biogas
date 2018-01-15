<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once("class.BaseController.php");

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PagesController extends BaseController
{
    public function home($request)
    {
        $data = $request->request->all();

        if (isset($data['content'])) {
            $this->dataAccess->AddContent('HomeContent', $data['content']);
        }

        $html = $this->Render('home.twig', array(
            'IsAdmin' => $this->IsAdmin(),
            'HomeContent' => $this->dataAccess->GetContent('HomeContent')
        ));
        return new Response($html, Response::HTTP_OK);
    }

    public function dasTeam()
    {
        $html = $this->Render('dasTeam.twig');
        return new Response($html, Response::HTTP_OK);
    }

    public function biogas($request)
    {
        $data = $request->request->all();

        if (isset($data['content'])) {
            $this->dataAccess->AddContent('BiogasContent', $data['content']);
        }

        $html = $this->Render('biogas.twig', array(
            'IsAdmin' => $this->IsAdmin(),
            'BiogasContent' => $this->dataAccess->GetContent('BiogasContent')
        ));
        return new Response($html, Response::HTTP_OK);
    }

    public function anlage($request)
    {
        $data = $request->request->all();

        if (isset($data['content'])) {
            for ($i = 0; $i < count($data['content']); $i++) {
                $this->dataAccess->AddContent('AnlageContent' . $i,
                    $data['content'][$i]);
            }
        }
        $content = array();
        for ($i = 0; $i < 11; $i++) {
            array_push($content,
                $this->dataAccess->GetContent('AnlageContent' . $i));
        }
        $html = $this->Render('anlage.twig',
            array('IsAdmin' => $this->IsAdmin(), 'AnlageContent' => $content));
        return new Response($html, Response::HTTP_OK);
    }

    public function rohstoffmanagement($request)
    {
        $data = $request->request->all();

        if (isset($data['content'])) {
            $this->dataAccess->AddContent('RohstoffmanagementContent',
                $data['content']);
        }

        $html = $this->Render('rohstoffmanagement.twig', array(
            'IsAdmin' => $this->IsAdmin(),
            'RohstoffmanagementContent' => $this->dataAccess->GetContent('RohstoffmanagementContent')
        ));
        return new Response($html, Response::HTTP_OK);
    }

    public function weitereInfos($request)
    {
        $data = $request->request->all();

        if (isset($data['content'])) {
            $this->dataAccess->AddContent('WeitereInfosContent',
                $data['content']);
        }

        $html = $this->Render('weitereInfos.twig', array(
            'IsAdmin' => $this->IsAdmin(),
            'WeitereInfosContent' => $this->dataAccess->GetContent('WeitereInfosContent')
        ));
        return new Response($html, Response::HTTP_OK);
    }


    public function kontakt($request)
    {
        $data = $request->request->all();

        if (isset($data['content'])) {
            $this->dataAccess->AddContent('KontaktContent', $data['content']);
        }

        $html = $this->Render('kontakt.twig', array(
            'IsAdmin' => $this->IsAdmin(),
            'KontaktContent' => $this->dataAccess->GetContent('KontaktContent')
        ));
        return new Response($html, Response::HTTP_OK);
    }


    public function aktuelles($request)
    {
        $data = $request->request->all();

        if (isset($data['content'])) {
            $this->dataAccess->AddContent('AktuellesContent', $data['content']);
        }

        $html = $this->Render('aktuelles.twig', array(
            'IsAdmin' => $this->IsAdmin(),
            'AktuellesContent' => $this->dataAccess->GetContent('AktuellesContent')
        ));
        return new Response($html, Response::HTTP_OK);
    }

    public function anfahrt()
    {
        $html = $this->Render('anfahrt.twig');
        return new Response($html, Response::HTTP_OK);
    }

    public function impressum($request)
    {
        $data = $request->request->all();

        if (isset($data['content'])) {
            $this->dataAccess->AddContent('ImpressumContent', $data['content']);
        }

        $html = $this->Render('impressum.twig', array(
            'IsAdmin' => $this->IsAdmin(),
            'ImpressumContent' => $this->dataAccess->GetContent('ImpressumContent')
        ));
        return new Response($html, Response::HTTP_OK);
    }

    public function dokumentation()
    {
        $html = $this->Render('dokumentation.twig');
        return new Response($html, Response::HTTP_OK);
    }

}



