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
function makepayment(id){
var set;
if(set='0'){
document.getElementById(id).style.display="block";
set='1';
}else{
document.getElementById(id).style.display="none";
set='0';
}
}

function subpay(pid,ppid){
var amount=document.paybox.amount.value;
var notes=document.paybox.notes.value;
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
<div id="paybox<?PHP echo $i ?>" style="display:none; width:500px;"><form name="paybox">Amount: <input type="text" name="amount" size="5"><br />Notes: <input type="text" name="notes" size="50" maxlength="250"> <input type="button" name="sub" value="submit" onClick="javascript:subpay('<?PHP echo mysql_result($client,$i,"pid") ?>','<?PHP echo mysql_result($client,$i,"ppid") ?>')"></form></div>
<?PHP } ?>

</body>
</html>
