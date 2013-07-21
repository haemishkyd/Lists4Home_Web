<!DOCTYPE html>
<?php
$sort_flag=0;

include 'generic_php.php';
include 'logscript_inc.php';


if(isset($_GET['sortby'])) {
	switch ($_GET['sortby'])
	{
		case 0:
			$sort_flag=0;
			break;
		case 1:
			$sort_flag=1;
			break;
		case 2:
			$sort_flag=2;
			break;
		default:
			$sort_flag=0;
			break;	
	}
}
else {
	$sort_flag=$_COOKIE['BlendSort'];
} 

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
$last_blend_id=0;

function validateSubmit() 
{	
	return true;
}

function GetRemove(idValue)
{
	SetCookie("ItemToRemove",idValue,1);
}

function DoExpand(element_id,pic_id)
{
	if (document.getElementById(element_id).style.display == 'block')
	{
		document.getElementById(pic_id).src = "../images/wine_glass_up.png";
	}
	else
	{
		document.getElementById(pic_id).src = "../images/wine_glass_down.png";
	}
	ToggleElementState(element_id);
}

function ShowNewFarm (theelement) 
{
	if (document.getElementById("selectFarmList").selectedIndex == (document.getElementById("selectFarmList").length-1))
	{
		show("newfarmname");
	}
	else
	{
		hide("newfarmname");
	}
}

function SelectVar(id)
{
	if (document.getElementById("chck"+id).checked==false)
	{
		document.getElementById(id).style.color="rgb(0, 100, 0)";
		document.getElementById("chck"+id).checked=true;
	}
	else
	{
		document.getElementById(id).style.color=document.getElementById("col"+id).value;
		document.getElementById("chck"+id).checked=false;
	}
}

function LoadFunction()
{
	blend_sort=GetCookie("BlendSort");
	if (document.cookie.indexOf("BlendSort")<=0)
	{
		blend_sort=0;
	}
	
	if (blend_sort==0)
	{
		document.getElementById("sortButton").value="Sorty By Year";
	}
	else if (blend_sort==1)
	{
		document.getElementById("sortButton").value="Sorty By Farm";
	}
	else
	{
		document.getElementById("sortButton").value="Sorty By Varietal";
	}
	/* This must always be last */
	highlightMenu('link4');
}

$(document).ready(function() {						   
	// Here we will write a function when link click under class popup				   
	$('div.popup').click(function() {										
		// Here we will describe a variable popupid which gets the
		// rel attribute from the clicked link							
		var popupid = $(this).attr('rel');		
		
		// Now we need to popup the marked which belongs to the rel attribute
		// Suppose the rel attribute of click link is popuprel then here in below code
		// #popuprel will fadein
		$('#' + popupid).fadeIn();
				
		ToggleElementState('popup'+this.id);
		$last_blend_id=this.id;
		
		// append div with id fade into the bottom of body tag
		// and we allready styled it in our step 2 : CSS
		$('body').append('<div id="fade"></div>');
		$('#fade').css({'filter' : 'alpha(opacity=80)'}).fadeIn();		
		
		// Now here we need to have our popup box in center of 
		// webpage when its fadein. so we add 10px to height and width 
		var popuptopmargin = ($('#' + popupid).height() + 10) / 2;
		var popupleftmargin = ($('#' + popupid).width() + 10) / 2;
				
		// Then using .css function style our popup box for center allignment
		$('#' + popupid).css({
		'margin-top' : -popuptopmargin,
		'margin-left' : -popupleftmargin
		});
	});		
		
	// Now define one more function which is used to fadeout the 
	// fade layer and popup window as soon as we click on fade layer
	$('#popupclose').click(function() {	
		// Add markup ids of all custom popup box here 						  
		$('#fade , #popuprel').fadeOut()
		if ($last_blend_id!=0)
		{
			ToggleElementState('popup'+$last_blend_id);
			$last_blend_id=0;
		}		
		return false;
	});
});
</script>
<title>Home Wine List</title>
</head>

<body onload="LoadFunction()">
<form name="AddToList" action="process.php" method="post" onsubmit="return validateSubmit();">
<p class="smalllink"><?php echo "Hello ".$_SESSION['username']."!";?><a href="login.php">Not <?php echo $_SESSION['username'];?>? Click Here.</a></p>
<table border=0 width="100%"><tr>
	<td><input type="button" value="Add Wine To List" onclick="ToggleElementState('addwine')"/></td>
	<td><input type="submit" id="sortButton" name="sortButton" value="Sort By Year"/></td>
</tr></table>
<div id="addwine" style="display: none;">
<table border=0 width="100%">
	<tr>
		<th colspan="4"><h1>Add Item to Wine List</h1></th>
	</tr>
	<tr>
		<!-- Select Farm from List -->
		<td colspan="2" width="50%">
			<table border=0 id="farm_choose" width="100%">
				<tr>
				<td width="30%">Farm:</td>
				<td width="70%">
					<select name="farmList" id="selectFarmList" onchange="ShowNewFarm(this)">
					<?php
						//Select all from database	
						$result=run_select_on_db("SELECT wine_farms.Farm FROM wine_farms ORDER BY wine_farms.Farm",$con);							
						while($row = mysql_fetch_array($result))
						{
							echo "<option>".$row['Farm']."</option>\n";
						}				
						echo "<option>(*Create New)</option>\n";			
					?>									  
					</select>
				</td>
				</tr>
			</table>
		</td>
		<td colspan="2"  width="50%">
			<table width="100%" border=0 id="newfarmname" style="display: none;">
				<tr>
					<td width="40%">New Farm:</td><td width="60%"><input type="text" name="NewFarmName" class="textfield"/></td>
				</tr>
			</table>			
		</td>
	</tr>
	<tr>
		<!-- Enter Number of Bottles -->			
		<td colspan="2" width="50%">
			<table border=0 id="bottles_insert" width="100%">
				<tr>
					<td width="30%">No. of Bottles:</td>
					<td width="70%"><input type="text" name="NoBottles" class="textfield"/></td>
				</tr>
			</table>	
		</td>
		<!-- Select Year from List -->
		<td colspan="2" width="50%">
			<table border=0 id="year_choose" width="100%">
				<tr>
				<td width="40%">Year:</td><td width="60%">
					<select name="yearList" id="selectYearList">
					<?php
					for ($myyear=1970;$myyear<2013;$myyear++)	
					{
						echo "<option";
						if ($myyear==2009)
						{
							echo " selected";
						}
						echo ">".$myyear."</option>\n";
					}
					?>									  
					</select>
				</td>
				</tr>
			</table>						
		</td>
	</tr>
	<tr>
		<!-- Enter Comment -->
		<td colspan="2" width="50%">
			<table border=0 id="bottles_insert" width="100%">
				<tr>
					<td width="30%">Comment:</td>
					<td width="70%"><textarea rows="5" name="WineComment"></textarea></td>
				</tr>
			</table>			
		</td>
		<td colspan="2"></td>
	</tr>
	<tr>
		<!-- Enter Blend -->
		<td colspan="4" width="100%">
			Click on the varietals present in the wine.
			<table border=1 width="100%">
				<?php
					$blend_counter=0;
					$result=run_select_on_db("SELECT wine_varietals.Varietal,wine_varietals.Varietal_ID,wine_varietals.Type FROM wine_varietals ORDER BY wine_varietals.Type,wine_varietals.Varietal",$con);
					while($row = mysql_fetch_array($result))
					{
						$blend_counter++;
						if ($blend_counter==1)
						{
							echo "<tr>";
						}
						echo "<td><div id=\"".$row['Varietal_ID']."\" style=\"color:";
							#Colour of the name red or white
							if ($row['Type']==0)
							{
								echo "#710909";
							}
							else 
							{
								echo "#FFFFFF";	
							}
						echo ";\" onclick=\"SelectVar('".$row['Varietal_ID']."')\"><input type=\"hidden\" id=\"col".$row['Varietal_ID']."\" value=\"";
						if ($row['Type']==0)
						{
							echo "#710909";
						}
						else 
						{
							echo "#FFFFFF";	
						}
						echo "\"><input id=\"chck".$row['Varietal_ID']."\" style=\"display: none;\" type=\"checkbox\" name=\"varietals_list[]\" value=\"".$row['Varietal_ID']."\">".$row['Varietal']."</div></td>";
						if ($blend_counter==6)
						{
							echo "</tr>";
							$blend_counter=0;
						}								
					}
					while ($blend_counter!=0)
					{
						$blend_counter++;
						echo "<td></td>";
						if ($blend_counter==6)
						{
							echo "</tr>";
							$blend_counter=0;
						}
					}
				?>
			</table>
		</td>
	</tr>	
	<tr>
		<td colspan="4" width="100%"><input type="submit" name="SubmitToWineList" value="Submit Wine"/></td>
	</tr>
</table>
</div>
<hr>
<table border=0 width="100%">		
	<?php			
		//Select all from database	
		$current_header_value="";			
		$wine_farm_index=0;
		$old_farm="";
		$old_year="";
		$old_bottles="";
		$old_varietal="";
		$old_comment="";
		$isrepeat=false;			
		$sql="SELECT wine_collection.Record_ID,wine_farms.Farm,wine_collection.Year,wine_varietals.Varietal,wine_collection.IsBlend,wine_collection.NoBottles,wine_collection.Comment 
		FROM wine_collection,wine_farms,wine_varietals,wine_blend 
		WHERE wine_collection.Farm_ID   =wine_farms.Farm_ID 			
		AND   wine_collection.Record_ID =wine_blend.Record_ID 
		AND   wine_blend.Varietal_ID    =wine_varietals.Varietal_ID 
		AND   wine_collection.User_ID   ='".$_SESSION['user_id']."' ";
		
		switch ($sort_flag)
		{
			case 0:
				$sql=$sql."ORDER BY wine_varietals.Varietal,wine_farms.Farm,wine_collection.Year";					
				break;
			case 1:
				$sql=$sql."ORDER BY wine_collection.Year,wine_farms.Farm,wine_varietals.Varietal";
				break;
			case 2:
				$sql=$sql."ORDER BY wine_farms.Farm,wine_collection.Year,wine_varietals.Varietal";
				break;
		}
		$result=run_select_on_db($sql,$con);
		while($row = mysql_fetch_array($result))
		{
			switch ($sort_flag)
			{
			case 0:
				$header_value=$row['Varietal'];						
				break;
			case 1:
				$header_value=$row['Year'];
				break;
			case 2:
				$header_value=$row['Farm'];
				break;
			}					
			#CHECK HEADER ROW
			if ($current_header_value != $header_value)
			{
				$wine_farm_index=$wine_farm_index+1;
				#WE NEED TO CLOSE OFF THE PREVIOUS FARM	
				if ($current_header_value!="")
				{							
					echo "</table>";
					echo "</div>";
					echo "</td></tr>";
				}
				#THE HEADER ROW FOR THIS FARM
				echo "<tr><td colspan=\"4\">";
				echo "<div onclick=\"DoExpand('wine_block_".$wine_farm_index."','wine_pic_$wine_farm_index')\">";
				echo "<img id=\"wine_pic_$wine_farm_index\" src=\"../images/wine_glass_up.png\" height=\"40\">";
				echo $header_value;
				echo "</div>";
				echo "</td>";
				echo "</tr>";
				$current_header_value = $header_value;
				#CREATE THE CONTENT ROW FOR THIS FARM
				echo "<tr><td colspan=\"4\">";
				#CREATE THE TABLE FOR THIS FARM
				echo "<div id=\"wine_block_".$wine_farm_index."\" style=\"display: none\">";
				echo "<table border=0 class=\"wine_list\" width=\"100%\">";
				echo "<tr><td colspan=\"4\">";
			}					
			#FILL THE CONTENT FOR THIS FARM
			#FIRST CHECK FOR THE REPEAT BLEND ROWS AND EXCLUDE
			#HOWEVER WHEN WE ARE SORTED BY BLEND WE WANT THE REPEAT ROWS SO.... ADD THE $sort_flag CHECK
			if ($old_farm==$row['Farm'] && $old_year==$row['Year'] && $old_bottles==$row['NoBottles'] && $old_comment==$row['Comment']
			&& $row['IsBlend']==1 
			&& $sort_flag!=0)
			{}
			else 
			{
				echo "<div id=\"".$row['Record_ID']."\" rel=\"popuprel\" class=\"popup\"><table border=0 width=\"100%\"><tr>";
				echo "<td width=\"35%\">";						
				echo $row['Farm'];
				echo "</td>";
				
				echo "<td width=\"20%\">";
				echo $row['Year'];
				echo "</td>";
				
				echo "<td width=\"35%\">";
				if ($row['IsBlend']==1)
				{
					#echo "<a href=\"#\" id=\"".$row['Record_ID']."\" rel=\"popuprel\" class=\"popup\">";
					echo "Blend";
					#echo "</a>";
				}
				else {
					echo $row['Varietal'];
				}						
				echo "</td>";
				
				echo "<td width=\"10%\">";
				echo $row['NoBottles'];
				echo "</td>";
				echo "</tr>";
				
				echo "<tr>";
				echo "<td colspan=3 class=\"mycomment\">";
				echo $row['Comment'];
				echo "</td>";
				echo "<td>";						
				echo "<input type=\"submit\" name=\"RemoveFromWineList[]\" value=\"Remove\" onclick=\"GetRemove(".$row['Record_ID'].")\"/>";
				echo "</td>";
				echo "</tr>";
				
				echo "<tr>";
				echo "<td colspan=4>";
				echo "<hr>";
				echo "</td>";
				echo "</tr>";
				
				echo "</table></div>";
				$old_farm=$row['Farm'];
				$old_year=$row['Year'];
				$old_bottles=$row['NoBottles'];
				$old_comment=$row['Comment'];
			}
		}
		#CLOSE OFF THE FINAL ROW
		echo "</table>";
		echo "</div>";
		echo "</td></tr>";
	?>
</table>	
</form>

<?php
	include 'wine_detail_popup_inc.php';
	
	//Close the database
	mysql_close($con);
?>
</body>
</html>