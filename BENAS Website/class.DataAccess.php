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
        $salt = uniqid();
        $hash = password_hash($password. $salt, PASSWORD_DEFAULT)."\n";
		$sql = "insert into User (Name,Password,Salt) 
	  		VALUES('$name','$hash','$salt');";
		$this -> databaseHandle -> exec($sql);


	}

    public function LoginUser($name, $password)
    {
        $user = $this -> GetUser($name);
        $hash = password_hash($password. $user -> Salt, PASSWORD_DEFAULT)."\n";

        if($hash == $user -> Password)
        {
            $_SESSION['username'] = $name;
            return true;
        }
        return false;
    }

	public function GetUser($name)
    {
        $sql = "select * from User where Name = '$name'";
		$sth = $this -> databaseHandle -> prepare($sql);
        return $sth -> fetch(PDO::FETCH_ASSOC);
    }

	public function GetMeasures($from, $to)
	{

		$sth = $this -> databaseHandle -> prepare("SELECT * FROM
						(
							select 
							unix_timestamp(Timestamp)*1000 as Timestamp,
							Temperature,
							Humidity,
							WantedTemperature,
							WantedHumidity,
							ORD(IsVentilating) as IsVentilating,
							ORD(IsIlluminating) as IsIlluminating,
							ORD(IsChannelAActive) as IsChannelAActive,
							ORD(IsChannelBActive) as IsChannelBActive
							from Measures 
							where unix_timestamp(Timestamp)>=$from && unix_timestamp(Timestamp)<=$to
							order by timestamp desc limit 1000
						) as T1 order by timestamp asc");

		$sth -> execute();

		/* Group values by the first column */
		return $sth -> fetchAll(PDO::FETCH_ASSOC);
	}

	public function GetLastMeasure($lasttimestamp = null)
	{
		$elapsedTime = 0;
		do
		{

			$sth = $this -> databaseHandle -> prepare("SELECT 
							unix_timestamp(Timestamp)*1000 as Timestamp,
							Temperature,
							Humidity,
							WantedTemperature,
							WantedHumidity,
							ORD(IsVentilating) as IsVentilating,
							ORD(IsIlluminating) as IsIlluminating,
							ORD(IsChannelAActive) as IsChannelAActive,
							ORD(IsChannelBActive) as IsChannelBActive
							from Measures 
							order by timestamp desc limit 1");

			$sth -> execute();

			/* Group values by the first column */
			$dataset = $sth -> fetch(PDO::FETCH_ASSOC);

			usleep($this -> SleepDelay);
			$elapsedTime += $this -> SleepDelay;
			if ($elapsedTime > $this -> Timeout)
			{
				echo "elapsed";
				return null;
			}

			if ($lasttimestamp == null)
				return $dataset;

		}
		while($dataset['Timestamp']<=$lasttimestamp);
		return $dataset;
	}

	public function GetTimeSpans()
	{

		$sth = $this -> databaseHandle -> prepare("
							select 
							Channel,
							Start,
							End
							from TimeSpans 
							order by Channel desc");

		$sth -> execute();
		/* Group values by the first column */
		return $sth -> fetchAll(PDO::FETCH_ASSOC);
	}

	public function PrintChannelTimes($channel)
	{

		$sth = $this -> databaseHandle -> prepare("
							select 
							Channel,
							CONCAT(HOUR(Start) , ':' , MINUTE(Start)) as StartTime,
							CONCAT(HOUR(End) , ':' , MINUTE(End)) as EndTime
							from TimeSpans 
							where Channel='$channel'
							order by Start,End desc");

		$sth -> execute();
		/* Group values by the first column */
		$values = $sth -> fetchAll(PDO::FETCH_ASSOC);
		foreach ($values as $value)
		{
			echo $value['StartTime'] . ' ' . $value['EndTime'] . "\r\n";
		}
	}

	public function GetWantedTemperature()
	{

		$sth = $this -> databaseHandle -> prepare("
							select 
							Timestamp,
							Value
							from WantedValues 
							where Channel = 'Temperature'
							order by Channel desc,Timestamp desc");

		$sth -> execute();
		/* Group values by the first column */
		return $sth -> fetchAll(PDO::FETCH_ASSOC);
	}

	public function PrintWantedTimes($channel)
	{

		$sth = $this -> databaseHandle -> prepare("
							select 
						CONCAT(HOUR(Timestamp) , ':' , MINUTE(Timestamp)) as Time,
							Value
							from WantedValues 
							where Channel = '" . $channel . "'
							order by Timestamp, Channel desc"); 

		$sth -> execute();
		/* Group values by the first column */
		$values = $sth -> fetchAll(PDO::FETCH_ASSOC);

		if (count($values) >= 1)
		{
			array_unshift($values, array(
				'Time' => "00:00",
				'Value' => $values[count($values) - 1]['Value']
			));
			array_push($values, array(
				'Time' => "23:59",
				'Value' => $values[count($values) - 1]['Value']
			));
			foreach ($values as $value)
			{
				echo $value['Time'] . ' ' . $value['Value'] . "\r\n";
			}

		}

	}

	public function GetWantedHumidity()
	{

		$sth = $this -> databaseHandle -> prepare("
							select 
							Timestamp,
							Value
							from WantedValues 
							where Channel = 'Humidity'
							order by Channel desc,Timestamp desc");

		$sth -> execute();
		/* Group values by the first column */
		return $sth -> fetchAll(PDO::FETCH_ASSOC);
	}

	public function SetTimeSpans($ranges)
	{
		$sth = $this -> databaseHandle -> prepare("delete from TimeSpans");
		$sth -> execute();
		foreach ($ranges as $range)
		{
			$sth = $this -> databaseHandle -> prepare("insert into TimeSpans 
							(Channel,Start,End)
							Values('" . $range[0] . "','" . $this -> LocalDate($range[1]) . "','" . $this -> LocalDate($range[2]) . "')");

			$sth -> execute();
		}
		$this->SetSettingsVersion();
	}

	public function SetWantedHumidity($ranges)
	{
		$sth = $this -> databaseHandle -> prepare("delete from WantedValues where Channel='Humidity'");

		$sth -> execute();

		foreach ($ranges as $range)
		{

			$sth = $this -> databaseHandle -> prepare("insert into WantedValues
								(Channel,Timestamp,Value)
								Values('Humidity','" . $this -> LocalDate($range[0]) . "'," . $range[1] . ")");

			$sth -> execute();
		}
		$this -> SetSettingsVersion();
	}

	public function SetWantedTemperature($ranges)
	{

		$sth = $this -> databaseHandle -> prepare("delete from WantedValues where Channel='Temperature'");

		$sth -> execute();
		foreach ($ranges as $range)
		{
			$sth = $this -> databaseHandle -> prepare("insert into WantedValues
								(Channel,Timestamp,Value)
								Values('Temperature','" . $this -> LocalDate($range[0]) . "'," . $range[1] . ")");

			$sth -> execute();
		}
		$this -> SetSettingsVersion();
	}

	public function GetRange()
	{

		$sth = $this -> databaseHandle -> prepare("select 
							max(unix_timestamp(Timestamp)) as max,
							min(unix_timestamp(DATE_SUB(Timestamp, INTERVAL 30 DAY))) as min
							from Measures order by Timestamp");

		$sth -> execute();

		/* Group values by the first column */
		return $sth -> fetch(PDO::FETCH_ASSOC);

	}

	public function SetSettingsVersion()
	{
		$this -> clear("SettingsVersion");
		$sth = $this -> databaseHandle -> prepare("insert into SettingsVersion
								(ID)
								Values(0)");
		$sth -> execute();
	}

	public function GetSettingsVersion()
	{

		$sth = $this -> databaseHandle -> prepare("select ID from SettingsVersion");
		$sth -> execute();
		if ($sth -> rowCount() == 0)
		{
			$this -> SetSettingsVersion();
			return $this -> GetSettingsVersion();
		}
		return $sth -> fetchColumn();
	}

}
?>