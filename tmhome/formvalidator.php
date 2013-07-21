<?php

include 'generic_php.php';

//Initialise the session
session_start();

#this gets all the users and adds them to a list for checking
$con = open_database_connection();

if (isset( $_POST["whatbought"])) 
{
	$output="";
	$result=run_select_on_db("SELECT Item,PurchasedDate FROM sl_bought WHERE User_ID='".$_SESSION['user_id']."' AND PurchasedDate BETWEEN DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND CURDATE() ORDER BY PurchasedDate DESC",$con);
	echo "<a href=\"#\" data-role=\"button\" onclick=\"closeBought()\">Close</a>";		
	
	echo "<ul id=\"bought_list\" data-role=\"listview\">";
	while($row = mysql_fetch_array($result))
	{
		echo "<li>".$row['Item'];
		echo "<span class=\"ui-li-count\">".$row['PurchasedDate']."</span></li>";
	}	
	echo "</ul>";
}
else 
{
	echo "Nothing was sent to this page!";
}
?>