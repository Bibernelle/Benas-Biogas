<?php
session_start();
require_once ("class.Database.php");
class DataAccess extends Database
{
	private $SleepDelay = 50000;
	private $Timeout = 50000000;

	function __construct($filename)
	{

		parent::__construct($filename);
	}

	public function AddUser($name, $password)
	{
        if($this->UserExists($name))
        {
            return false;
        }
        if($name == '' || $password == '')
        {
            return false;
        }
        $salt = uniqid();
        $hash = sha1($password.$salt);
		$sql = "insert into User (Name,Password,Salt) 
	  		VALUES('$name','$hash','$salt');";
        $stmt = $this -> databaseHandle -> prepare($sql);

        $stmt -> execute();
        if (!$stmt) {
            echo "\nPDO::errorInfo():\n";
            print_r($this->databaseHandle->errorInfo());
            return false;
        }
        return true;
	}

    public function DeleteUser($name)
    {
        if(!$this->UserExists($name))
        {

            return false;
        }
        $user = $this->GetUser($name);


        $sql = "delete from IsInUserRole where UserID = ".$user['ID'].";" ;
        $stmt = $this -> databaseHandle -> prepare($sql);

        $stmt -> execute();

        $sql = "delete from User where ID = ".$user['ID'].";" ;
        $stmt = $this -> databaseHandle -> prepare($sql);

        $stmt -> execute();
        if (!$stmt) {
            echo "\nPDO::errorInfo():\n";
            print_r($this->databaseHandle->errorInfo());
            return false;
        }
        return true;
    }

    public function LoginUser($name, $password)
    {
       if(!$this->UserExists($name))
       {
           print "UserNotFound: $name";
           return;
       }

        $user = $this -> GetUser($name);

        $hash = sha1($password.$user ["Salt"]);

        if($hash == $user ["Password"])
        {
            $_SESSION['username'] = $name;
            return true;
        }
        return false;
    }

    public function UserExists($name)
    {

        $sql = "select * from User where Name = '$name'";
        foreach ($this->databaseHandle->query($sql) as $row) {
            return true;
        }
        return false;
    }

	public function GetUser($name)
    {
        $sql = "select * from User where Name = '$name'";
        foreach ($this->databaseHandle->query($sql) as $row) {
            $row ['Roles'] = $this -> GetUserRoles($name);
            return $row;
        }
        return null;
    }

    public function GetUserID($name)
    {

        return $this -> GetUser($name) ['ID'];
    }

    public function GetAllUsers()
    {

        $results = array();
        $sql = "select * from User";
        foreach ($this->databaseHandle->query($sql) as $row) {
            array_push($results, $row['Name']);

        }


        return $results;
    }

    public function AddRole($name)
    {
        if($this->RoleExists($name))
        {
            print "User: $name already exists";
            return;
        }

        $sql = "insert into UserRole (Name) 
	  		VALUES('$name');";
        $stmt = $this -> databaseHandle -> prepare($sql);

        $stmt -> execute();
        if (!$stmt) {
            echo "\nPDO::errorInfo():\n";
            print_r($this->databaseHandle->errorInfo());
        }

    }

    public function RoleExists($name)
    {

        $sql = "select * from UserRole where Name = '$name'";
        foreach ($this->databaseHandle->query($sql) as $row) {
            return true;
        }
        return false;
    }

    public function IsUserInRole($username, $rolename)
    {

        $sql = "select UserRole.Name Name from UserRole 
            LEFT JOIN IsInUserRole
            ON (UserRole.ID=IsInUserRole.RoleID) 
            LEFT JOIN User 
            ON (User.ID=IsInUserRole.UserID) 
            WHERE User.Name = '$username' AND UserRole.Name ='$rolename'";
        foreach ($this->databaseHandle->query($sql) as $row) {
            return true;
        }
        return false;
    }

    public function GetRole($name)
    {
        $sql = "select * from UserRole where Name = '$name'";
        foreach ($this->databaseHandle->query($sql) as $row) {
            return $row;
        }
        return null;
    }

    public function GetAllRoles()
    {
        $results = array();
        $sql = "select * from UserRole";
        foreach ($this->databaseHandle->query($sql) as $row) {
            array_push($results, $row['Name']);
          // print_r($results);

        }
        return $results;
    }

    public function AssignUserRole($username, $rolename)
    {
        if(!$this->RoleExists($rolename))
        {
            print "Role: $rolename doesnt exist";
            return false;
        }
        if(!$this->UserExists($username))
        {
            print "User: $username doesnt exist";
            return false;
        }
        if($this->IsUserInRole($username, $rolename))
        {
            print "User: $username is already $rolename";
            return false;
        }
        $user = $this -> GetUser($username);
        $role = $this -> GetRole($rolename);
        $sql = "insert into IsInUserRole (UserID, RoleID) 
	  		VALUES(".$user ['ID'].",".$role ['ID'].");";
        $stmt = $this -> databaseHandle -> prepare($sql);

        $stmt -> execute();
        return true;
    }

    public function GetUserRoles($username)
    {
        $roles = array();

        $sql = "select UserRole.Name Name from UserRole 
            LEFT JOIN IsInUserRole
            ON (UserRole.ID=IsInUserRole.RoleID) 
            LEFT JOIN User 
            ON (User.ID=IsInUserRole.UserID) 
            WHERE User.Name = '$username'";
        foreach ($this->databaseHandle->query($sql) as $row) {
            array_push($roles,$row['Name']);
        }
        return $roles;
    }

    public function isAdministrator($username)
    {
        return $this -> IsUserInRole($username, 'Administrator');
    }

    public function AddImage($path, $userID)
    {
        $sql = "insert into Image (Path, UserID) 
	  		VALUES('$path','$userID')";
        $stmt = $this -> databaseHandle -> prepare($sql);

        $stmt -> execute();
        if (!$stmt) {
            echo "\nPDO::errorInfo():\n";
            print_r($this->databaseHandle->errorInfo());
            return false;
        }
        $sql = "select ID from Image where Path = '$path';";
        foreach ($this->databaseHandle->query($sql) as $row) {
            return $row ['ID'];
        }
        return false;
    }

    public function AddArticle($username, $header, $text, $imagePath)
    {
        if(!$this->UserExists($username))
        {
            return null;
        }
        $userID = $this -> GetUserID($username);
        $imageID = $this -> AddImage($imagePath, $userID);
        $sql = "insert into Article (Header,Text,ImageID) 
	  		VALUES('$header','$text','$imageID');";
        $stmt = $this -> databaseHandle -> prepare($sql);

        $stmt -> execute();
        if (!$stmt) {
            echo "\nPDO::errorInfo():\n";
            print_r($this->databaseHandle->errorInfo());
            return false;
        }
        return true;
    }

    public function GetAllArticles()
    {
        $results = array();
        $sql = "select Article.Header Header, Article.Text Text, Article.Timestamp Timestamp, Image.Path ImagePath from Article 
              LEFT JOIN Image ON (Article.ImageID=Image.ID)";
        foreach ($this->databaseHandle->query($sql) as $row) {
            array_push($results, $row);
            // print_r($results);

        }
        return $results;
    }
}
?>