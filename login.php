
<?php
require_once("include/bittorrent.php");
dbconn();

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

<style>
	.bg{
		background-color: #333;
		height: 700px;
	}
	.lang{
		font-family: "Century Gothic", "Microsoft yahei";
		font-size: 15px;
		color: #00aaaa;
	}
	.notice{
		background-color: #00a8c6;
		color: white;
		font-family: "Century Gothic", "Microsoft yahei";
		font-size: 16px;
		padding-left: 36%;
	}
	.noticenav{
		background-color: #00a8c6;
		color: white;
		font-family: "Century Gothic", "Microsoft yahei";
		font-size: 16px;
		padding-left:43%;
	}
	/*.noticenav{*/
		/*margin-left: 200px;*/
	/*}*/
	.loginform{
		font-family: "Century Gothic", "Microsoft yahei";
		height: 80%;
		width: 45%;
		background-color: white;
		margin-left: 27%;
		margin-top: 2px;
		margin-bottom: 2px;
	}
	.rowhead{
		padding-left: 20%;
		font-family: "Hiragino Sans GB", "Microsoft YaHei", sans-serif;
		font-size: 17px;

	}
	.rowfollow{
		padding: 0px;
		margin: 0px;
	}
	.input{
		height: 40px;
		width: 100px;
	}
	.advance{
		padding-left: 20%;
		font-family: "Hiragino Sans GB", "Microsoft YaHei", sans-serif;
		font-size: 22px;
		/*background-color: #00a8c6;*/
	}
</style>


<form method="get" action="<?php echo $_SERVER['PHP_SELF'] ?>" >
<?php
print("<div align=\"right\" class='lang'>".$lang_login['text_select_lang']. $s . "</div>");
?>
</form>



<?php

unset($returnto);
if (!empty($_GET["returnto"])) {
	$returnto = $_GET["returnto"];
	if (!$_GET["nowarn"]) {
		print("<h1>" . $lang_login['h1_not_logged_in']. "</h1>\n");
		print("<p><b>" . $lang_login['p_error']. "</b> " . $lang_login['p_after_logged_in']. "</p>\n");
	}
}
?>
<form method="post" action="takelogin.php" class="bg">

<p class="notice">
	<?php echo "<div class='notice'>".$lang_login['p_need_cookies_enables']."</div>"?>
	<?php echo "<div class='notice'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$maxloginattempts.$lang_login['p_fail_ban']."</div>"?>
<?php echo "<div class='notice'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$lang_login['p_you_have']."<span style='color=red'>".remaining ()."</span>".$lang_login['p_remaining_tries']."</div>"?>
</p>



<table border="0" cellpadding="5" class="loginform">
<tr><td class="rowhead"><?php echo $lang_login['rowhead_username']?></td><td class="rowfollow" align="left"><input class="form-control form-control-solid placeholder-no-fix" type="text" name="username" style="width: 180px; border: 1px solid gray" /></td></tr>
<tr><td class="rowhead"><?php echo $lang_login['rowhead_password']?></td><td class="rowfollow" align="left"><input class="form-control form-control-solid placeholder-no-fix" type="password" name="password" style="width: 180px; border: 1px solid gray"/></td></tr>
	<?php
//show_image_code ();
	if ($iv == "yes") {
		unset($imagehash);
		$imagehash = image_code () ;
//		echo "<div><img style='margin-left: 50%' src=\"".htmlspecialchars("image.php?action=regimage&imagehash=".$imagehash)."\" /></div>";

		print ("<tr><td class=\"rowhead\" style=\"font-family: Century Gothic, Microsoft yahei;font-size: 17px;margin-left: 10%;>".$lang_functions['row_security_image']."</td>");
		print ("<td  align=\"left\"><img style='margin-left: 105%' src=\"".htmlspecialchars("image.php?action=regimage&imagehash=".$imagehash)."\" border=\"0\" alt=\"CAPTCHA\" /></td></tr>");
		print ("<tr><td class=\"rowhead\"><span style='font-family: \"Century Gothic\", \"Microsoft yahei\";font-size: 17px;'>".$lang_functions['row_security_code']."</td><td align=\"left\">");
		print("<input  class=\"form-control form-control-solid placeholder-no-fix\" type=\"text\" autocomplete=\"off\" style=\"width: 180px; border: 1px solid gray;\" name=\"imagestring\" value=\"\" />");
		print("<input type=\"hidden\" name=\"imagehash\" value=\"$imagehash\" /></td></tr>");
	}


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
<tr><td class="toolbox" colspan="2" align="left"><?php echo "<div class='advance'>".$lang_login['text_advanced_options']."</div>"?></td></tr>
<tr><td class="rowhead"><?php echo $lang_login['text_auto_logout']?></td><td class="rowfollow" align="left"><input class="mt-checkbox" type="checkbox" name="logout" value="yes" /><?php echo $lang_login['checkbox_auto_logout']?></td></tr>
<tr><td class="rowhead"><?php echo $lang_login['text_restrict_ip']?></td><td class="mt-checkbox " align="left"><label class="mt-checkbox"></label><input class="mt-checkbox" type="checkbox" name="securelogin" value="yes" /><?php echo $lang_login['checkbox_restrict_ip']?></td></tr>
<tr><td class="rowhead"><?php echo $lang_login['text_ssl']?></td><td class="rowfollow" align="left"><input class="checkbox" type="checkbox" name="ssl" value="yes" <?php echo $sec?> /><?php echo $lang_login['checkbox_ssl']?><br /><input class="checkbox" type="checkbox" name="trackerssl" value="yes" <?php echo $sectra?> /><?php echo $lang_login['checkbox_ssl_tracker']?></td></tr>

<tr ><td class="toolbox" colspan="2" align="right" ><input type="submit" value="<?php echo $lang_login['button_login']?>" class="btn" style="background-color:#00a8c6;color: white" /> <input  type="reset" value="<?php echo $lang_login['button_reset']?>" style="background-color: crimson;color: white;margin-right: 10%;margin-left: 20px" class="btn"/></td></tr>
</table>
<?php

if (isset($returnto))
	print("<input type=\"hidden\" name=\"returnto\" value=\"" . htmlspecialchars($returnto) . "\" />\n");

?>

	<p><?php echo "<div class='noticenav'>".$lang_login['p_no_account_signup']."</div>"?></p>
	<?php
	if ($smtptype != 'none'){
		?>
		<p><?php echo "<div class='noticenav'>".$lang_login['p_forget_pass_recover']?></p>
		<p><?php echo "<div class='noticenav' style='margin-left: -430px'>".$lang_login['p_resend_confirm']?></p>
		<?php
	}
//	if ($showhelpbox_main != 'no'){?>
<!--	<table width="700" class="main" border="0" cellspacing="0" cellpadding="0"><tr><td class="embedded" ">-->
<!--				<h2>--><?php //echo $lang_login['text_helpbox'] ?><!--<font class="small"> - --><?php //echo $lang_login['text_helpbox_note'] ?><!--<font id= "waittime" color="red"></font></h2>-->
<!--				--><?php
////				print("<table width='100%' border='1' cellspacing='0' cellpadding='1'><tr><td class=\"text\">\n");
////				print("<iframe src='" . get_protocol_prefix() . $BASEURL . "/shoutbox.php?type=helpbox' width='650' height='180' frameborder='0' name='sbox' marginwidth='0' marginheight='0'></iframe><br /><br />\n");
////				print("<form action='" . get_protocol_prefix() . $BASEURL . "/shoutbox.php' id='helpbox' method='get' target='sbox' name='shbox'>\n");
////				print($lang_login['text_message']."<input type='text' id=\"hbtext\" name='shbox_text' autocomplete='off' style='width: 500px; border: 1px solid gray' ><input type='submit' id='hbsubmit' class='btn' name='shout' value=\"".$lang_login['sumbit_shout']."\" /><input type='reset' class='btn' value=".$lang_login['submit_clear']." /> <input type='hidden' name='sent' value='yes'><input type='hidden' name='type' value='helpbox' />\n");
////				print("<div id=sbword style=\"display: none\">".$lang_login['sumbit_shout']."</div>");
////				print(smile_row("shbox","shbox_text"));
//				print("</td></tr></table></form></td></tr></table>");
//				}
//				stdfoot();
//
//				?>
<!--</form>-->



