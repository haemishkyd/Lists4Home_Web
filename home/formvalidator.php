<?php

include 'generic_php.php';
include 'logscript_inc.php';

$con=open_database_connection();

header("Content-Type: text/plain");
header("Cache-Control: no-cache");

if (isset( $_GET["barcode"])) 
{
	$output = 0;

	$result=run_select_on_db("SELECT item.Item FROM sl_barcodes bc,sl_items item WHERE bc.barcode='".$_GET["barcode"]."' AND bc.Item_ID=item.Item_ID",$con);
	while($row = mysql_fetch_array($result))
	{
		$output=$row['Item'];
	}
	
	echo $output;	
} 
elseif (isset( $_GET["whatbought"])) 
{
	$output="";
	$result=run_select_on_db("SELECT Item,PurchasedDate FROM sl_bought WHERE User_ID='".$_SESSION['user_id']."' AND PurchasedDate BETWEEN DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND CURDATE()",$con);
	echo "<table width=\"100%\" height=\"100%\" class=\"popup_table_style\">";
	echo "<tr>";
	echo "<td style=\"text-align: center;\" colspan=2><a href=\"#\" onclick=\"closeBought()\">Close</a></td>";		
	echo "</tr>";
	echo "<tr>";
	echo "<th>Item</th>";
	echo "<th style=\"text-align: center;\">Purchase Date</th>";
	echo "</tr>";
	while($row = mysql_fetch_array($result))
	{
		echo "<tr>";
		echo "<td>".$row['Item']."</td>";
		echo "<td style=\"text-align: center;\">".$row['PurchasedDate']."</td>";
		echo "</tr>";
	}	
	echo "</table>";
}
else 
{
	echo "Nothing was sent to this page!";
}
?>