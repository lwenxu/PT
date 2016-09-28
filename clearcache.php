<?php
require "include/bittorrent.php";
dbconn();
loggedinorreturn();
echo "<style>
    td{
        border: solid 2px;
        color: #00a8c6;
        font-family: 'Microsoft Yahei';
        font-size: 17px;
        font-weight: 200;
    }
    td input{
        margin: 4px;
    }
    td select{
        margin: 4px;
    }
    table{
        margin-left: 25%;
        margin-top: 10px;
        width: 50%;
    }
    .selectdate{
    	font-family: 'Microsoft Yahei';
    	color: #00a8c6;
    	font-size: 20px;
    }
    b{
    font-family: 'Microsoft Yahei';
    	color: #00a8c6;
    	font-size: 20px;
    }
    #orderlist{
    font-family: 'Microsoft Yahei';
    	color: #00a8c6;
    	font-size: 16px;
    }
</style>";
if (get_user_class() < UC_MODERATOR)
stderr("Error", "Permission denied.");
$done = false;
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
$cachename = $_POST["cachename"];
if ($cachename == "")
stderr("Error", "You must fill in cache name.");
if ($_POST['multilang'] == 'yes')
$Cache->delete_value($cachename, true);
else 
$Cache->delete_value($cachename);
$done = true;
}
stdhead("Clear cache");
?>
<h1>Clear cache</h1>
<?php
if ($done)
print ("<p align=center><font class=striking>Cache cleared</font></p>");
?>
<form method=post action=clearcache.php>
<table border=1 cellspacing=0 cellpadding=5>
<tr><td class=rowhead>Cache name</td><td><input type=text name=cachename size=40></td></tr>
<tr><td class=rowhead>Multi languages</td><td><input type=checkbox name=multilang>Yes</td></tr>
<tr><td colspan=2 align=center><input type=submit value="Okay" class=btn></td></tr>
</table>
</form>
<?php stdfoot();