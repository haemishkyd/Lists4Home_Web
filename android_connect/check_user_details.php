<?php

include '../mhome/generic_php.php';
/*
 * Following code will list all the products
 */

// array for JSON response
$response = array();
$db = open_database_connection();
$user_id = "None";
$user_query="select user.User_ID from haemishk_listsforhome.sl_users user where user.Handle = '".$_GET["name"]."' and user.Password ='".$_GET["pass"]."';";
$result = mysql_query($user_query) or die(mysql_error());

if (mysql_num_rows($result) > 0) {
    while ($row = mysql_fetch_array($result)) {
        $user_id=$row["User_ID"];
    }
}
if ($user_id != "None")
{    
    $response["success"] = 1;
    $response["message"] = "User Details Correct.";

    echo json_encode($response);
}
else
{    
    $response["success"] = 0;
	$response["message"] = "User Details Incorrect.";
	
    echo json_encode($response);
}
?>
