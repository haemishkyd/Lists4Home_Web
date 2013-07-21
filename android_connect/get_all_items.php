<?php

include '../mhome/generic_php.php';
/*
 * Following code will list all the products
 */

// array for JSON response
$response = array();


$db = open_database_connection();

// get all products from products table
$result = mysql_query("select items.Item,items.Category_ID from haemishk_listsforhome.sl_items items") or die(mysql_error());

// check for empty result
if (mysql_num_rows($result) > 0) {
    // looping through all results
    // products node
    $response["products"] = array();
    $response["category"] = array();
    
    while ($row = mysql_fetch_array($result)) {
        // temp user array
        $product = array();
        $product["item"] = $row["Item"];
        $product["cat"]=$row["Category_ID"];
        
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
?>
