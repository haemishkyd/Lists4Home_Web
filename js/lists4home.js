/* Author: Haemish Kyd
 Useful, generic javascript functions for use on a website.
 */

/* Shoppling List JS ******************************************************/
var shoprequest1;
var shoprequest2;
var availableTags;
function closeBought()
{
	$("#bought_recently").popup("close");
}

$(document).ready(function()
{
	initBinding_Shop_List();
	initBinding_Login();
});

function initBinding_Shop_List()
{
	$("#remove_item_id").click(function()
	{
		var my_done_shopping_list = new Array();
		
		$("input[name='shopping_list[]']:checked").each(function()
		{
		    my_done_shopping_list.push($(this).val());
		});		
		
		shoprequest2 = $.ajax(
		{
			url : "process.php",
			type : "POST",
			data :
			{
				shopping_list : my_done_shopping_list,
				RemoveItems : "0"
			}
		});
		shoprequest2.done(function(response, textStatus, jqXHR)
		{
			// log a message to the console
			$.mobile.changePage("shopping_list.php#shopping_list",
			{
				transition : "slide"
			});
		});
	});

	$("#logout_id").live("click",function()
	{
		deleteSessionCookies();
		logout_session=$.ajax(
		{
			url : "process.php",
			type : "POST",
			data :
			{				
				logout : "0"
			}
		});
		
		$.mobile.changePage("shopping_list.php",
		{
			transition : "slide"
		});
	});
	
	$("#add_to_list").click(function()
	{
		if (($.inArray($("#item_list_id").val(), availableTags) != -1) || (document.getElementById("cat_choose_id").style.display == "block"))
		{
			hide("cat_choose_id");
			shoprequest1 = $.ajax(
			{
				url : "process.php",
				type : "POST",
				data :
				{
					item_comment_name : $("#item_comment_id").val(),
					item_list_name : $("#item_list_id").val(),
					category_name : $("#selectList_id").val(),
					SubmitToList : "0"
				}
			});

			shoprequest1.done(function(response, textStatus, jqXHR)
			{
				// log a message to the console
				$.mobile.changePage("shopping_list.php#shopping_list",
				{
					transition : "slide"
				});
			});
		}
		else
		{
			show("cat_choose_id");
			SetCookie("UnknownItem", 1, 1);			
		}
	});
}

/*
 * This function checks the state of the check box and toggles it. It also changes
 * the picture based on that toggled state.
 * Called: From the table elements that make up the list (checkbox and the text itself)
 */
function ChangeCheckBox(cbid, pic, id)
{
	if (document.getElementById(cbid).checked == false)
	{
		document.getElementById(pic).src = "../images/checkbox-checked-md.png";
		document.getElementById(cbid).checked = true;
		$("#"+id).animate({ opacity: 0.2 }, 100);
	}
	else
	{
		document.getElementById(pic).src = "../images/checkbox-unchecked-md.png";
		document.getElementById(cbid).checked = false;
		$("#"+id).animate({ opacity: 1.0 }, 100);
	}		
	//ToggleElementState(id);
}

function deleteSessionCookies() {
   var allcookies = document.cookie.split(";");

   for (var i = 0; i < allcookies.length; i++) {
        var cookie = allcookies[i];
        var eqPos = cookie.indexOf("=");
        var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
        //do not delete the device id
        if (name.indexOf("device") == -1)
        {
        	document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
        }
    }
}
/* End of Shopping List *****************************************************************/

/* Login JS ******************************************************/
var request1;
var request2;
var request3;
var passok = 0;
var emailok = 0;
var usernameok = 0;

function initBinding_Login()
{
	$("#confirmpass_id").blur(function()
	{
		if ($("#register_block").css('display') == 'block')
		{
			if (($("#password_id").val().search($("#confirmpass_id").val())) == -1)
			{
				show("password_match_wrong_id");
				passok = 0;
				$("#submit_button_id").addClass('ui-disabled');
			}
			else
			{
				hide("password_match_wrong_id");
				passok = 1;
				$("#submit_button_id").removeClass('ui-disabled');
			}
		}
	});

	$("#email_id").blur(function()
	{
		if ($("#register_block").css('display') == 'block')
		{
			//Check email address - just a length check
			if ($("#email_id").val().length < 5)
			{
				show("email_wrong_id");
				emailok = 0;
				$("#submit_button_id").addClass('ui-disabled');
			}
			else
			{
				hide("email_wrong_id");
				emailok = 1;
				$("#submit_button_id").removeClass('ui-disabled');
			}
		}
	});

	$('#username_id').blur(function()
	{
		if ($("#register_block").css('display') == 'block')
		{
			request2 = $.ajax(
			{
				url : "validator.php",
				type : "POST",
				data :
				{
					handle : $("#username_id").val()
				}
			});

			request2.done(function(response, textStatus, jqXHR)
			{
				if (response.search("Available!") != -1)
				{
					hide("username_taken_id");
					usernameok = 1;
					$("#submit_button_id").removeClass('ui-disabled');
				}
				else
				{
					show("username_taken_id");
					usernameok = 0;
					$("#submit_button_id").addClass('ui-disabled');
				}
			});
		}
	});

	$("#register_click_id").click(function()
	{
		ToggleElementState('register_block');
		ToggleElementState('loginbutton_id');
	});
	
	$("#reset_pword_click_id").click(function()
	{
		if ($("#username_id").val().length == 0)
		{
			alert("Please enter your username.");
		}
		else{
			request20 = $.ajax(
			{
				url : "process.php",
				type : "POST",
				data :
				{
					homehandlename : $("#username_id").val(),
					resetpass : "0"
				}
			});

			request20.done(function(response, textStatus, jqXHR)
			{
				if (response.search("Problem!") == -1)
				{
					alert("A new password has been sent to "+response);
				}
				else
				{
					alert("There was a problem.");
				}
			});
		}
	});

	$("#submit_button_id").click(function()
	{
		if (usernameok && emailok && passok)
		{
			request1 = $.ajax(
			{
				url : "process.php",
				type : "POST",
				data :
				{
					homehandlename : $("#username_id").val(),
					homepasswordname : $("#password_id").val(),
					confirmhomepasswordname : $("#confirmpass_id").val(),
					reg_username : $("#name_id").val(),
					reg_surname : $("#surname_id").val(),
					emailname : $("#email_id").val(),
					register : "0"
				}
			});
			request1.done(function(response, textStatus, jqXHR)
			{
				if (response.search("Completed!") != -1)
				{
					alert("User Successfully Created. Please log in.");
					$("#username_id").val("");
					$("#password_id").val("");
					$("#confirmpass_id").val("");
					$("#name_id").val("");
					$("#surname_id").val("");
					$("#email_id").val("");
					ToggleElementState('register_block');
					ToggleElementState('loginbutton_id');
				}
				else
				{
					alert("There was a problem creating the user.\n Please try again.\n Response:" + response);
				}
			});
		}
		else
		{
			alert("There are required fields missing.");
		}
	});

	$("#login_button_id").click(function()
	{
		if ($("#register_block").css('display') == 'block')
		{
			alert("There is a problem.");
		}
		else
		{
			request3 = $.ajax(
			{
				url : "process.php",
				type : "POST",
				data :
				{
					homehandlename : $("#username_id").val(),
					homepasswordname : $("#password_id").val(),
					login : "0"
				}
			});

			request3.done(function(response, textStatus, jqXHR)
			{
				if (response.search("Correct!") != -1)
				{
					$.mobile.changePage("shopping_list.php#shopping_list",
					{
						transition : "slide"
					});
				}
				else
				{
					alert("Password/Username Incorrect.");
				}
			});
		}
	});
}

/* End of Login List *****************************************************************/

/*
 * Generic function show show an element
 */
function show(id)
{
	document.getElementById(id).style.display = 'block';
}

/*
 * Generic function to hide an element
 */
function hide(id)
{
	document.getElementById(id).style.display = 'none';
}

/*
 *Generic function to set cookies in Java Script
 */
function SetCookie(cookieName, cookieValue, nDays)
{
	var today = new Date();
	var expire = new Date();
	if (nDays == null || nDays == 0)
		nDays = 1;
	expire.setTime(today.getTime() + 3600000 * 24 * nDays);
	document.cookie = cookieName + "=" + escape(cookieValue) + ";expires=" + expire.toGMTString();
}

/*
 *Generic function to get cookies in Java Script
 */
function GetCookie(cookieName)
{
	var i, x, y, ARRcookies = document.cookie.split(";");
	for ( i = 0; i < ARRcookies.length; i++)
	{
		x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
		y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
		x = x.replace(/^\s+|\s+$/g, "");
		if (x == cookieName)
		{
			return unescape(y);
		}
	}
}

/*
 * Generic function to toggle the state of an element.
 */
function ToggleElementState(id)
{
	if (document.getElementById(id).style.display == 'block')
	{
		hide(id);
	}
	else
	{
		show(id);
	}
}

