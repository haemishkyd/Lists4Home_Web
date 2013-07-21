<!DOCTYPE html>
<html>
<body>
<?php
include 'generic_php.php';

session_start();
$con=open_database_connection();


#=================================================================
#  Get all posts up front
#=================================================================
$new_category=$_POST['category'];
$new_item=$_POST['item'];
$item_comment=$_POST['commentitem'];
$bar_code=$_POST['barcodename'];
#wine info
$new_farm=$_POST['NewFarmName'];
$farm_choice=$_POST['farmList'];
$wine_year=$_POST['yearList'];
$wine_nobottles=$_POST['NoBottles'];
$wine_comment=$_POST['WineComment'];
$varietal_list=$_POST['varietals_list'];
$remove_buttons=$_POST['RemoveFromWineList'];
$remove_buttons_value=$_POST['RemoveFromWineListValue'];
#user info
$home_handle=$_POST['homehandlename'];
$home_pass=$_POST['homepasswordname'];
$home_confirm=$_POST['confirmhomepasswordname'];
$home_reg_username=$_POST['reg_username'];
$home_reg_surname=$_POST['reg_surname'];
$home_emailname=$_POST['emailname'];
$reset_handle=$_POST['changehomehandlename'];
$reset_oldpword=$_POST['oldpasswordname'];
$reset_newpword=$_POST['newpasswordname'];

#=================================================================
#  Wine List Page Processing Section
#=================================================================
if (isset($_POST['SubmitToWineList']))
{
	if ($new_farm != "")
	{
		$sql="INSERT INTO wine_farms(Farm)VALUES('$new_farm')";

		if (!mysql_query($sql,$con))
		{
			die('Error: ' . mysql_error());
		}
		#if a farm was entered that must override the selection from the drop
		#down which will be (*Create New)
		$farm_choice=$new_farm;
	}
	
	$isblend=0;
	if (count($varietal_list)>1)
	{
		$isblend=1;
	}	
	
	$sql="INSERT INTO wine_collection(Farm_ID,Year,IsBlend,Comment,NoBottles,User_ID) SELECT Farm_ID,'$wine_year','$isblend','$wine_comment','$wine_nobottles','".$_SESSION['user_id']."' FROM wine_farms WHERE Farm='$farm_choice'";

	if (!mysql_query($sql,$con))
	{
		die('Error: ' . mysql_error());
	}
	
	$record_id=mysql_insert_id();

	foreach ($varietal_list as $one_item)
	{
		$sql_blend="INSERT INTO wine_blend(Record_ID,Varietal_ID)VALUES('$record_id','$one_item')";
		if (!mysql_query($sql_blend,$con))
		{
			die('Error: ' . mysql_error());
		}
	}
	
	header('Location: wine_list.php');
	#Query to find all orphan blends
	#SELECT * FROM `wine_blend` WHERE NOT EXISTS ( SELECT * FROM wine_collection WHERE wine_collection.Record_ID = wine_blend.Record_ID)
}

if (isset($_POST['RemoveFromWineList']))
{
	echo $_COOKIE['ItemToRemove'];
	$sql="DELETE FROM wine_collection WHERE Record_ID=".$_COOKIE['ItemToRemove'];
	if (!mysql_query($sql,$con))
	{
		die('Error: ' . mysql_error());
	}
	$sql="DELETE FROM wine_blend WHERE Record_ID=".$_COOKIE['ItemToRemove'];
	if (!mysql_query($sql,$con))
	{
		die('Error: ' . mysql_error());
	}
	header('Location: wine_list.php');
}

if (isset($_POST['sortButton']))
{
	$blend_sort=$_COOKIE['BlendSort'];
	$timeout=time() + (20 * 365 * 24 * 60 * 60);
	if ($blend_sort==0)
	{
		setcookie('BlendSort',1,$timeout);
		header('Location: wine_list.php?sortby=1');
	}
	else if ($blend_sort==1)
	{
		setcookie('BlendSort',2,$timeout);
		header('Location: wine_list.php?sortby=2');
	}
	else
	{
		setcookie('BlendSort',0,$timeout);
		header('Location: wine_list.php?sortby=0');
	}
}
#=================================================================
#  Login Page Processing Section
#=================================================================
if (isset($_POST['changepassname']))
{
	$result=run_select_on_db("SELECT password from sl_users WHERE (Handle='".$reset_handle."')",$con);
	while($row = mysql_fetch_array($result))
	{
		$password_to_compare=$row['password'];
	}
	if ($password_to_compare == md5($reset_oldpword))
	{
		$result=run_select_on_db("UPDATE sl_users SET password= '".md5($reset_newpword)."' where Handle='".$reset_handle."'",$con);
		echo "Password correct.";
		header('Location: change_pass.php?old_pass_wrong=0');
	}
	else
	{
		echo "Password wrong.";
		header('Location: change_pass.php?old_pass_wrong=1');
	}
	
}

if (isset($_POST['resetpass']))
{
	$new_password=randomPassword();
	$result=run_select_on_db("SELECT email from sl_users WHERE (Handle='".$home_handle."')",$con);
	while($row = mysql_fetch_array($result))
	{
		$email_addie=$row['email'];
	}	
	$to      = $email_addie;
	$subject = 'Password Reset';
	$message = 'This email is from Lists4Home.\n\n At your request your password has been reset to '.$new_password;
	$headers = 'From: info@lists4home.com' . "\r\n" .
    'Reply-To: info@lists4home.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

	$result=run_select_on_db("UPDATE sl_users SET password= '".md5($new_password)."' where Handle='".$home_handle."'",$con);
	
	mail($to, $subject, $message, $headers);	
	
	header('Location: login.php?reset=1');
}

if (isset($_POST['login']))
{
	$sql="SELECT * from sl_users WHERE (Handle='".$home_handle."') and (Password = '".md5($home_pass)."')";	
	$login=mysql_query($sql,$con);
	if (!$login)
	{
		die('Error: ' . mysql_error());
	}
	#This is all test code - it can be removed----------------
	echo "Passed: ".$home_handle."-".$home_pass."!<br>";
	echo "Handle: ".$home_handle."!<br>";
	echo "MD5: ".md5($home_pass)."!<br>";
	echo $sql."<br>";
	echo $login."<br>";
	echo "Number of rows: ".mysql_num_rows($login)."<br>";
	#---------------------------------------------------------
	
	if (mysql_num_rows($login) == 1)
	{
		while($row = mysql_fetch_array($login))
		{
			$_SESSION['user_id']=$row['User_ID'];
		}
		$_SESSION['username']=$home_handle;		
		$identifier=$home_handle;
		$token=md5(uniqid(rand(),TRUE));
		$timeout=time() + (20 * 365 * 24 * 60 * 60);
		
		#remove the already existing session from the session list
		if (isset($_COOKIE['auth']))
		{
			$processed_cookie=explode(":", $_COOKIE['auth']);	
			$sql="DELETE FROM forhome_sessions WHERE homeid='$processed_cookie[0]' AND token='$processed_cookie[1]'";
			if (!mysql_query($sql,$con))
			{
				die('Error: ' . mysql_error());
			}
		}
		setcookie('auth',"$identifier:$token",$timeout);
		
		$sql="INSERT INTO forhome_sessions(homeid,token) VALUES('$identifier','$token')";
		if (!mysql_query($sql,$con))
		{
			die('Error: ' . mysql_error());
		}

		echo $_SESSION['user_id']."<br>".$_SESSION['username'];
		header('Location: shopping_list.php');
	}
	else
	{
		echo "Not Found!";		
		header('Location: login.php?failed=1');
	}
}

if (isset($_POST['register']))
{
	$sql1="SELECT MAX(User_ID) AS Max_User_ID FROM sl_users";
		
	$result=mysql_query($sql1,$con);
	while($row = mysql_fetch_array($result))
	{
		$max_user_id=$row['Max_User_ID']+1;
	}
			
	$sql2="INSERT INTO sl_users(Handle,User_Name,User_Surname,Password,Email,User_ID)VALUES('$home_handle','$home_reg_username','$home_reg_surname','".md5($home_pass)."','$home_emailname','$max_user_id')";

	if (!mysql_query($sql2,$con))
	{
		die('Error: ' . mysql_error());
	}
	header('Location: login.php');	
}

#=================================================================
#  Setup Items Page Processing Section
#=================================================================
if (isset($_POST['ManageItems']))
{
	header("Location: setupitems.php");
}

if (isset($_POST['BackToList']))
{
	header("Location: shopping_list.php");
}

if (isset($_POST['AddItem']))
{	
	#add a new category to the database should one exist		
	if ($new_category != "")
	{
		$sql1="SELECT MAX(Category_ID) AS Max_Cat_ID FROM sl_categories";
		
		$result=mysql_query($sql1,$con);
		while($row = mysql_fetch_array($result))
		{
			$max_category_id=$row['Max_Cat_ID']+1;
		}
				
		$sql2="INSERT INTO sl_categories(Category_ID,Category)VALUES('$max_category_id','$new_category')";

		if (!mysql_query($sql2,$con))
		{
			die('Error: ' . mysql_error());
		}		
	}
	
	#check to see if the item already exists if it already exists the item will not be added
	$sql1="SELECT Item_ID FROM sl_items WHERE Item='$new_item'";		
	$result=mysql_query($sql1,$con);
	
	#add a new item to the database
	if ($new_item != "" && !mysql_num_rows($result))
	{
		$sql1="SELECT Item_ID FROM sl_items WHERE Item='$new_item'";	
		
		$result=mysql_query($sql1,$con);
			
		#get the next item ID	
		$sql1="SELECT MAX(Item_ID) AS Max_Item_ID FROM sl_items";
		
		$result=mysql_query($sql1,$con);
		while($row = mysql_fetch_array($result))
		{
			$max_item_id=$row['Max_Item_ID']+1;
		}
			
		#now get the category id for the stipulated category	
		if ($new_category == "")	
		{
			$sql_query_cat=$_POST['catList'];
		}
		else 
		{
			$sql_query_cat=$new_category;
		}
		
		$sql2="SELECT Category_ID FROM sl_categories WHERE Category='$sql_query_cat'";	
		
		$result=mysql_query($sql2,$con);
		while($row = mysql_fetch_array($result))
		{
			$retrieved_cat_id=$row['Category_ID'];
		}
					
		$result=run_select_on_db("INSERT INTO sl_items(Item,Item_ID,Category_ID)VALUES('$new_item','$max_item_id','$retrieved_cat_id')",$con);
	}
	header("Location: setupitems.php");	
}

#=================================================================
#  Shopping List Page Processing Section
#=================================================================
if (isset($_POST['RemoveItems']))
{
	#OPTIMISATION: This could be done in one query instead of multiple queries.
	$shopping=$_POST['shopping_list'];
	foreach ($shopping as $one_item)
	{
		echo $one_item."<br>";
		$result=run_select_on_db("INSERT INTO sl_bought(Item,PurchasedDate,User_ID)VALUES('$one_item',CURDATE(),'".$_SESSION['user_id']."')",$con);
		$result=run_select_on_db("DELETE FROM  sl_list WHERE Item_ID=(SELECT Item_ID FROM sl_items WHERE Item='$one_item')",$con);
	}
	header("Location: shopping_list.php");
}

if (isset($_POST['SubmitToList']))
{
	#check to see if the item already exists if it already exists the item will not be added
	$result=run_select_on_db("SELECT Item_ID FROM sl_items WHERE Item='$new_item'",$con);
		
	#if this is a new item - create it
	if (($new_item != "") && ($_COOKIE['UnknownItem'] == 1) && (!mysql_num_rows($result)))
	{		
		#get the next item ID	
		$result=run_select_on_db("SELECT MAX(Item_ID) AS Max_Item_ID FROM sl_items",$con);		
		
		while($row = mysql_fetch_array($result))
		{
			$max_item_id=$row['Max_Item_ID']+1;
		}
			
		#now get the category id for the stipulated category	
		if ($new_category == "")	
		{
			$sql_query_cat=$_POST['catList'];
		}
		else 
		{
			$sql_query_cat=$new_category;
		}
		
		$result=run_select_on_db("SELECT Category_ID FROM sl_categories WHERE Category='$sql_query_cat'",$con);	
		
		while($row = mysql_fetch_array($result))
		{
			$retrieved_cat_id=$row['Category_ID'];
		}
					
		$result=run_select_on_db("INSERT INTO sl_items(Item,Item_ID,Category_ID)VALUES('$new_item','$max_item_id','$retrieved_cat_id')",$con);		
	}
	
	#add item to the list
	$result=run_select_on_db("SELECT Item_ID,Category_ID FROM sl_items WHERE Item='$new_item'",$con);	
	while($row = mysql_fetch_array($result))
	{
		$item_id=$row['Item_ID'];
		$cat_id=$row['Category_ID'];
		echo "Item: $item_id Cat:$cat_id<br>";
	}
	
	if ($_COOKIE['UnknownBarcode'] == 1)
	{		
		$result=run_select_on_db("INSERT INTO sl_barcodes(barcode,Item_ID) VALUES('$bar_code','$item_id')",$con);
		$_COOKIE['UnknownBarcode']=0;
	}
	$result=run_select_on_db("INSERT INTO sl_list(Item_ID,Category_ID,User_ID,ItemComment) VALUES('$item_id','$cat_id','".$_SESSION['user_id']."','$item_comment')",$con);
	
	header("Location: shopping_list.php");		
}

//Close the database
mysql_close($con);
?>
	
</body>
</html>