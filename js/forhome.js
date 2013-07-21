/* Author: Haemish Kyd
 Useful, generic javascript functions for use on a website.
 */

/* Shoppling List JS ******************************************************/
var shoprequest1;
var shoprequest2;
var availableTags;
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
        document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
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
			}
			else
			{
				hide("password_match_wrong_id");
				passok = 1;
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
			}
			else
			{
				hide("email_wrong_id");
				emailok = 1;
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
				}
				else
				{
					show("username_taken_id");
					usernameok = 0;
				}
			});
		}
	});

	$("#register_click_id").click(function()
	{
		ToggleElementState('register_block');
		ToggleElementState('loginbutton_id');
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
			alert("There are required fields missing.")
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

function checkOffline()
{
	document.getElementById("username").value = "Hello World";
}

/* The following are Ajax functions to do calls for data to the server */
function HttpRequest(sUrl, fpCallback)
{
	this.request = this.createXmlHttpRequest();
	this.request.open("GET", sUrl, true);

	var tempRequest = this.request;
	function request_readystatechange()
	{
		if (tempRequest.readyState == 4)
		{
			if (tempRequest.status == 200)
			{
				fpCallback(tempRequest.responseText);
			}
			else
			{
				alert("An error occurred while attempting to contact the server.");
			}
		}
	}


	this.request.onreadystatechange = request_readystatechange;
}

HttpRequest.prototype.createXmlHttpRequest = function()
{
	if (window.XMLHttpRequest)
	{

		var oHttp = new XMLHttpRequest();
		return oHttp;

	}
	else if (window.ActiveXObject)
	{

		var versions = ["MSXML2.XmlHttp.6.0", "MSXML2.XmlHttp.3.0"];

		for (var i = 0; i < versions.length; i++)
		{
			try
			{
				oHttp = new ActiveXObject(versions[i]);
				return oHttp;
			}
			catch (error)
			{
				//do nothing here
			}
		}

	}

	return null;
}

HttpRequest.prototype.send = function()
{
	this.request.send(null);
}
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

/*
 * Generic function to create the hint in the text fields.
 */
function inputFocus(i)
{
	if (i.value == i.defaultValue)
	{
		i.value = "";
		i.style.color = "#000";
	}
}

function inputBlur(i)
{
	if (i.value == "")
	{
		i.value = i.defaultValue;
		i.style.color = "#888";
	}
}

/*
 * Function to handle the global menu functions
 */
function highlightMenu(whichOne)
{
	window.parent.document.getElementById('link1').style.backgroundColor = "#151515";
	window.parent.document.getElementById('link2').style.backgroundColor = "#151515";
	window.parent.document.getElementById('link3').style.backgroundColor = "#151515";
	window.parent.document.getElementById('link4').style.backgroundColor = "#151515";
	window.parent.document.getElementById('linkend').style.backgroundColor = "#151515";

	window.parent.document.getElementById(whichOne).style.backgroundColor = "#333333";
}

/*
 * Function to create a generic popupbox
 */
function genericPopupBox(item)
{
	// Here we will describe a variable popupid which gets the
	// rel attribute from the clicked link
	var popupid = $(item).attr('rel');

	// Now we need to popup the marked which belongs to the rel attribute
	// Suppose the rel attribute of click link is popuprel then here in below code
	// #popuprel will fadein
	$('#' + popupid).fadeIn();

	// append div with id fade into the bottom of body tag
	// and we allready styled it in our step 2 : CSS
	$('body').append('<div id="fade"></div>');
	$('#fade').css(
	{
		'filter' : 'alpha(opacity=80)'
	}).fadeIn();

	// Now here we need to have our popup box in center of
	// webpage when its fadein. so we add 10px to height and width
	var popuptopmargin = ($('#' + popupid).height() + 10) / 2;
	var popupleftmargin = ($('#' + popupid).width() + 10) / 2;

	// Then using .css function style our popup box for center allignment
	$('#' + popupid).css(
	{
		'margin-top' : -0,
		'margin-left' : -popupleftmargin
	});
}

