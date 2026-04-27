<?php

	include("configure.inc");

	$navMatch = "home";

	$cta = "Welcome to Timberline Lawn & Landscape! <strong>Call Today! $phoneNumber</strong>";

	$meta["title"] = "Timberline Lawn & Landscape Providing Lawn Care, Lawn Mowing & Landscaping in and around Dallas Texas";

	$meta["description"] = "Timberline lawn & Landscape is a Dallas based lawn care services company providing lawn mowing, lawn care and landscape services in Dallas Texas & the surrounding areas.";

	$meta["keywords"] = "dallas, lawn  care, dallas landscaping, dallas lawncare, dallas lawn care company, dallas landscape company, dallas tx lawn care, lawn care in dallas texas, landscaping in dallas, landscaping company in dallas, lawn mowing company dallas, lawn mowing dallas, lawn care dallas, lawn care dallas tx, timberline lawn care, timberline lawn care company, timberline lawn care & landscape";

	include("header.php");

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

$dbconn=mysql_connect ("localhost", "timberli_timberl","Y@V84*6va6w") or die('Cannot connect to the database because: ' . mysql_error());
$db_selected = mysql_select_db("timberli_timberline", $dbconn);

$clientq="SELECT * FROM clients";
$client=mysql_query($clientq,$dbconn);
$totalRows_client=mysql_num_rows($client);

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
.clearall{
clear:both;
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
alert(state);
document.getElementById(id).style.display=""+state+"";
}

function subpay(payform,pid,ppid){
var amount=document.forms[payform].amount.value;
var notes=document.forms[payform].notes.value;
location.href="process.php?pid="+pid+"&ppid="+ppid+"&amount="+amount+"&notes="+notes+"";
}
</script>
</head>

<body style="font-family:arial;">

<h2>Timberline Payment Page</h2>

<div class="field" style="padding-bottom:15px;"><strong>Name</strong></div><div class="field2" style="padding-bottom:15px;"><strong>Phone</strong></div>
<div class="clearall"></div>
<?PHP 
for ($i=-0; $i<$totalRows_client; $i++){
?>
<div class="field"><?PHP echo mysql_result($client,$i,"clientfirst")." ".mysql_result($client,$i,"clientlast") ?></div><div class="field2"><?PHP echo mysql_result($client,$i,"clientphone") ?></div><div class="field"><a href="javascript:makepayment('paybox<?PHP echo $i ?>');">Make Payment</a></div>
<div class="clearall"></div>
<div id="paybox<?PHP echo $i ?>" style="display:none; width:500px;"><form name="paybox<?PHP echo $i ?>"><div style="float:left; width:75px; text-align:right; padding-top:5px;">Amount: </div><div style="float:left; width:75px; text-align:left; padding-top:5px;"><input type="text" name="amount" size="5"></div>
<div class="clearall"></div>
<div style="float:left; width:75px; text-align:right; padding-top:5px;">Notes: </div><div style="float:left; width:75px; text-align:left; padding-top:5px;"><textarea name="notes" cols="50" rows="5" maxlength="250"></textarea></div>
<div class="clearall"></div>
<div style="width:230px; text-align:right; padding-top:5px;"><input type="button" name="sub" value="submit" onClick="javascript:subpay('paybox<?PHP echo $i ?>','<?PHP echo mysql_result($client,$i,"pid") ?>','<?PHP echo mysql_result($client,$i,"ppid") ?>')"></form></div>
<div class="clearall"></div>
</div>
<?PHP } ?>

	</div>

	

<?php

	include("../footer.php");

?>