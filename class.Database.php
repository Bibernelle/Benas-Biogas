<?php
require_once ('class.SQLLitePDO.php');

class Database
{
	protected $databaseHandle;
	function __construct($filename)
	{
        $this -> databaseHandle = new PDO('sqlite:'.$filename);
		// set the PDO error mode to exception
		$this -> databaseHandle -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this -> createDataStructure();
	}

	public function TableExists($table)
	{

		try
		{
			$result = $this -> databaseHandle -> query("SELECT 1 FROM $table LIMIT 1");
		}
		catch (Exception $e)
		{

			return FALSE;
		}

		return $result !== FALSE;
	}

	public function DropTable($name)
	{
		$sql = "DROP TABLE $name";
		$this -> databaseHandle -> exec($sql);
	}

	public function CreateDataStructure($dropExisting = false)
	{
		if ($dropExisting)
		{
			$this -> DropTable("User");
			$this -> DropTable("IsInUserRole");
			$this -> DropTable("UserRole");
			$this -> DropTable("Image");
            $this -> DropTable("Article");
		}
		if (!$this -> tableExists('User'))
		{
			$sql = "CREATE table User(
					     ID INTEGER PRIMARY KEY,
					     Name text NOT NULL,
					     Salt text NOT NULL, 
					     Password text NOT NULL,
					     Timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP);";
			$this -> databaseHandle -> exec($sql);
		}
		
		if (!$this -> tableExists('IsInUserRole'))
		{
			$sql = "CREATE table IsInUserRole(
					     ID INTEGER PRIMARY KEY,
					     UserID INTEGER NOT NULL,
					     RoleID INTEGER NOT NULL,
					     Timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP);";
			$this -> databaseHandle -> exec($sql);
		}

		if (!$this -> tableExists('UserRole'))
		{
			$sql = "CREATE table UserRole(
						ID INTEGER PRIMARY KEY,
						Name text NOT NULL,
						Timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP);";

			$this -> databaseHandle -> exec($sql);
		}

		if (!$this -> tableExists('Image'))
		{
			$sql = "CREATE table Image(
						ID INTEGER PRIMARY KEY,
						Path text NOT NULL,
						UserID INTEGER NOT NULL,
						Timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP);";

			$this -> databaseHandle -> exec($sql);
		}



        if (!$this -> tableExists('Article'))
        {
            $sql = "CREATE table Article(
						ID INTEGER PRIMARY KEY,
						Header text,
						Text text,
                        ImageID INTEGER,
                        UserID INTEGER,
                        Timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP);";

            $this -> databaseHandle -> exec($sql);
        }

        if (!$this -> tableExists('Content'))
        {
            $sql = "CREATE table Content(
						ID INTEGER PRIMARY KEY,
						[Index] text,
						Content text,
                        Timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP);";

            $this -> databaseHandle -> exec($sql);
        }

	}

	public function clear($table)
	{
		$sth = $this -> databaseHandle -> prepare("delete from $table");
		$sth -> execute();
	}
	

}
?>