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

    private $administratorRole = 'Administrator';

    public function CreateUser(Request $request)
    {
        if (!isset($_SESSION['username']) || !($this->dataAccess->IsUserInRole($_SESSION['username'], $this->administratorRole))) {
            $html = $this->twig->render('Error.twig',
                ['error' => 'User not logged in or not permitted']);
            return new Response($html, Response::HTTP_FORBIDDEN);
        }
        $data = $request->request->all();
        if (isset($data['username'], $data['password'], $data['role'])) {
            if ($this->dataAccess->AddUser($data['username'], $data['password'])) {
                $html = $this->twig->render('UserCreated.twig',
                    array('username' => $data['username'], 'role' => $data['role']));
                $this->dataAccess->AssignUserRole($data['username'], $data['role']);
            } else {
                $html = $this->twig->render('Error.twig',
                    ['error' => "Can't create user"]);
                return new Response($html, Response::HTTP_FORBIDDEN);
            }
        } else {
            $roles = $this->dataAccess->GetAllRoles();
            $html = $this->twig->render('CreateUser.twig', ['roles' => $roles]);
        }

        return new Response($html, Response::HTTP_OK);
    }

    public function DeleteUser(Request $request)
    {
        if (!isset($_SESSION['username']) || !($this->dataAccess->IsUserInRole($_SESSION['username'], $this->administratorRole))) {
            $html = $this->twig->render('Error.twig',
                ['error' => 'User not logged in or not permitted']);
            return new Response($html, Response::HTTP_FORBIDDEN);
        }
        $data = $request->request->all();
        if (isset($data['username'])) {
            if ($this->dataAccess->DeleteUser($data['username'])) {
                $html = $this->twig->render('UserDeleted.twig',
                    ['username' => $data['username']]);

            } else {
                $html = $this->twig->render('Error.twig',
                    ['error' => "Can't delete user"]);
                return new Response($html, Response::HTTP_FORBIDDEN);
            }
        } else {
            $users = $this->dataAccess->GetAllUsers();
            $users = array_diff($users, array($_SESSION['username']));
            $html = $this->twig->render('DeleteUser.twig', array('users' => $users));
        }

        return new Response($html, Response::HTTP_OK);
    }

    public function LoginUser(Request $request)
    {
        // print_r($request->request->all());

        $data = $request->request->all();

        if (isset($data['username'], $data['password'])) {
            if ($this->dataAccess->LoginUser($data['username'], $data['password'])) {
                $html = $this->twig->render('LoggedIn.twig',
                    ['username' => $data['username']]);
            } else {
                $html = $this->twig->render('Error.twig',
                    ['error' => "Can't loggin user"]);
                return new Response($html, Response::HTTP_FORBIDDEN);
            }
        } else {
            $html = $this->twig->render('LoginUser.twig');
        }

        return new Response($html, Response::HTTP_OK);
    }

    public function LogoutUser()
    {
        // print_r($request->request->all());

        unset($_SESSION['username']);
        $html = $this->twig->render('LoggedOut.twig');
        return new Response($html, Response::HTTP_OK);
    }

    public function AssignRole(Request $request)
    {
        // print_r($request->request->all());
        if (!isset($_SESSION['username']) || !($this->dataAccess->IsUserInRole($_SESSION['username'], $this->administratorRole))) {
            $html = $this->twig->render('Error.twig',
                ['error' => 'User not logged in or not permitted']);
            return new Response($html, Response::HTTP_FORBIDDEN);
        }
        $data = $request->request->all();

        if (isset($data['username'], $data['role'])) {
            if ($this->dataAccess->AssignUserRole($data['username'], $data['role'])) {
                $html = $this->twig->render('RoleAssigned.twig',
                    array('username' => $data['username'], 'role' => $data['role']));
            } else {
                $html = $this->twig->render('Error.twig',
                    ['error' => "Can't assign role to user"]);
                return new Response($html, Response::HTTP_FORBIDDEN);
            }
        } else {
            $roles = $this->dataAccess->GetAllRoles();
            $users = $this->dataAccess->GetAllUsers();
            $html = $this->twig->render('AssignRole.twig', array('users' => $users, 'roles' => $roles));
        }

        return new Response($html, Response::HTTP_OK);
    }

    public function CreateArticle(Request $request)
    {


        if (!isset($_SESSION['username'])) {
            $html = $this->twig->render('Error.twig',
                ['error' => 'User not logged in or not permitted']);
            return new Response($html, Response::HTTP_FORBIDDEN);
        }
        $data = $request->request->all();
        if (isset($data['header'], $data['text'])) {
            $path = $this -> UploadFile();
            if ($this->dataAccess->AddArticle($_SESSION['username'], $data['header'], $data['text'], $path)) {

                $articles = $this->dataAccess->GetAllArticles();

                $html = $this->twig->render('CreateArticle.twig', ['articles' => $articles]);

            } else {
                $html = $this->twig->render('Error.twig',
                    ['error' => "Can't create article"]);
                return new Response($html, Response::HTTP_FORBIDDEN);
            }
        } else {
            $articles = $this->dataAccess->GetAllArticles();
            $html = $this->twig->render('CreateArticle.twig', ['articles' => $articles]);
        }

        return new Response($html, Response::HTTP_OK);
    }

    public function UploadFile()
    {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image
        if(isset($_POST["submit"])) {
            $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
            if($check !== false) {
                echo "File is an image - " . $check["mime"] . ".";
                $uploadOk = 1;
            } else {
                echo "File is not an image.";
                $uploadOk = 0;
            }
        }
// Check if file already exists
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
            $uploadOk = 0;
        }
// Check file size
        if ($_FILES["fileToUpload"]["size"] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }
// Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif" ) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }
// Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
        return $target_file;

    }
}