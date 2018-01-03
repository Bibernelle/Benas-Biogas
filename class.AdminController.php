<?php
/**
 * Created by PhpStorm.
 * User: GoOse
 * Date: 28.12.2017
 * Time: 15:21
 */

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController
{
    public $dataAccess;
    private $administratorRole = 'Administrator';
    private $twig;

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

    public function CreateUser(Request $request)
    {
        if(!isset($_SESSION['username']) || !($this->dataAccess->IsUserInRole($_SESSION['username'], $this->administratorRole)))
        {
            $html = $this->twig->render('Error.twig',
                ['error' => 'User not logged in or not permitted']);
            return new Response($html, Response::HTTP_FORBIDDEN);
        }
        $data = $request->request->all();
        if (isset($data['username'],$data['password'],$data['role']))
        {
            if($this->dataAccess->AddUser($data['username'], $data['password']))
            {
                $html = $this->twig->render('UserCreated.twig',
                    array('username' => $data['username'], 'role' => $data['role']));
                $this->dataAccess->AssignUserRole($data['username'], $data['role']);
            }
            else {
                $html = $this->twig->render('Error.twig',
                    ['error' => "Can't create user"]);
                return new Response($html, Response::HTTP_FORBIDDEN);
            }
        }
        else{
            $roles = $this->dataAccess->GetAllRoles();
            $html = $this->twig->render('CreateUser.twig', ['roles' => $roles]);
        }

        return new Response($html, Response::HTTP_OK);
    }

    public function DeleteUser(Request $request)
    {
        if(!isset($_SESSION['username']) || !($this->dataAccess->IsUserInRole($_SESSION['username'], $this->administratorRole)))
        {
            $html = $this->twig->render('Error.twig',
                ['error' => 'User not logged in or not permitted']);
            return new Response($html, Response::HTTP_FORBIDDEN);
        }
        $data = $request->request->all();
        if (isset($data['username']))
        {
            if($this->dataAccess->DeleteUser($data['username']))
            {
                $html = $this->twig->render('UserDeleted.twig',
                    ['username' => $data['username']]);

            }
            else {
                $html = $this->twig->render('Error.twig',
                    ['error' => "Can't delete user"]);
                return new Response($html, Response::HTTP_FORBIDDEN);
            }
        }
        else{
            $users = $this->dataAccess->GetAllUsers();
            $users = array_diff($users, array($_SESSION['username']));
            $html = $this->twig->render('DeleteUser.twig', array('users'=>$users));
        }

        return new Response($html, Response::HTTP_OK);
    }

    public function LoginUser(Request $request)
    {
        // print_r($request->request->all());

        $data = $request->request->all();

        if (isset($data['username'],$data['password']))
        {
            if($this->dataAccess->LoginUser($data['username'], $data['password']))
            {
                $html = $this->twig->render('LoggedIn.twig',
                    ['username' => $data['username']]);
            }
            else {
                $html = $this->twig->render('Error.twig',
                    ['error' => "Can't loggin user"]);
                return new Response($html, Response::HTTP_FORBIDDEN);
            }
        }
        else{
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
        if(!isset($_SESSION['username']) || !($this->dataAccess->IsUserInRole($_SESSION['username'], $this->administratorRole)))
        {
            $html = $this->twig->render('Error.twig',
                ['error' => 'User not logged in or not permitted']);
            return new Response($html, Response::HTTP_FORBIDDEN);
        }
        $data = $request->request->all();

        if (isset($data['username'],$data['role']))
        {
            if($this->dataAccess->AssignUserRole($data['username'], $data['role']))
            {
                $html = $this->twig->render('RoleAssigned.twig',
                    array('username' => $data['username'],'role' => $data['role']));
            }
            else {
                $html = $this->twig->render('Error.twig',
                    ['error' => "Can't assign role to user"]);
                return new Response($html, Response::HTTP_FORBIDDEN);
            }
        }
        else{
            $roles = $this->dataAccess->GetAllRoles();
            $users = $this->dataAccess->GetAllUsers();
            $html = $this->twig->render('AssignRole.twig', array('users' => $users, 'roles' => $roles));
        }

        return new Response($html, Response::HTTP_OK);
    }
}