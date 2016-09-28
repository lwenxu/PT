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
        margin-left: 35%;
        margin-top: 10px;
        width: 30%;
    }
</style>";
if (get_user_class() < UC_MODERATOR)
	stderr("Error", "Permission denied.");
$res2 = sql_query("SELECT agent,peer_id FROM peers  GROUP BY agent ") or sqlerr();
stdhead("All Clients");
print("<table align=center border=3 cellspacing=0 cellpadding=5>\n");
print("<tr><td class=colhead>Client</td><td class=colhead>Peer ID</td></tr>\n");
while($arr2 = mysql_fetch_assoc($res2))
{
	print("</a></td><td align=left>$arr2[agent]</td><td align=left>$arr2[peer_id]</td></tr>\n");
}
print("</table>\n");
stdfoot();
