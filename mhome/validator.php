<?php

include 'generic_php.php';

#this gets all the users and adds them to a list for checking
$con = open_database_connection();

$result=run_select_on_db("SELECT Handle FROM sl_users where Handle='".$_POST['handle']."'",$con);

if (!mysql_num_rows($result))
{
	echo "Available!";
} else
{
	echo "Taken!";
}
?>