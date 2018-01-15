<?php
/**
 * Created by PhpStorm.
 * User: GoOse
 * Date: 28.12.2017
 * Time: 15:21
 */

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require_once("class.BaseController.php");

class AdminController extends BaseController
{

    public function CreateUser(Request $request)
    {
        if (!$this->IsAdmin()) {
            $html = $this->Render('Error.twig',
                ['error' => 'User not logged in or not permitted']);
            return new Response($html, Response::HTTP_FORBIDDEN);
        }
        $data = $request->request->all();
      if (isset($data['username'], $data['password'], $data['role'])) {
            if ($this->dataAccess->AddUser($data['username'], $data['password'])) {
                $html = $this->Render('UserCreated.twig',
                    array('username' => $data['username'], 'role' => $data['role']));
                $this->dataAccess->AssignUserRole($data['username'], $data['role']);
            } else {
                $html = $this->Render('Error.twig',
                    ['error' => "Can't create user"]);
                return new Response($html, Response::HTTP_FORBIDDEN);
            }
        } else {
            $roles = $this->dataAccess->GetAllRoles();
            $html = $this->Render('CreateUser.twig', ['roles' => $roles]);
        }

        return new Response($html, Response::HTTP_OK);
    }

    public function DeleteUser(Request $request)
    {
        if (!$this->isAdmin()) {
            $html = $this->Render('Error.twig',
                ['error' => 'User not logged in or not permitted']);
            return new Response($html, Response::HTTP_FORBIDDEN);
        }
        $data = $request->request->all();
        if (isset($data['username'])) {
            if ($this->dataAccess->DeleteUser($data['username'])) {
                $html = $this->Render('UserDeleted.twig',
                    ['username' => $data['username']]);

            } else {
                $html = $this->Render('Error.twig',
                    ['error' => "Can't delete user"]);
                return new Response($html, Response::HTTP_FORBIDDEN);
            }
        } else {
            $users = $this->dataAccess->GetAllUsers();
            $users = array_diff($users, array($_SESSION['username']));
            $html = $this->Render('DeleteUser.twig', array('users' => $users));
        }

        return new Response($html, Response::HTTP_OK);
    }

    public function LoginUser(Request $request)
    {

        $data = $request->request->all();

        if (isset($data['username'], $data['password'])) {
            if ($this->dataAccess->LoginUser($data['username'], $data['password'])) {
                $html = $this->Render('LoggedIn.twig',
                    ['username' => $data['username']]);
            } else {
                $html = $this->Render('Error.twig',
                    ['error' => "Can't loggin user"]);
                return new Response($html, Response::HTTP_FORBIDDEN);
            }
        } else {
            $html = $this->Render('LoginUser.twig');
        }

        return new Response($html, Response::HTTP_OK);
    }

    public function LogoutUser()
    {
        unset($_SESSION['username']);
        unset($_SESSION[CSRF_TOKEN]);
        $html = $this->Render('LoggedOut.twig');
        return new Response($html, Response::HTTP_OK);
    }

    public function AssignRole(Request $request)
    {
        if (!$this->isAdmin()) {
            $html = $this->Render('Error.twig',
                ['error' => 'User not logged in or not permitted']);
            return new Response($html, Response::HTTP_FORBIDDEN);
        }
        $data = $request->request->all();

        if (isset($data['username'], $data['role'])) {
            if ($this->dataAccess->AssignUserRole($data['username'], $data['role'])) {
                $html = $this->Render('RoleAssigned.twig',
                    array('username' => $data['username'], 'role' => $data['role']));
            } else {
                $html = $this->Render('Error.twig',
                    ['error' => "Can't assign role to user"]);
                return new Response($html, Response::HTTP_FORBIDDEN);
            }
        } else {
            $roles = $this->dataAccess->GetAllRoles();
            $users = $this->dataAccess->GetAllUsers();
            $html = $this->Render('AssignRole.twig', array('users' => $users, 'roles' => $roles));
        }

        return new Response($html, Response::HTTP_OK);
    }

    public function CreateArticle(Request $request)
    {


        if (!$this->IsLoggedIn()) {
            header('Location: ../../index.php/Admin/LoginUser', true, 301);
            return;
        }
        $data = $request->request->all();
        if (isset($data['header'], $data['text'])) {
            try {

                $path = null;

                if($_FILES["fileToUpload"]["name"] != '') {
                    $path = $this->UploadFile();
                }

                $this->dataAccess->AddArticle($_SESSION['username'], $data['header'], $data['text'], $path);

                $articles = $this->dataAccess->GetAllArticles();

                $html = $this->Render('CreateArticle.twig', ['articles' => $articles]);

            } catch (Exception $e) {
                $html = $this->Render('Error.twig',
                    ['error' => $e->getMessage()]);
                return new Response($html, Response::HTTP_FORBIDDEN);
            }
        } else {
            $articles = $this->dataAccess->GetAllArticles();
            $html = $this->Render('CreateArticle.twig', ['articles' => $articles]);
        }

        return new Response($html, Response::HTTP_OK);
    }

    public function UploadFile()
    {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);

        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (isset($_POST["submit"])) {
            $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
            if ($check !== false) {


            } else {
                throw new Exception("Datei ist keine Bilddatei.");

            }
        }


        if ($_FILES["fileToUpload"]["size"] > 5000000) {
            throw new Exception("Entschuldigung, das Bild ist zu groß (max. 5mb). ");

        }

        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif") {
            throw new Exception("Nur Dateien vom Typ JPG, JPEG, PNG & GIF sind erlaubt.");

        }
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        //    echo "the file: " . basename($_FILES["fileToUpload"]["name"]) . " has been uploaded.";
        } else {
            throw new Exception("Entschuldigung es gab einen Fehler beim hochladen des Bildes.");

        }

        return $target_file;

    }
}