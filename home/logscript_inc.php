<?php
$con = open_database_connection();

//Initialise the session
session_start();

//Check if the user is already logged in and if so carry on
if (!isset($_SESSION['username']))
{
	if (isset($_COOKIE['auth']))
	{
		$name = explode(":", $_COOKIE['auth']);
		$sql = "SELECT * FROM forhome_sessions WHERE homeid='$name[0]' and token='$name[1]'";
		$result = mysql_query($sql, $con);
		if (mysql_num_rows($result) != 1)
		{
			header('Location: login.php');
		} else
		{
			$sql = "SELECT * from sl_users WHERE Handle='$name[0]'";
			$login = mysql_query($sql, $con);
			if (mysql_num_rows($login) == 1)
			{
				while ($row = mysql_fetch_array($login))
				{
					$_SESSION['user_id'] = $row['User_ID'];
					$_SESSION['username'] = $row['Handle'];
				}
			}
		}
	} else
	{
		header('Location: /home/login.php');
	}
}
?>