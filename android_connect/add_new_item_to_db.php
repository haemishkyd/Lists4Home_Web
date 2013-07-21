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
        $itemstring = "item".strval($prodindex);
        $catstring = "cat".strval($prodindex);
        #add item to the list
        $result = run_select_on_db("SELECT MAX(Item_ID) AS Max_Item_ID FROM sl_items", $db);

        while ($row = mysql_fetch_array($result))
        {
            $max_item_id = $row['Max_Item_ID'] + 1;
        }

        #now get the category id for the stipulated category
        $result = run_select_on_db("SELECT Category_ID FROM sl_categories WHERE Category='".$_GET[$catstring]."'", $db);

        while ($row = mysql_fetch_array($result))
        {
            $retrieved_cat_id = $row['Category_ID'];
        }

        $result = run_select_on_db("INSERT INTO sl_items(Item,Item_ID,Category_ID)VALUES('".$_GET[$itemstring]."','$max_item_id','$retrieved_cat_id')", $db);
        
        $response["success"] = 1;
            
        $response["message"] = "Added Successfully";
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
