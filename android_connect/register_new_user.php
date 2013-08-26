<?php

include '../mhome/generic_php.php';
/*
 * Following code register a new user
 */

// array for JSON response
$response = array();
$db = open_database_connection();
$user_id = "None";
$user_query="select user.User_ID from haemishk_listsforhome.sl_users user where user.Handle = '".$_GET["registerusername"]."';";
$result = mysql_query($user_query) or die(mysql_error());

if (mysql_num_rows($result) > 0) {
    while ($row = mysql_fetch_array($result)) {
        $user_id=$row["User_ID"];		
    }
}
/* A user by that name does not exist */
if ($user_id == "None")
{
    $sql1 = "SELECT MAX(User_ID) AS Max_User_ID FROM sl_users";

    $result = mysql_query($sql1, $db);
    while ($row = mysql_fetch_array($result))
    {
        $max_user_id = $row['Max_User_ID'] + 1;
    }

	$home_handle = $_GET["registerusername"];
	$home_reg_username = $_GET["registeractualname"];
	$home_reg_surname = $_GET["registeractualsurname"];
	$home_pass = $_GET["registerpassword"];
	$home_emailname = $_GET["registeremail"];
	
    $sql2 = "INSERT INTO haemishk_listsforhome.sl_users(Handle,User_Name,User_Surname,Password,Email,User_ID)VALUES('$home_handle','$home_reg_username','$home_reg_surname','" . md5($home_pass) . "','$home_emailname','$max_user_id')";

    if (!mysql_query($sql2, $db))
    {
        die('Error: ' . mysql_error());
		$response["success"] = 0;
	    $response["message"] = "Database Error.";
    }
	else
	{
	    $response["success"] = 1;
	    $response["message"] = "Added Successfully.";
	}
    
    echo json_encode($response);
}
else
{
    $response["query"] = $user_query;    
    $response["user"] = $user_id;
    $response["success"] = 0;
    $response["message"] = "User name already taken.";
    echo json_encode($response);
}
?>
