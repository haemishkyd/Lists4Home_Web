<?php
include 'generic_php.php';

session_start();
$con = open_database_connection();

#=================================================================
#  Get all posts up front
#=================================================================
$new_category = $_POST['category_name'];
$new_item = $_POST['item_list_name'];
$item_comment = $_POST['item_comment_name'];
#user info
$home_handle = $_POST['homehandlename'];
$home_pass = $_POST['homepasswordname'];
$home_confirm = $_POST['confirmhomepasswordname'];
$home_reg_username = $_POST['reg_username'];
$home_reg_surname = $_POST['reg_surname'];
$home_emailname = $_POST['emailname'];
$reset_handle = $_POST['changehomehandlename'];
$reset_oldpword = $_POST['oldpasswordname'];
$reset_newpword = $_POST['newpasswordname'];

#=================================================================
#  Login Page Processing Section
#=================================================================
if (isset($_POST['changepassname']))
{
    $result = run_select_on_db("SELECT password from sl_users WHERE (Handle='" . $reset_handle . "')", $con);
    while ($row = mysql_fetch_array($result))
    {
        $password_to_compare = $row['password'];
    }
    if ($password_to_compare == md5($reset_oldpword))
    {
        $result = run_select_on_db("UPDATE sl_users SET password= '" . md5($reset_newpword) . "' where Handle='" . $reset_handle . "'", $con);
        echo "Password correct.";
        header('Location: change_pass.php?old_pass_wrong=0');
    } else
    {
        echo "Password wrong.";
    }
}

if (isset($_POST['logout']))
{
    session_destroy();
}

if (isset($_POST['login']))
{
    $sql = "SELECT * from sl_users WHERE (Handle='" . $home_handle . "') and (Password = '" . md5($home_pass) . "')";
    $login = mysql_query($sql, $con);
    if (!$login)
    {
        die('Error: ' . mysql_error());
    }
    #This is all test code - it can be removed----------------
    #echo "Passed: ".$home_handle."-".$home_pass."!<br>";
    #echo "Handle: ".$home_handle."!<br>";
    #echo "MD5: ".md5($home_pass)."!<br>";
    #echo $sql."<br>";
    #echo $login."<br>";
    #echo "Number of rows: ".mysql_num_rows($login)."<br>";
    #---------------------------------------------------------

    if (mysql_num_rows($login) == 1)
    {
        while ($row = mysql_fetch_array($login))
        {
            $_SESSION['user_id'] = $row['User_ID'];
        }
        $_SESSION['username'] = $home_handle;
        $identifier = $home_handle;
        $token = md5(uniqid(rand(), TRUE));
        $timeout = time() + (20 * 365 * 24 * 60 * 60);

        #remove the already existing session from the session list
        if (isset($_COOKIE['auth']))
        {
            $processed_cookie = explode(":", $_COOKIE['auth']);
            $sql = "DELETE FROM forhome_sessions WHERE homeid='$processed_cookie[0]' AND token='$processed_cookie[1]'";
            if (!mysql_query($sql, $con))
            {
                die('Error: ' . mysql_error());
            }
        }
        setcookie('auth', "$identifier:$token", $timeout);
        
        #get or set the device cookie this should only ever be created once
        if (!isset($_COOKIE['device']))        
        {
            $randomdeviceid = randomPassword();
            setcookie('device',"$identifier:$randomdeviceid",$timeout);
            $databaseval = $identifier.$randomdeviceid;
        }
        else
        {
            $processed_cookie = explode(":", $_COOKIE['device']);
            $databaseval = $processed_cookie[0].$processed_cookie[1];
        }
        #delete any old sessions from the database
        $sql = "DELETE FROM forhome_sessions WHERE device_id='$databaseval'";
        if (!mysql_query($sql, $con))
        {
            die('Error: ' . mysql_error());
        }
        #insert the new session
        $sql = "INSERT INTO forhome_sessions(homeid,token,device_id) VALUES('$identifier','$token','$databaseval')";
        if (!mysql_query($sql, $con))
        {
            die('Error: ' . mysql_error());
        }

        echo "Correct!";
    } else
    {
        echo "Wrong!";
    }
}

if (isset($_POST['register']))
{
    $sql1 = "SELECT MAX(User_ID) AS Max_User_ID FROM sl_users";

    $result = mysql_query($sql1, $con);
    while ($row = mysql_fetch_array($result))
    {
        $max_user_id = $row['Max_User_ID'] + 1;
    }

    $sql2 = "INSERT INTO sl_users(Handle,User_Name,User_Surname,Password,Email,User_ID)VALUES('$home_handle','$home_reg_username','$home_reg_surname','" . md5($home_pass) . "','$home_emailname','$max_user_id')";

    if (!mysql_query($sql2, $con))
    {
        die('Error: ' . mysql_error());
    }
    echo "Completed!";
}

if (isset($_POST['getuser']))
{
    echo $_SESSION['username'];
}

if (isset($_POST['resetpass']))
{
    $new_password=randomPassword();
    $result=run_select_on_db("SELECT email from sl_users WHERE (Handle='".$home_handle."')",$con);
    $email_addie = "Problem!";
    while($row = mysql_fetch_array($result))
    {
        $email_addie=$row['email'];
    }
    if ($email_addie != "Problem!")
    {   
        $to      = $email_addie;
        $subject = 'Lists4Home Password Reset';
        $message = 'This email is from Lists4Home. At your request your password has been reset to '.$new_password;
        $headers = 'From: info@lists4home.com' . "\r\n" .
        'Reply-To: info@lists4home.com' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();
    
        $result=run_select_on_db("UPDATE sl_users SET password= '".md5($new_password)."' where Handle='".$home_handle."'",$con);
        
        mail($to, $subject, $message, $headers);
        
        echo $email_addie;
    }
    else
    {
        echo "Problem!";
    }    
}

#=================================================================
#  Shopping List Page Processing Section
#=================================================================
if (isset($_POST['GetList']))
{
    $button_index = 0;
    $result = run_select_on_db("SELECT sl_items.Item,sl_list.ItemComment FROM sl_list,sl_items WHERE sl_items.Item_ID=sl_list.Item_ID AND sl_list.User_ID='" . $_SESSION['user_id'] . "' ORDER BY sl_list.Category_ID", $con);
    while ($row = mysql_fetch_array($result))
    {
        $button_index = $button_index + 1;
        echo "<li><table class=\"l4h_shopping_list_table\"><tr><td id=\"checkbox_column\">";
        #This is all the checkbox
        echo "<div style=\"display:block\" onclick=\"ChangeCheckBox('Check" . $button_index . "','CheckBox" . $button_index . "','Element" . $button_index . "')\">";
        echo "<img id=\"CheckBox$button_index\" src=\"../images/checkbox-unchecked-md.png\" class=\"l4h_icon\">";
        echo "<input style=\"display: none;\" type=\"checkbox\" id=\"Check$button_index\" name=\"shopping_list[]\" value=\"" . $row['Item'] . "\" onclick=\"ChangeState('Element" . $button_index . "')\" />";
        echo "</div>";
        echo "</td><td id=\"item_column\">";
        #This is the actual item
        echo "<div id=\"Element" . $button_index . "\" style=\"display:block\" onclick=\"ChangeCheckBox('Check" . $button_index . "','CheckBox" . $button_index . "','Element" . $button_index . "')\" >";
        echo $row['Item'];
        if ($row['ItemComment'] != "")
        {
            echo "<a style=\"font-style:italic;text-decoration: none;color: #AFAFAF;\"> : " . $row['ItemComment'] . "</a>";
        }
        echo "</div>";
        echo "</td></tr></table>";
        echo "</li>";
    }
}

if (isset($_POST['RemoveItems']))
{
    #OPTIMISATION: This could be done in one query instead of multiple queries.
    $shopping = $_POST['shopping_list'];
    echo $shopping . "<br>";
    foreach ($shopping as $one_item)
    {
        echo $one_item . "<br>";
        $result = run_select_on_db("INSERT INTO sl_bought(Item,PurchasedDate,User_ID)VALUES('$one_item',CURDATE(),'" . $_SESSION['user_id'] . "')", $con);
        $result = run_select_on_db("DELETE FROM  sl_list WHERE Item_ID=(SELECT Item_ID FROM sl_items WHERE Item='$one_item') AND User_ID='". $_SESSION['user_id']."'" , $con);
    }
}

if (isset($_POST['SubmitToList']))
{
    #check to see if the item already exists if it already exists the item will not be added
    $result = run_select_on_db("SELECT Item_ID FROM sl_items WHERE Item='$new_item'", $con);
    echo "Returned: " + mysql_num_rows($result);

    #if this is a new item - create it
    if (($new_item != "") && ($_COOKIE['UnknownItem'] == 1) && (!mysql_num_rows($result)))
    {
        #get the next item ID
        $result = run_select_on_db("SELECT MAX(Item_ID) AS Max_Item_ID FROM sl_items", $con);

        while ($row = mysql_fetch_array($result))
        {
            $max_item_id = $row['Max_Item_ID'] + 1;
        }

        #now get the category id for the stipulated category
        if ($new_category == "")
        {
            $sql_query_cat = $_POST['catList'];
        } else
        {
            $sql_query_cat = $new_category;
        }

        $result = run_select_on_db("SELECT Category_ID FROM sl_categories WHERE Category='$sql_query_cat'", $con);

        while ($row = mysql_fetch_array($result))
        {
            $retrieved_cat_id = $row['Category_ID'];
        }

        $result = run_select_on_db("INSERT INTO sl_items(Item,Item_ID,Category_ID)VALUES('$new_item','$max_item_id','$retrieved_cat_id')", $con);
    }

    #add item to the list
    $result = run_select_on_db("SELECT Item_ID,Category_ID FROM sl_items WHERE Item='$new_item'", $con);
    while ($row = mysql_fetch_array($result))
    {
        $item_id = $row['Item_ID'];
        $cat_id = $row['Category_ID'];
        echo "Item: $item_id Cat:$cat_id<br>";
    }

    if ($_COOKIE['UnknownBarcode'] == 1)
    {
        $result = run_select_on_db("INSERT INTO sl_barcodes(barcode,Item_ID) VALUES('$bar_code','$item_id')", $con);
        $_COOKIE['UnknownBarcode'] = 0;
    }
    $result = run_select_on_db("INSERT INTO sl_list(Item_ID,Category_ID,User_ID,ItemComment) VALUES('$item_id','$cat_id','" . $_SESSION['user_id'] . "','$item_comment')", $con);

}

//Close the database
mysql_close($con);
?>