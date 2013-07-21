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
    // get all products from products table
    $result = mysql_query("select items.Item from haemishk_listsforhome.sl_items items,haemishk_listsforhome.sl_list lists where items.item_id=lists.item_id and lists.user_id='".$user_id."';") or die(mysql_error());
    
    // check for empty result
    if (mysql_num_rows($result) > 0) {
        // looping through all results
        // products node
        $response["products"] = array();
        
        while ($row = mysql_fetch_array($result)) {
            // temp user array
            $product = array();
            $product["item"] = $row["Item"];
    
            // push single product into final response array
            array_push($response["products"], $product);
        }
        // success
        $response["success"] = 1;
    
        // echoing JSON response
        echo json_encode($response);
    } else {
        // no products found
        $response["success"] = 0;
        $response["message"] = "No products found";
    
        // echo no users JSON
        echo json_encode($response);
    }
}
else
{
    $response["query"] = $user_query;    
    $response["user"] = $user_id;
    $response["success"] = 0;
    echo json_encode($response);
}
?>
