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
        #add item to the list
        $result = run_select_on_db("SELECT Item_ID,Category_ID FROM haemishk_listsforhome.sl_items WHERE Item='".$_GET[$urlstring]."'", $db);
        while ($row = mysql_fetch_array($result))
        {
            $item_id = $row['Item_ID'];
            $cat_id = $row['Category_ID'];
        }
        if (mysql_num_rows($result) == 0)
        {
            // no products found
            $response["success"] = 0;
            $response["message"] = "No products found";
        }
        else 
        {
            $result = run_select_on_db("INSERT INTO sl_list(Item_ID,Category_ID,User_ID,ItemComment) VALUES('$item_id','$cat_id','" . $user_id . "','$item_comment')", $db);
            
            // success
            $response["success"] = 1;
            
            $response["message"] = "Added Successfully";
        }
    }
    if ($number_items == 0)
    {
        $response["success"] = 1;
        $response["message"] = "Nothing to add.";
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
