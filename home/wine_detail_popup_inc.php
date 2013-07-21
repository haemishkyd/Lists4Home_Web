<div class="popupbox" id="popuprel">
	<div id="intabdiv">
      	<a href="#" id="popupclose"><img src="../images/close_pop.png"/></a>
      		<?php
      		$record_id=0;
      		$sql="SELECT wine_collection.NoBottles,wine_collection.Record_ID,wine_farms.Farm,wine_collection.Farm_ID,wine_collection.Year,wine_varietals.Varietal
			FROM wine_collection,wine_blend,wine_varietals,wine_farms 
			WHERE 	wine_collection.Record_ID	=wine_blend.Record_ID
			AND 	wine_collection.Farm_ID		=wine_farms.Farm_ID 
			AND 	wine_blend.Varietal_ID		=wine_varietals.Varietal_ID
			ORDER BY wine_collection.Farm_ID"; 
			
			$result=mysql_query($sql,$con);
			if (!$result)
			{
				die('Query Error: ' . mysql_error());
			}
			while($row = mysql_fetch_array($result))
			{
				if ($record_id != $row['Record_ID'])
				{
					if ($record_id != 0)
					{
						echo "</table></div>";
					}
					echo "<div id=\"popup".$row['Record_ID']."\" style=\"display: none\">";
					echo "<table width=\"100%\" height=\"100%\" class=\"popup_table_style\">";
					echo "<tr><td colspan=4><h1>Details</h1></td></tr>";
					echo "<tr><td colspan=2>Farm</td><td colspan=2 class=\"mycomment\">".$row['Farm']."</td></tr>";
					echo "<tr><td>Year</td><td class=\"mycomment\">".$row['Year']."</td><td>No. Bottles</td><td class=\"mycomment\">".$row['NoBottles']."</td></tr>";
					$record_id=$row['Record_ID'];
				}				
				echo "<tr><td colspan=4 class=\"mycomment\">".$row['Varietal']."</td></tr>";	
			}
      		?>
  	</div>
</div>