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


function checkUsername() 
{
    var userValue = document.getElementById("username").value;
    
    if (userValue == "") 
    {
        alert("Please enter a user name to check!");
        return;
    }
    
    var url = "formvalidator.php?barcode=" + userValue;
    
    var request = new HttpRequest(url, checkUsername_callBack);
    request.send();
}

function sendSMS() 
{
    var url = "http://api.clickatell.com/http/sendmsg?user=h5banking&password=empyrean69&api_id=3359473&to=27823772620&text=Hello there!";
    
    var request = new HttpRequest(url, sendSMS_callBack);
    request.send();
}

function sendSMS_callBack(sResponseText) 
{
    alert("SMS Sent: "+sResponseText);
}

function checkUsername_callBack(sResponseText) 
{    
    alert("Item is " + sResponseText);    
}
</script>
<title>Form Field Validation</title>
    <style type="text/css">
        .fieldname
        {
            text-align: right;
        }
        
        .submit
        {
            text-align: right;
        }
    </style>
</head>

<body>
	<form>
		<a href="javascript: sendSMS()">Send SMS</a>		
        <table>
            <tr>            	
                <td class="fieldname">
                    Barcode:
                </td>
                <td>
                    <input type="text" id="username" />
                </td>
                <td>
                    <a href="javascript: checkUsername()">Check Barcode</a>
                </td>
            </tr>            
        </table>
    </form>
</body>