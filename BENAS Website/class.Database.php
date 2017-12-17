<?php

class Database
{
	protected $databaseHandle;
	function __construct($servername, $database, $username, $password)
	{
		$this -> databaseHandle = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
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
			$this -> DropTable("Measures");
			$this -> DropTable("Settings");
			$this -> DropTable("TimeSpans");
			$this -> DropTable("WantedValues");
			$this -> DropTable("SettingsVersion");
		}
		if (!$this -> tableExists('Measures'))
		{
			$sql = "CREATE table Measures(
					     ID INT( 11 ) AUTO_INCREMENT PRIMARY KEY,
					     Temperature FLOAT NOT NULL, 
					     Humidity FLOAT NOT NULL,
						 WantedTemperature FLOAT NOT NULL, 
					     WantedHumidity FLOAT NOT NULL,
						 IsVentilating bit NOT NULL,
						 IsIlluminating bit NOT NULL,
						 IsChannelAActive bit NOT NULL,
						 IsChannelBActive bit NOT NULL,
					     Timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP);";
			$this -> databaseHandle -> exec($sql);
		}
		
		if (!$this -> tableExists('SettingsVersion'))
		{
			$sql = "CREATE table SettingsVersion(
					     ID INT( 11 ) AUTO_INCREMENT PRIMARY KEY,
					     Timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP);";
			$this -> databaseHandle -> exec($sql);
		}

		if (!$this -> tableExists('Settings'))
		{
			$sql = "CREATE table Settings(
						ID INT( 11 ) AUTO_INCREMENT PRIMARY KEY,
						WantedTemperature FLOAT NOT NULL,
						WantedHumidity FLOAT NOT NULL,
						VentilationTimesKey int not null,
						IlluminationTimesKey int not null,
						ChannelATimesKey int not null,
						ChannelBTimesKey int not null,
						Timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP);";

			$this -> databaseHandle -> exec($sql);
		}

		if (!$this -> tableExists('TimeSpans'))
		{
			$sql = "CREATE table TimeSpans(
						ID INT( 11 ) AUTO_INCREMENT PRIMARY KEY,
						Channel text NOT NULL,
						Start datetime NOT NULL,
						End datetime NOT NULL);";

			$this -> databaseHandle -> exec($sql);
		}

		if (!$this -> tableExists('WantedValues'))
		{
			$sql = "CREATE table WantedValues(
						ID INT( 11 ) AUTO_INCREMENT PRIMARY KEY,
						Channel text NOT NULL,
						Timestamp datetime NOT NULL,
						Value double NOT NULL);";

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