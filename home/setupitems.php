<!DOCTYPE html>
<?php

include 'generic_php.php';
include 'logscript_inc.php';

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

$(document).ready(function() 
{
   $(function() {
		var availableTags = [
		<?php 
		$sql="SELECT * FROM sl_items";
		$result=mysql_query($sql,$con);
		while($row = mysql_fetch_array($result))
		{
			echo "\"".$row['Item']."\",";
		} 
		?>			
		];
		$("#tags").autocomplete({source: availableTags});
	});
});

function checkselection (theelement) 
{
	if (document.getElementById("selectList").selectedIndex == (document.getElementById("selectList").length-1))
	{
		show("cat_insert");
	}
	else
	{
		hide("cat_insert");
	}
}
</script>
<title>Home Item Setup</title>
</head>

<body>
<form name="AddItem" action="process.php" method="post">
	<table border=0 width="100%">
		<tr>
			<th colspan=4 align="center"><h1>Add A New Item</h1></th>
		</tr>
		<tr>
			<td colspan="2">Item Name: <input type="text" name="item" size="20" maxlength="50" class="textfield"/></td>
			<td colspan="2">Category:
				<select name="catList" id="selectList" onchange="checkselection(this)">
				<?php
					//Select all from database	
					$sql="SELECT sl_categories.Category FROM sl_categories";
					$result=mysql_query($sql,$con);
					if (!$result)
					{
						die('Query Error: ' . mysql_error());
					}
					else 
					{
						while($row = mysql_fetch_array($result))
						{
							echo "<option>".$row['Category']."</option>\n";
						}
					}
				?>
				<option>(*Create New)</option>			  
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2"></td>
			<td colspan="2">
				<table border=0 id="cat_insert" style="display: none;">
					<tr>
						<td>New Category: <input type="text" name="category" size="20" maxlength="50" class="textfield"/></td>
					</tr>
				</table>			
			</td>
		</tr>			
		<tr>
			<td><input type="submit" name="BackToList" value="Back To List"/></td>
			<td><input type="submit" name="AddItem" value="Add Item"/></td>
			<td colspan="2"></td>
		</tr>
	</table>
	<table border="1">
		<tr><th>Item</th><th>Category</th><!--<th>Flag</th>--></tr>
		<?php
			//Select all from database	
			$button_index=0;
			$sql="SELECT sl_items.Item,sl_categories.Category FROM sl_items,sl_categories WHERE sl_categories.Category_ID=sl_items.Category_ID ORDER BY sl_items.Category_ID";
			$result=mysql_query($sql,$con);
			if (!$result)
			{
				die('Query Error: ' . mysql_error());
			}
			else 
			{
				while($row = mysql_fetch_array($result))
				{
					$button_index=$button_index+1;
					echo "<tr>";	
					echo "<td>".$row['Item']."</td>";
					echo "<td>".$row['Category']."</td>";
					//echo "<td style=\"text-align: center;padding: 8px\"><input type=\"checkbox\" class=\"name=\"shopping_list[]\" value=\"".$row['Items']."\" /></td>";
					echo "</tr>";
				}
			}			
		?>
	</table>	
	<?php
		//Close the database
		mysql_close($con);
	?>
</form>
</body>
</html>