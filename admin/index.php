<?php

	include("configure.inc");

	$navMatch = "home";

	$cta = "Welcome to Timberline Lawn & Landscape! <strong>Call Today! $phoneNumber</strong>";

	$meta["title"] = "Timberline Lawn & Landscape Providing Lawn Care, Lawn Mowing & Landscaping in and around Dallas Texas";

	$meta["description"] = "Timberline lawn & Landscape is a Dallas based lawn care services company providing lawn mowing, lawn care and landscape services in Dallas Texas & the surrounding areas.";

	$meta["keywords"] = "dallas, lawn  care, dallas landscaping, dallas lawncare, dallas lawn care company, dallas landscape company, dallas tx lawn care, lawn care in dallas texas, landscaping in dallas, landscaping company in dallas, lawn mowing company dallas, lawn mowing dallas, lawn care dallas, lawn care dallas tx, timberline lawn care, timberline lawn care company, timberline lawn care & landscape";

	include("header.php");
	
	$dbconn=mysql_connect ("localhost", "timberli_timberl","Y@V84*6va6w") or die('Cannot connect to the database because: ' . mysql_error());
$db_selected = mysql_select_db("timberli_timberline", $dbconn);
	
	if (isset($_GET['del'])){
	
	$delq="DELETE FROM clients WHERE clientid='".$_GET['del']."'";
$del=mysql_query($delq,$dbconn);	
	
	echo ("<script type=\"text/javascript\">alert ('Client Deleted'); document.location.href=\"index.php\";</script>");
	
	}

?>

	

	<div id="main" style="padding-top:50px; padding-bottom:50px;">

		<?PHP
if(isset($_POST['submit'])){
include('process.php');

}

if($_GET['response']=='approved'){
echo('Your transaction has been processed<br />'.$_GET['result'].'');
}

if($_GET['response']=='declined'){
echo('<p>Your transaction has been declined. Please check the client\'s CIM Profiles.</p>'.$_GET['result'].'');
}

if($_GET['response']=='error'||$_GET['response']=='unknown'){
echo('<p>There was an error processing this transaction.</p>'.$_GET['result'].'');
}

if($_GET['response']=='review'){
echo('<p>This transactiion has been held for review..</p>'.$_GET['result'].'');
}


if(isset($_GET['name'])){
$clientq="SELECT * FROM clients WHERE clientid='".$_GET['name']."'";
$client=mysql_query($clientq,$dbconn);
$totalRows_client=mysql_num_rows($client);
} else {
$clientq="SELECT * FROM clients";
$client=mysql_query($clientq,$dbconn);
$totalRows_client=mysql_num_rows($client);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Timberline Payment Page</title>
<style>
.field{
float:left;
width:250px;
}
.field2{
float:left;
width:125px;
}

.field3{
float:left;
width:25px;
}
.clearall{
clear:both;
}

.spacer{
padding-bottom:15px;
}

a {
color:white;
}
</style>

<style type="text/css">
#livesearch
  {
 position:absolute;
 z-index:1000;
  margin-left:85px;
  width:158px;
  padding-bottom:15px;
  }
#txt1
  {
  margin:0px;
  }
</style>
<script type="text/javascript">
var state='none';

function makepayment(id){

if(state=='block'){
state='none';
} else {
state='block';
}

document.getElementById(id).style.display=""+state+"";
}

function subpay(payform,pid,ppid){
var amount=document.forms[payform].amount.value;
var notes=document.forms[payform].notes.value;
location.href="process.php?pid="+pid+"&ppid="+ppid+"&amount="+amount+"&notes="+notes+"";
}

// Client search script 

var xmlhttp;

function showHint(str)
{
if (str.length==0)
  {
  document.getElementById("livesearch").innerHTML="";
  document.getElementById("livesearch").style.border="0px";
  return;
  }
xmlhttp=GetXmlHttpObject()
if (xmlhttp==null)
  {
  alert ("Your browser does not support XML HTTP Request");
  return;
  }
var url="gethint.php";
url=url+"?q="+str;
url=url+"&sid="+Math.random();
xmlhttp.onreadystatechange=stateChanged ;
xmlhttp.open("GET",url,true);
xmlhttp.send(null);
}

function stateChanged()
{
if (xmlhttp.readyState==4)
  {
  document.getElementById("livesearch").innerHTML=xmlhttp.responseText;
  document.getElementById("livesearch").style.border="1px solid #A5ACB2";
  }
}

function GetXmlHttpObject()
{
if (window.XMLHttpRequest)
  {
  // code for IE7+, Firefox, Chrome, Opera, Safari
  return new XMLHttpRequest();
  }
if (window.ActiveXObject)
  {
  // code for IE6, IE5
  return new ActiveXObject("Microsoft.XMLHTTP");
  }
return null;
}

function resetpage(){
document.location.href="index.php";
}


function delrec(delid){

$conf=confirm('Are you sure you wish to delete this record?  This deletion will be permanent.');

if($conf){
document.location.href="index.php?del="+delid+"";
}

}
</script>
</head>

<body style="font-family:arial;">

<h2>Timberline Payment Page</h2>
<div style="padding-bottom:30px;">
<form>
Search Client: <input type="text" id="txt1" size="30" onkeyup="showHint(this.value)" /> <a href="index.php"><input type="button" value="Reset" onClick="resetpage()"></a>
<div id="livesearch"></div>
</form>
</div>
<div class="field" style="padding-bottom:15px;"><strong>Name</strong></div><div class="field2" style="padding-bottom:15px;"><strong>Phone</strong></div>
<div class="clearall"></div>
<?PHP 
for ($i=-0; $i<$totalRows_client; $i++){

if(mysql_result($client,$i,"clientphone")!=""){
$phone=mysql_result($client,$i,"clientphone");
} else {
$phone="No number";
}

?>
<div class="field"><?PHP echo mysql_result($client,$i,"clientfirst")." ".mysql_result($client,$i,"clientlast") ?></div><div class="field2"><?PHP echo $phone ?></div><div class="field2"><a href="javascript:makepayment('paybox<?PHP echo $i ?>');">Make Payment</a></div><div class="field3"><a href="javascript:delrec('<?PHP echo mysql_result($client,$i,"clientid") ?>')" border="0"><img src="images/deleteButton.png" alt="Delete" title="Delete"></a></div>
<div class="clearall"></div>
<div id="paybox<?PHP echo $i ?>" style="display:none; width:500px;"><form name="paybox<?PHP echo $i ?>"><div style="float:left; width:75px; text-align:right; padding-top:5px;">Amount: </div><div style="float:left; width:75px; text-align:left; padding-top:5px;"><input type="text" name="amount" size="5"></div>
<div class="clearall"></div>
<div style="float:left; width:75px; text-align:right; padding-top:5px;">Notes: </div><div style="float:left; width:75px; text-align:left; padding-top:5px;"><textarea name="notes" cols="50" rows="5" maxlength="250"></textarea></div>
<div class="clearall"></div>
<div style="width:230px; text-align:right; padding-top:5px;"><input type="button" name="sub" value="submit" onClick="javascript:subpay('paybox<?PHP echo $i ?>','<?PHP echo mysql_result($client,$i,"pid") ?>','<?PHP echo mysql_result($client,$i,"ppid") ?>')"></form></div>
<div class="clearall"></div>
</div>
<div class="spacer"></div>
<?PHP } ?>

	</div>

	

<?php

	include("../footer.php");

?>