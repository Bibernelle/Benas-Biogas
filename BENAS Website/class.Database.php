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

	public function LocalDate($utcDate)
	{
		return date('c',strtotime($utcDate));

	}

	public function TableExists($table)
	{

		// Try a select statement against the table
		// Run it in try/catch in case PDO is in ERRMODE_EXCEPTION.
		try
		{
			$result = $this -> databaseHandle -> query("SELECT 1 FROM $table LIMIT 1");
		}
		catch (Exception $e)
		{
			// We got an exception == table not found
			return FALSE;
		}

		// Result is either boolean FALSE (no table found) or PDOStatement Object (table found)
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
			$this -> DropTable("ImageGallery");
            $this -> DropTable("IsInImageGallery");
            $this -> DropTable("Article");
		}
		if (!$this -> tableExists('User'))
		{
			$sql = "CREATE table User(
					     ID INTEGER PRIMARY KEY,
					     Name text NOT NULL,
					     Salt NVARCHAR(255) NOT NULL, 
					     Password NVARCHAR(255) NOT NULL,
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
						Name NVARCHAR(255) NOT NULL,
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

        if (!$this -> tableExists('IsInImageGallery'))
        {
            $sql = "CREATE table IsInImageGallery(
					     ID INTEGER PRIMARY KEY,
					     ImageID INTEGER NOT NULL,
					     GalleryID INTEGER NOT NULL,
					     Timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP);";
            $this -> databaseHandle -> exec($sql);
        }

		if (!$this -> tableExists('ImageGallery'))
		{
			$sql = "CREATE table ImageGallery(
						ID INTEGER PRIMARY KEY,
						Name text NOT NULL);";

			$this -> databaseHandle -> exec($sql);
		}

        if (!$this -> tableExists('Article'))
        {
            $sql = "CREATE table Article(
						ID INTEGER PRIMARY KEY,
						Header text,
						Text text,
                        ImageID INTEGER,
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