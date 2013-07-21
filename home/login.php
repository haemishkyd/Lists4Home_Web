<!DOCTYPE html>
<?php

include 'generic_php.php';

?>
<html>
<head>
	<title>Lists4Home</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="../themes/lists4home.min.css"/>
	<link rel="stylesheet" href="../css/ui-lightness/jquery-ui-1.8.23.custom.css" />
	<link rel="stylesheet" href="../css/mjQuery/jquery.mobile.custom.structure.min.css" />
	<link rel="stylesheet" href="../css/mobile_style.css" />

	<script src="../js/libs/jquery-1.8.0.min.js"></script>
	<script src="../js/libs/jquery-ui-1.8.23.custom.min.js"></script>
	<script src="../js/mjquery/jquery.mobile.custom.min.js"></script>
	<script src="../js/forhome.js"></script>

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
	if (document.getElementById('register_block').style.display == 'block')
	{
		if ($.inArray(document.getElementById("homehandleid").value, currentUsers) != -1)
		{
			alert("Home Name is not available.");
			return false;
		}
				
		if (document.getElementById("homepasswordid").value != document.getElementById("confirmhomepasswordid").value)
		{
			alert("Passwords do not match!!");
			return false;
		}
		
		if (document.getElementById("emailnameid").value == "")
		{
			alert("You must provide an email address. This is for password reset purposes,\nwe will never send you any other emails.");
			return false;
		}
	}
	else
	{
		if ($.inArray(document.getElementById("homehandleid").value, currentUsers) == -1)
		{
			alert("Apologies, we don't know who you are.\nHave you typed your Home ID correctly?");
			return false;
		}
	}
	return true;
}

function checkHomeName()
{
	if (document.getElementById('homehandleid').value.length > 0)
	{
		document.getElementById("loginid").disabled = false;
		document.getElementById("resetpassid").disabled = false;
	}
	else
	{
		document.getElementById("loginid").disabled = true;
		document.getElementById("resetpassid").disabled = true;
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
			<th colspan="2"><h1>Please Log In</h1></th>
		</tr>
		<tr>
			<td style="width: 50%;">Home Name: </td><td style="width: 50%;"><input type="text" id="homehandleid" name="homehandlename" value="" onkeyup="checkHomeName()"/></td>
		</tr>
		<tr>
			<td>Password: </td><td><input type="password" id="homepasswordid" name="homepasswordname" value=""/></td>
		</tr>
		<div >
		<tr>
			<td colspan="2">
				<div id="register_block" style="display: none;">
				<table border="0" style="width: 100%;">
					<tr>
						<td style="width: 50%;">Confirm Password: </td><td style="width: 50%;"><input type="password" id="confirmhomepasswordid" name="confirmhomepasswordname" value=""/></td>
					</tr>
					<tr>
						<td>Name: </td><td><input type="text" name="reg_username" value=""/></td>
					</tr>
					<tr>
						<td>Surname: </td><td><input type="text" name="reg_surname" value=""/></td>
					</tr>
					<tr>
						<td>Email: </td><td><input type="text" id="emailnameid" name="emailname" value=""/></td>
					</tr>
					<tr>
						<td colspan="2"><input type="submit" name="register" value="Register"/></td>			
					</tr>
				</table> 
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2" >
				<div id="loginbutton" style="display: block;">
				<table border="0" style="width: 100%;">
					<tr>
						<td>
							<?php if(isset($_GET['failed']) && $_GET['failed'] == "1") {  
							echo "Username or Password not recognised. Please try again.";
							} ?>
						</td>
					</tr>
					<tr>
						<td>
							<?php if(isset($_GET['reset']) && $_GET['reset'] == "1") {  
							echo "Your password has been changed. <br>Please refer to your email address provided for your new password.";
							} ?>
						</td>
					</tr>
					<tr>
						<td><input type="submit" name="login" id="loginid" value="Login" disabled="disabled"/></td>
					</tr>
					<tr>
						<td><input type="submit" name="resetpass" id="resetpassid" value="Reset Password" disabled="disabled"/></td>
					</tr>
				</table>
				</div>
			</td>			
		</tr>
		<tr>
			<td colspan="2"><a href="#" onclick="toggle('register_block','loginbutton')">Click Here to Register</a></td>			
		</tr>
	</table>		
	<?php
		//Close the database
		mysql_close($con);
	?>
</form>
</body>
</html>