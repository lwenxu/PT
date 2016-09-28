<?php
require_once("include/bittorrent.php");
dbconn();
echo "
	<style>
		input[type=text]{
		display: block;
//		width: 100%;
//		height: 34px;
		padding: 6px 12px;
		font-size: 14px;
		line-height: 1.42857143;
		color: #555;
		background-color: #fff;
		background-image: none;
		border: 1px solid #ccc;
		border-radius: 4px;
		-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
		box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
		-webkit-transition: border-color ease-in-out .15s, -webkit-box-shadow ease-in-out .15s;
		-o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
		transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
		}
		input[type=password]{
		display: block;
//		width: 100%;
//		height: 34px;
		padding: 6px 12px;
		font-size: 14px;
		line-height: 1.42857143;
		color: #555;
		background-color: #fff;
		background-image: none;
		border: 1px solid #ccc;
		border-radius: 4px;
		-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
		box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
		-webkit-transition: border-color ease-in-out .15s, -webkit-box-shadow ease-in-out .15s;
		-o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
		transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
		}
		input[type=submit]{
		display: inline-block;
		margin-bottom: 0;
		font-weight: 400;
		text-align: center;
		vertical-align: middle;
		touch-action: manipulation;
		cursor: pointer;
		border: 1px solid transparent;
		white-space: nowrap;
		padding: 6px 12px;
		font-size: 14px;
		line-height: 1.42857;
		border-radius: 4px;
		-webkit-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		user-select: none;
		color: #FFF;
		background-color: #32c5d2;
		border-color: #32c5d2;
		}
		form p{
		font-family: 'Microsoft Yahei';
		font-size: 17px;
		color: #E83737;
		}
		tr{
		font-family: 'Microsoft JhengHei UI';
		font-size: 18px;
		color: #00a8c6;
		font-weight: 400;
		}
		td input{
		margin-top: 10px;
		margin-bottom: 10px;
		}
		p{
		font-family: 'Microsoft Yahei';
		font-size: 15px;
//		color: #00a8c6;
		font-weight: 300;
		text-align: center;
		}
		table{
		margin-left: 9%;
		}
		.langselect{
		font-family: 'Microsoft Yahei';
		font-size: 17px;
		color: #00a8c6;
		font-weight: 300;
		}
		body{
//		background-image: url('./bg.jpg');
		    background-color: #364150!important;
		}
		.loginbox{
			height: 101%;
			width: 45%;
			background-color: #fff;
			margin-left: 28%;
			border-radius: 9px;
		}
		.footer{
			color: white;
		}
	</style>
";

$langid = 0 + $_GET['sitelanguage'];
if ($langid)
{
	$lang_folder = validlang($langid);
	if(get_langfolder_cookie() != $lang_folder)
	{
		set_langfolder_cookie($lang_folder);
		header("Location: " . $_SERVER['PHP_SELF']);
	}
}
require_once(get_langfile_path("", false, $CURLANGDIR));

failedloginscheck ();
cur_user_check () ;
stdhead($lang_login['head_login']);

$s = "<select name=\"sitelanguage\" onchange='submit()'>\n";

$langs = langlist("site_lang");

foreach ($langs as $row)
{
	if ($row["site_lang_folder"] == get_langfolder_cookie()) $se = "selected=\"selected\""; else $se = "";
	$s .= "<option value=\"". $row["id"] ."\" ". $se. ">" . htmlspecialchars($row["lang_name"]) . "</option>\n";
}
$s .= "\n</select>";
?>

<form method="get" action="<?php echo $_SERVER['PHP_SELF'] ?>">
<?php
print("<div align=\"right\" class='langselect'>".$lang_login['text_select_lang']. $s . "</div>");
?>
</form>
<?php
echo "<div class='loginbox'>";
unset($returnto);
if (!empty($_GET["returnto"])) {
	$returnto = $_GET["returnto"];
	if (!$_GET["nowarn"]) {
		print("<h1>" . $lang_login['h1_not_logged_in']. "</h1>\n");
		print("<p><b>" . $lang_login['p_error']. "</b> " . $lang_login['p_after_logged_in']. "</p>\n");
	}
}
?>
<form method="post" action="takelogin.php">
<p><?php echo $lang_login['p_need_cookies_enables']?><br /> [<b><?php echo $maxloginattempts;?></b>] <?php echo $lang_login['p_fail_ban']?></p>
<p><?php echo $lang_login['p_you_have']?> <b><?php echo remaining ();?></b> <?php echo $lang_login['p_remaining_tries']?></p>
<table border="0" cellpadding="5">
<tr><td class="rowhead"><?php echo $lang_login['rowhead_username']?></td><td class="rowfollow" align="left"><input type="text" name="username" style="width: 180px; border: 1px solid gray" /></td></tr>
<tr><td class="rowhead"><?php echo $lang_login['rowhead_password']?></td><td class="rowfollow" align="left"><input type="password" name="password" style="width: 180px; border: 1px solid gray"/></td></tr>
<?php
show_image_code ();
if ($securelogin == "yes") 
	$sec = "checked=\"checked\" disabled=\"disabled\"";
elseif ($securelogin == "no")
	$sec = "disabled=\"disabled\"";
elseif ($securelogin == "op")
	$sec = "";

if ($securetracker == "yes") 
	$sectra = "checked=\"checked\" disabled=\"disabled\"";
elseif ($securetracker == "no")
	$sectra = "disabled=\"disabled\"";
elseif ($securetracker == "op")
	$sectra = "";
?>
<tr><td class="toolbox" colspan="2" align="left"><?php echo $lang_login['text_advanced_options']?></td></tr>
<tr><td class="rowhead"><?php echo $lang_login['text_auto_logout']?></td><td class="rowfollow" align="left"><input class="checkbox" type="checkbox" name="logout" value="yes" /><?php echo $lang_login['checkbox_auto_logout']?></td></tr>
<tr><td class="rowhead"><?php echo $lang_login['text_restrict_ip']?></td><td class="rowfollow" align="left"><input class="checkbox" type="checkbox" name="securelogin" value="yes" /><?php echo $lang_login['checkbox_restrict_ip']?></td></tr>
<tr><td class="rowhead"><?php echo $lang_login['text_ssl']?></td><td class="rowfollow" align="left"><input class="checkbox" type="checkbox" name="ssl" value="yes" <?php echo $sec?> /><?php echo $lang_login['checkbox_ssl']?><br /><input class="checkbox" type="checkbox" name="trackerssl" value="yes" <?php echo $sectra?> /><?php echo $lang_login['checkbox_ssl_tracker']?></td></tr>
<tr><td class="toolbox" colspan="2" align="right"><input type="submit" value="<?php echo $lang_login['button_login']?>" class="btn" /> <input style="background-color: #da3f3f;color: white" type="reset" value="<?php echo $lang_login['button_reset']?>" class="btn" /></td></tr>
</table>
<?php

if (isset($returnto))
	print("<input type=\"hidden\" name=\"returnto\" value=\"" . htmlspecialchars($returnto) . "\" />\n");

?>
</form>
<p><?php echo $lang_login['p_no_account_signup']?></p>
<?php
if ($smtptype != 'none'){
?>
<p><?php echo $lang_login['p_forget_pass_recover']?></p>
<p><?php echo $lang_login['p_resend_confirm']?></p>
<?php
}
if ($showhelpbox_main != 'no'){?>
<table width="700" class="main" border="0" cellspacing="0" cellpadding="0"><tr><td class="embedded">
<h2><?php echo $lang_login['text_helpbox'] ?><font class="small"> - <?php echo $lang_login['text_helpbox_note'] ?><font id= "waittime" color="red"></font></h2>
<?php
print("<table width='100%' border='1' cellspacing='0' cellpadding='1'><tr><td class=\"text\">\n");
print("<iframe src='" . get_protocol_prefix() . $BASEURL . "/shoutbox.php?type=helpbox' width='650' height='180' frameborder='0' name='sbox' marginwidth='0' marginheight='0'></iframe><br /><br />\n");
print("<form action='" . get_protocol_prefix() . $BASEURL . "/shoutbox.php' id='helpbox' method='get' target='sbox' name='shbox'>\n");
print($lang_login['text_message']."<input type='text' id=\"hbtext\" name='shbox_text' autocomplete='off' style='width: 500px; border: 1px solid gray' ><input type='submit' id='hbsubmit' class='btn' name='shout' value=\"".$lang_login['sumbit_shout']."\" /><input type='reset' class='btn' value=".$lang_login['submit_clear']." /> <input type='hidden' name='sent' value='yes'><input type='hidden' name='type' value='helpbox' />\n");
print("<div id=sbword style=\"display: none\">".$lang_login['sumbit_shout']."</div>");
print(smile_row("shbox","shbox_text"));
print("</td></tr></table></form></td></tr></table>");
}
echo "</div>";
echo "<div class='footer' >";
stdfoot();
echo "</div>";