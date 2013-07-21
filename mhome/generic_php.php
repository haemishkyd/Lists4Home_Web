<?php
#=======================================================
# Generic Database Access Functions
#=======================================================
#	Run a generic select on the database specified
#-------------------------------------------------------
function run_select_on_db($sql_statement,$sql_connection)
{	
	$result=mysql_query($sql_statement,$sql_connection);
	if (!$result)
	{
		die('Query Error: ' . mysql_error());
	}
	
	return $result;
}
#-------------------------------------------------------
#	General Database Access Logic
#-------------------------------------------------------
function open_database_connection()
{
	#ByteHost
	$con = mysql_connect("sql.byethost25.org","haemishk_haemish","empyrean69");
	#MWEB
	#$con = mysql_connect("db2.swh.mweb.net","m3088230","8bi53ay5");
	if (!$con)
	{
		die('Could not connect: ' . mysql_error());
	}
	#ByteHost
	$db_selected = mysql_select_db("haemishk_listsforhome", $con);
	#MWEB
	#mysql_select_db("CLDBHOST_M3088230", $con);
	if (!$db_selected) 
	{
    	die ('Can\'t use foo : ' . mysql_error());
	}
	
	return $con;
}
#-------------------------------------------------------
#	Generate a random password
#-------------------------------------------------------
function randomPassword() 
{
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); 						//remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; 	//put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); 					//turn the array into a string
}
?>