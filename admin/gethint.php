<?php

$dbconn=mysql_connect ("localhost", "timberli_timberl","Y@V84*6va6w") or die('Cannot connect to the database because: ' . mysql_error());
$db_selected = mysql_select_db("timberli_timberline", $dbconn);

//get the q parameter from URL
$q=$_GET["q"];


$searchq="SELECT * FROM clients WHERE concat(clientfirst,\" \",clientlast) LIKE '%$q%'";
$search=mysql_query($searchq,$dbconn);
$totalRows_search=mysql_num_rows($search);

for ($i=0;$i<$totalRows_search;$i++){
$a[]=mysql_result($search,$i,"clientfirst")." ".mysql_result($search,$i,"clientlast");
$b[]=mysql_result($search,$i,"clientid");
}

//lookup all hints from array if length of q>0
if (strlen($q) > 0)
  {
  $hint="";
  for($i=0; $i<count($a); $i++)
    {
   
      if ($hint=="")
        {
        $hint="<a href=\"index.php?name=".$b[$i]."\">".$a[$i]."</a>";
        }
      else
        {
        $hint="<a href=\"index.php?name=".$b[$i]."\">".$a[$i]."</a>"."<br>".$hint;
        }
      
    }
  }

// Set output to "no suggestion" if no hint were found
// or to the correct values
if ($hint == "")
  {
  $response="no suggestion";
  }
else
  {
  $response=$hint;
  }

//output the response
echo $response;

?>