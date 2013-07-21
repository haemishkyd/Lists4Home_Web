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
    $number_items = $_GET["numitems"];
    for ($prodindex = 0;$prodindex<$number_items;$prodindex++)
    {
        $urlstring = "product".strval($prodindex);
        
        $result = run_select_on_db("INSERT INTO sl_bought(Item,PurchasedDate,User_ID)VALUES('".$_GET[$urlstring]."',CURDATE(),'" . $user_id . "')", $db);
        $result = run_select_on_db("DELETE FROM  sl_list WHERE Item_ID=(SELECT Item_ID FROM sl_items WHERE Item='".$_GET[$urlstring]."') AND User_ID='". $user_id."'" , $db);
        
        // success
        $response["success"] = 1;        
        $response["message"] = "Added Successfully";
    }    
    echo json_encode($response);
}
else
{
    $response["query"] = $user_query;    
    $response["user"] = $user_id;
    $response["success"] = 0;
    echo json_encode($response);
}
?>
