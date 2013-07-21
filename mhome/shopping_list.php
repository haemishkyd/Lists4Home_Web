<!DOCTYPE html>
<?php

include 'generic_php.php';
include 'logscript_inc.php';
?>
<html>
	<head>
		<title>Lists4Home::Shopping List</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="../themes/lists4home.min.css"/>
		<link rel="stylesheet" href="../css/ui-lightness/jquery-ui-1.8.23.custom.css" />
		<link rel="stylesheet" href="../css/mjQuery/jquery.mobile.custom.structure.min.css" />
		<link rel="stylesheet" href="../css/mobile_style.css" />

		<script src="../js/libs/jquery-1.8.0.min.js"></script>
		<script src="../js/libs/jquery-ui-1.8.23.custom.min.js"></script>
		<script src="../js/mjquery/jquery.mobile.custom.min.js"></script>
		<script src="../js/lists4home.js"></script>
		<script>
	  	var WhichPage = <?php echo $loginstatus;?>;
	  	var shoprequest3;
	  	var shoprequest4;
	  	var shoprequest5;
	  	var saved_user_name="";
	  	
        $(document).on('pagebeforeshow', function(e, data) {
            if (WhichPage == 200)
            {
                $.mobile.changePage("shopping_list.php#shopping_list",
                {
                    transition : "slide"
                });
                WhichPage = 300;
            }        
        });

        $(document).on('pagechange', function(e, data) {                
            $("#username_id").val('');
            $("#password_id").val('');
            $("#item_comment_id").val('');
            $("#item_list_id").val('');
            hide("cat_choose_id");
            
            //puts the current users username onto the logout button at the bottom
            shoprequest3 = $.ajax(
            {
                url : "process.php",
                type : "POST",
                data :
                {                        
                    getuser : "0"
                }
            });
            shoprequest3.done(function(response, textStatus, jqXHR)
            {
                saved_user_name = response;
                button_text="<a class=\"ui-btn ui-btn-up-c ui-shadow ui-btn-corner-all\" data-ajax=\"false\" data-role=\"button\" data-transition=\"slide\" id=\"logout_id\" data-corners=\"true\" data-shadow=\"true\" data-iconshadow=\"true\" data-wrapperels=\"span\" data-theme=\"c\"><span class=\"ui-btn-inner ui-btn-corner-all\"><span class=\"ui-btn-text\">Logout:"+saved_user_name+"</span></span></a>";
                $("#insert_button").html(button_text);
            });
            
            //Gets the actual shopping list
            if($.mobile.activePage.attr("id") == "shopping_list")
            {
                shoprequest4 = $.ajax(
                {
                    url : "process.php",
                    type : "POST",
                    data :
                    {                        
                        GetList : "0"
                    }
                });
                shoprequest4.done(function(response, textStatus, jqXHR)
                {
                    $("#the_list_data").html(response);
                });
            }
        });
        
		$(document).ready(function()
        {  
            $("#bought_recently").bind({
               popupbeforeposition: function(event, ui) 
               {
                   var h = $( window ).height();
    
                   $( "#bought_recently" ).css( "height", h-5 );
                   
                   shoprequest5 = $.ajax(
                    {
                        url : "formvalidator.php",
                        type : "POST",
                        data :
                        {                        
                            whatbought : "0"
                        }
                    });
                    shoprequest5.done(function(response, textStatus, jqXHR)
                    {
                        $("#bought_recently").html(response);
                        $("#bought_recently").trigger('create');
                    });                       
               }
            });   
                       
            $(function()
            {
                //this gets all the items and adds them to the auto-complete list
                availableTags = [<?php $result = run_select_on_db("SELECT * FROM sl_items", $con);while ($row = mysql_fetch_array($result)){echo "\"" . $row['Item'] . "\",";}?>];
        
        		$("#item_list_id").autocomplete(
        		{
        			source : availableTags,
        			select : function(e, ui)
        			{
        				document.getElementById("item_list_id").value = ui.item.value;
        			}
        		});        		
			});			
		});

		</script>
	</head>
	<body>
	    
        <!-- Login Page -->
		<div data-role="page" id="login_page">
			<div data-role="header">
				<h1>Login</h1>				
			</div>

			<div data-role="content">
				<div class="content-primary">
					<form id="Login_id" name="Login_name" class="ui-body ui-body-b ui-corner-all">
						<table border="0" width="100%">
							<tr>
								<td  colspan="2">
								<div data-role="fieldcontain" class="ui-hide-label">
									<label for="username_name" class="ui-hidden-accessible">Username</label>
									<input id="username_id" name="username_name" placeholder="Username" value="" autocomplete="off"/>
									<div id="username_taken_id" style="display: none;text-align: center;"><img src="../images/wrong.png" class="l4h_icon"/>User Already Exists!
									</div>
								</div></td>
							</tr>
							<tr>
								<td  colspan="2">
								<div data-role="fieldcontain" class="ui-hide-label">
									<label for="password_name" class="ui-hidden-accessible">Password</label>
									<input type="password" id="password_id" name="password_name" placeholder="Password" value=""/>
								</div></td>
							</tr>
							<tr>
								<td colspan="2">
								<div id="register_block" style="display: none;">
									<table border="0" style="width: 100%;">
										<tr>
											<td><label for="confirmpass_name" class="ui-hidden-accessible">Confirm Password</label>
											<input type="password" id="confirmpass_id" name="confirmpass_name" placeholder="Confirm Password" value=""/>
											<div id="password_match_wrong_id" style="display: none;text-align: center;"><img src="../images/wrong.png" class="l4h_icon"/>Passwords Do Not Match!
											</div></td>
										</tr>
										<tr>
											<td><label for="name_name" class="ui-hidden-accessible">Name</label>
											<input id="name_id" name="name_name" placeholder="Name" value=""/>
											</td>
										</tr>
										<tr>
											<td><label for="surname_name" class="ui-hidden-accessible">Surname</label>
											<input id="surname_id" name="surname_name" placeholder="Surname" value=""/>
											</td>
										</tr>
										<tr>
											<td><label for="email_name" class="ui-hidden-accessible">Email</label>
											<input id="email_id" name="email_name" placeholder="Email" value=""/>
											<div id="email_wrong_id" style="display: none;text-align: center;"><img src="../images/wrong.png" class="l4h_icon"/>Invalid Email!
											</div></td>
										</tr>
										<tr>
											<td>
											<!--<input type="button" id="submit_button_id" data-theme="b" value="Submit" />-->
											<a href="#" id="submit_button_id" data-transition="slide" data-position-to="window" data-role="button" data-rel="popup">Submit</a>
											</td>
										</tr>
									</table>
								</div></td>
							</tr>
							<tr>
								<td colspan="2">
								<div id="loginbutton_id" style="display: block;">
									<table border="0" style="width: 100%;">
										<tr>
											<td>
											<!--<input type="button" id="login_button_id" data-theme="b" name="Login" value="Login" />-->
											<a href="#" id="login_button_id" data-transition="slide" data-position-to="window" data-role="button" data-rel="popup">Login</a>
											</td>
										</tr>
									</table>
								</div></td>
							</tr>
							<tr>
								<td colspan="2">
								    <a href="#" id="register_click_id">Click Here to Register</a><br>
								    <a href="#" id="reset_pword_click_id">Click Here to Reset Password</a>
								</td>
							</tr>
						</table>
					</form>
				</div>
			</div>

			<div data-role="footer">
				<h4>Lists4Home</h4>
			</div><!-- /footer -->
		</div>

		<!-- List Page -->
		<div data-role="page" id="shopping_list">			
			<div data-role="header">
				<h1>Shopping List</h1>
				<p>
					<table class="l4h_shopping_list_table">
						<tr>
							<td>
							<a href="#add_item" data-transition="slide" data-role="button">Add Items</a>
							</td>
							<td>
							    <!--<input type="button" id="remove_item_id" data-theme="b" data-role="button" name="Remove Items" value="Remove Items" />-->
							    <a href="#" id="remove_item_id" data-transition="slide" data-position-to="window" data-role="button" data-rel="popup">Remove Items</a>
							</td>
							<td><a href="#bought_recently" data-transition="slide" data-position-to="window" data-role="button" data-rel="popup">Bought Recently</a></td>
						</tr>
					</table>
				</p>
			</div><!-- /header -->

			<div data-role="content">
				<div class="content-primary">
					<ul data-role="listview">
						<div id="the_list_data"></div>
					</ul>                    
				</div><!--/content-primary -->
			</div><!-- /content -->

			<div data-role="footer">
				<table class="l4h_shopping_list_table">
                        <tr>
                            <td>
                            <div id="insert_button"></div>
                            </td>
                            <td>
                                <h4 style="text-align: center">Lists4Home</h4>
                            </td>
                            <td><a href="../index.html" data-transition="slide" data-role="button" data-ajax="false">Back To Launchpad</a></td>
                        </tr>
                    </table>				
			</div><!-- /footer -->			
			
			<!-- Pop up -->
            <div data-role="popup" id="bought_recently" data-corners="false" data-theme="b" data-overlay-theme="b" data-shadow="false" data-tolerance="0,0">                
            </div>
		</div><!-- /page -->

		<!-- Add Item Page Page -->
		<div data-role="page" id="add_item">
			<div data-role="header">
				<h1>Shopping List</h1>
				<table class="l4h_shopping_list_table">
					<tr>
						<td></td>
						<td></td>
						<td><a href="#shopping_list" data-transition="slide" data-role="button">Cancel</a></td>
						<td></td>
						<td></td>
					</tr>
				</table>
			</div><!-- /header -->

			<div data-role="content">
				<div class="content-primary">					
					<table border="0" width="100%">
						<tr>
							<td  colspan="2">
							<div data-role="fieldcontain" class="ui-hide-label">
								<label for="item_list_name" class="ui-hidden-accessible">Item:</label>
								<input id="item_list_id" name="item_list_name" placeholder="Item" value="" autocomplete="off"/>
							</div></td>
							<td  colspan="2">
							<div data-role="fieldcontain" class="ui-hide-label">
								<label for="item_comment_name" class="ui-hidden-accessible">Comment:</label>
								<input id="item_comment_id" name="item_comment_name" placeholder="Measurement,Quantity,etc" value=""/>
							</div></td>
						</tr>
						<tr>
							<td colspan="4">
							<table border=0 id="cat_choose_id" style="display: none;">
								<tr>
									<td style="width: 40;">Select New Item's Category:</td><td>
									<select name="catList_name" id="selectList_id">
										<?php
                                        //Select all from database
                                        $result = run_select_on_db("SELECT sl_categories.Category FROM sl_categories ORDER BY sl_categories.Category", $con);
                                        while ($row = mysql_fetch_array($result))
                                        {
                                            echo "<option>" . $row['Category'] . "</option>\n";
                                        }
										?>
									</select></td>
								</tr>
							</table></td>
						</tr>
						<tr>
							<td colspan="4">
							<table border=0 id="cat_insert_id" style="display: none;">
								<tr>
									<td><label for="category_name" class="ui-hidden-accessible">New Category:</label></td>
									<td>
									<input type="text" name="category_name" size="20" maxlength="50"/>
									</td>
								</tr>
							</table></td>
						</tr>
						<tr>
							<td colspan="2"></td>
							<td colspan="2">
							    <!-- <input type="button" id="add_to_list" data-theme="b" name="Submit Items" value="Submit Items" /> -->
							    <a href="#" id="add_to_list" data-transition="slide" data-position-to="window" data-role="button" data-rel="popup">Submit Items</a>
							</td>
						</tr>
					</table>
				</div>
			</div>

			<div data-role="footer">
				<h4>Lists4Home<div id="insert_button"></div></h4>
			</div><!-- /footer -->		    
		</div>    
    
    <!-- Preload of the tick -->
    <img src="../images/checkbox-checked-md.png" style="display: none"/>
	</body>
</html>