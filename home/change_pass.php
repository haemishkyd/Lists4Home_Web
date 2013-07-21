<!DOCTYPE html>
<?php

include 'generic_php.php';

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<script type="text/javascript" src="../js/libs/jquery-1.8.0.js"></script>
<script type="text/javascript" src="../js/libs/jquery-ui-1.8.23.custom.min.js"></script>
<script type="text/javascript" src="../js/forhome.js"></script>
<link rel="stylesheet" type="text/css" href="../css/ui-lightness/jquery-ui-1.8.23.custom.css" />
<link rel="stylesheet" type="text/css" href="../css/shopping_list.css" />
<script type="text/javascript">

currentUsers = [
		<?php 
		#this gets all the users and adds them to a list for checking
		$con=open_database_connection();
		
		$sql_user="SELECT Handle FROM sl_users";
		$result=mysql_query($sql_user,$con);
		if (!$result)
		{
			die('Error: ' . mysql_error());
		}
		while($row = mysql_fetch_array($result))
		{
			echo "\"".$row['Handle']."\",";
		} 
		?>
		];
		
function toggle(id1,id2)
{
	if (document.getElementById(id1).style.display == 'none')
	{
		show(id1);
		hide(id2);
	}
	else
	{
		show(id2);
		hide(id1);
	}
}

function validateSubmit(theelement) 
{
	if ($.inArray(document.getElementById("changehomehandleid").value, currentUsers) == -1)
	{
		alert("Apologies, we don't know who you are.\nHave you typed your Home ID correctly?");
		return false;
	}
		
	if (document.getElementById("newpasswordid").value != document.getElementById("repeatpasswordid").value)
	{
		alert("Passwords do not match!!");
		return false;
	}		
	return true;
}

function checkHomeName()
{
	if (document.getElementById('changehomehandleid').value.length > 0)
	{
		document.getElementById("changepassid").disabled = false;		
	}
	else
	{
		document.getElementById("changepassid").disabled = true;
	}
}

function LoadFunction()
{
	highlightMenu('linkend');
}
</script>
<title>Home Shopping List</title>
</head>

<body onload="LoadFunction()">
<form name="Login" action="process.php" method="post" onsubmit="return validateSubmit();">
	<?php
	$con=open_database_connection();
	?>
	<table border="0" style="width: 100%;">
		<tr>
			<th colspan="2"><h1>Change Password</h1></th>
		</tr>
		<tr>
			<td colspan="2">
			<?php if(isset($_GET['old_pass_wrong']) && $_GET['old_pass_wrong'] == "1") {  
							echo "Your old password was entered incorrectly.<br>";
							} ?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
			<?php if(isset($_GET['old_pass_wrong']) && $_GET['old_pass_wrong'] == "0") {  
							echo "Password successfully changed.<br>";
							} ?>
			</td>
		</tr>
		<tr>
			<td style="width: 50%;">Home Name: </td><td style="width: 50%;"><input type="text" id="changehomehandleid" name="changehomehandlename" value="" onkeyup="checkHomeName()"/></td>
		</tr>
		<tr>
			<td>Old Password: </td><td><input type="password" id="oldpasswordid" name="oldpasswordname" value=""/></td>
		</tr>
		<tr>
			<td>New Password: </td><td><input type="password" id="newpasswordid" name="newpasswordname" value=""/></td>
		</tr>
		<tr>
			<td>Repeat New Password: </td><td><input type="password" id="repeatpasswordid" name="repeatpasswordname" value=""/></td>
		</tr>
		<tr>
			<td><input type="submit" name="changepassname" id="changepassid" value="Change Password" disabled="disabled" "/></td><td></td>
		</tr>		
	</table>		
	<?php
		//Close the database
		mysql_close($con);
	?>
</form>
</body>
</html>