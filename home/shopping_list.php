<!DOCTYPE html>
<?php

include 'generic_php.php';
include 'logscript_inc.php';

?>
<html manifest="../lists4home.manifest">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<script type="text/javascript" src="../js/libs/jquery-1.8.0.js"></script>
<script type="text/javascript" src="../js/libs/jquery-ui-1.8.23.custom.min.js"></script>
<script type="text/javascript" src="../js/forhome.js"></script>
<link rel="stylesheet" type="text/css" href="../css/ui-lightness/jquery-ui-1.8.23.custom.css" />
<link rel="stylesheet" type="text/css" href="../css/shopping_list.css" />
<script type="text/javascript">

var availableTags;
var lastScanned;

function checkBarcode(userValue) 
{    
    var url = "formvalidator.php?barcode=" + userValue;
    lastScanned=userValue;
    var request = new HttpRequest(url, checkBarcode_callBack);
    request.send();
}

function registerAJAX(whichone,userValue,item) 
{    
	if (whichone==2)
	{
	    var url = "formvalidator.php?whatbought=" + userValue;
	    lastScanned=userValue;
	    var request = new HttpRequest(url, getAlreadyBought_callBack);
	    genericPopupBox(item);
    }
    request.send();
}

function getAlreadyBought_callBack(sResponseText) 
{
	document.getElementById("popuprel").innerHTML=sResponseText;
}

function checkBarcode_callBack(sResponseText) 
{    
    if (sResponseText!=0)
    {
    	SetCookie("UnknownBarcode",0,1);
	    document.getElementById("tags").value=sResponseText;
	    document.getElementById("SubmitToListID").click();   
    }
    else
    {
    	document.getElementById("headerfield").innerHTML="Unknown Barcode: Please Enter Details"
    	ToggleElementState('addshopping');
    	document.getElementById("barcodeid").value=lastScanned;
    	SetCookie("UnknownBarcode",1,1);
    }
}

$(document).ready(function() 
{
   $(function() 
   {
		availableTags = [
		<?php 
		#this gets all the items and adds them to the auto-complete list		
		$result=run_select_on_db("SELECT * FROM sl_items",$con);
		while($row = mysql_fetch_array($result))
		{
			echo "\"".$row['Item']."\",";
		} 
		?>			
		];
		
		$("#tags").autocomplete({
			source: availableTags,
			select: function(e,ui)
			{
				document.getElementById("tags").value=ui.item.value;	
			}
		});
	});
});

function closeBought()
{
	$('#fade , #popuprel').fadeOut();
}

/*
 * This function checks the state of the check box and toggles it. It also changes
 * the picture based on that toggled state.
 * Called: From the table elements that make up the list (checkbox and the text itself)
 */
function ChangeCheckBox(cbid,pic,id)
{
	if (document.getElementById(cbid).checked==false)
	{
		document.getElementById(pic).src = "../images/checkbox-checked-md.png";
		document.getElementById(cbid).checked=true;
	}
	else
	{
		document.getElementById(pic).src = "../images/checkbox-unchecked-md.png";
		document.getElementById(cbid).checked=false;
	}	
	ToggleElementState(id);
}

/*
 * This function checks if the new shopping item is in the database already.
 * If so it submits to the processing page. If not it will show the category
 * chooser. If we come back here after the category is chosen, we will then submit
 * the item to the processing page (as a completely unknown item - by setting the unknownItem 
 * 	cookie to 1).
 * Called: From the form element
 */
function validateSubmit(theelement) 
{	
	$main_return_value=1;
	
	if (document.getElementById("commenttags").style.color=="rgb(136, 136, 136)")
	{
		document.getElementById("commenttags").value="";
		$main_return_value=1;
	}
			
	/* This gets whether or not the category chooser is being displayed */
	$display_state=document.getElementById("cat_choose").style.display;
	
	if ($display_state=="block" || document.getElementById("tags").value.length == 0)
	{
		//we assume that since the cat chooser is shown we can now submit
		$main_return_value=1;
	}		
	else if ($.inArray(document.getElementById("tags").value, availableTags) != -1)
	{
		//item is in the list so just submit
		hide("cat_choose");
		hide("cat_insert");
		SetCookie("UnknownItem",0,1);		
		$main_return_value=1;			
	}
	else
	{
		//item is not in the list so show the category chooser
		show("cat_choose");
		SetCookie("UnknownItem",1,1);
		$main_return_value=0;		
	}
	
	if ($main_return_value==1)
	{
		return true;
	}
	else
	{
		return false;
	}
}

/*
 * This function unticks all of the checkboxes in the shopping list
 * and makes sure the input field is blank as the page loads. 
 * Called: From the body element
 */
function LoadFunction()
{
	/* This is to make sure the unknown item cookie is set correctly */
	SetCookie("UnknownItem",0,1);
	SetCookie("UnknownBarcode",0,1);
	for (var i=0; i < document.AddToList.elements.length; i++)
	{
		if (document.AddToList.elements[i].type == "checkbox")
		{
			document.AddToList.elements[i].checked = false;
		}
	}
	document.getElementById("tags").value="";
	/* This must always be last */
	highlightMenu('link3');		
}

</script>
<title>Home Shopping List</title>
</head>

<body onload="LoadFunction()">
<form name="AddToList" action="process.php" method="post" onsubmit="return validateSubmit();">
<p class="smalllink"><?php echo "Hello ".$_SESSION['username']."!";?><a href="login.php">Not <?php echo $_SESSION['username'];?>? Click Here.</a></p>
<table width="100%"><tr><td><input type="button" value="Add Items To List" onclick="ToggleElementState('addshopping')"/></td><td><input type="submit" name="RemoveItems" value="Remove Items"/></td>
	<td><input type="button" onclick="registerAJAX(2,1,this)" rel="popuprel" class="popup" value="Bought this Week"/></td></tr></table>
	<input type="hidden" name="barcodename" id="barcodeid" value="" />
	<div id="addshopping" style="display: none;">
	<table border="0" width="100%">
		<tr>
			<th colspan="3"><h1 id="headerfield">Add Item to Shopping List</h1></th>
		</tr>
		<tr>
			<td  colspan="2">Item: <input id="tags" autocomplete="off" name="item"  value="Start typing..." style="color:#888;" onfocus="inputFocus(this)" onblur="inputBlur(this)"/></td>
			<td  colspan="2">Comment: <input id="commenttags" name="commentitem" value="Measurement,Quantity,etc" style="color:#888;" onfocus="inputFocus(this)" onblur="inputBlur(this)"/></td>
		</tr>
		<tr>
			<td colspan="4">
				<table border=0 id="cat_choose" style="display: none;">
					<tr>
					<td style="width: 40;">Select New Item's Category:</td><td>
						<select name="catList" id="selectList">
						<?php
							//Select all from database	
							$result=run_select_on_db("SELECT sl_categories.Category FROM sl_categories ORDER BY sl_categories.Category",$con);							
							while($row = mysql_fetch_array($result))
							{
								echo "<option>".$row['Category']."</option>\n";
							}
						?>									  
						</select>
					</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<table border=0 id="cat_insert" style="display: none;">
					<tr>
						<td style="width: 40;">New Category:</td>
						<td><input type="text" name="category" size="20" maxlength="50" class="textfield"/></td>
					</tr>
				</table>			
			</td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" name="SubmitToList" id="SubmitToListID" value="Submit Items"/></td>			
			<td colspan="2"><!--input type="submit" name="ManageItems" value="Manage Items"/--></td>
		</tr>
	</table>
	</div>
	<hr>
	<table border="0" width="100%">
		<tr><th style="width: 10%;"></th><th style="width: 90%;text-align: left;">Item</th></tr>
		<?php			
			//Select all from database	
			$button_index=0;
			$result=run_select_on_db("SELECT sl_items.Item,sl_list.ItemComment FROM sl_list,sl_items WHERE sl_items.Item_ID=sl_list.Item_ID AND sl_list.User_ID='".$_SESSION['user_id']."' ORDER BY sl_list.Category_ID",$con);
			while($row = mysql_fetch_array($result))
			{
				$button_index=$button_index+1;
				echo "<tr>";
				#new cell
				echo "<td class=\"checkbox_padding\">";
				echo "<div style=\"display:block\" onclick=\"ChangeCheckBox('Check".$button_index."','CheckBox".$button_index."','Element".$button_index."')\" >";
				echo "<img id=\"CheckBox$button_index\" src=\"../images/checkbox-unchecked-md.png\" height=\"40\">";
				echo "<input style=\"display: none;\" type=\"checkbox\" id=\"Check$button_index\" name=\"shopping_list[]\" value=\"".$row['Item']."\" onclick=\"ChangeState('Element".$button_index."')\" />";
				echo "</div>";
				echo "</td>";
				#new cell	
				echo "<td><div id=\"Element".$button_index."\" style=\"display:block\" onclick=\"ChangeCheckBox('Check".$button_index."','CheckBox".$button_index."','Element".$button_index."')\" >";
				echo $row['Item'];
				if ($row['ItemComment'] != "")
				{
					echo "<a style=\"font-style:italic;text-decoration: none;color: #AFAFAF;\"> : ".$row['ItemComment']."</a>";
				}
				echo "</div></td>";
				echo "</tr>";
			}
		?>
	</table>		
	<?php
		//Close the database
		mysql_close($con);
	?>
</form>
<img src="../images/checkbox-checked-md.png" style="display: none"/>
<div class="popupbox" id="popuprel"></div>
</body>
</html>