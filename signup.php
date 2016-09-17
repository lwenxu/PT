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
		header("Location: " . $_SERVER['REQUEST_URI']);
	}
}
require_once(get_langfile_path("", false, $CURLANGDIR));
cur_user_check ();
$type = $_GET['type'];
if ($type == 'invite')
{
	registration_check();
	failedloginscheck ("Invite signup");
	$code = $_GET["invitenumber"];

	$nuIP = getip();
	$dom = @gethostbyaddr($nuIP);
	if ($dom == $nuIP || @gethostbyname($dom) != $nuIP)
	$dom = "";
	else
	{
	$dom = strtoupper($dom);
	preg_match('/^(.+)\.([A-Z]{2,3})$/', $dom, $tldm);
	$dom = $tldm[2];
	}

	$sq = sprintf("SELECT inviter FROM invites WHERE hash ='%s'",mysql_real_escape_string($code));
	$res = sql_query($sq) or sqlerr(__FILE__, __LINE__);
	$inv = mysql_fetch_assoc($res);
	$inviter = htmlspecialchars($inv["inviter"]);
	if (!$inv)
		stderr($lang_signup['std_error'], $lang_signup['std_uninvited'], 0);
	stdhead($lang_signup['head_invite_signup']);
}
else {
	registration_check("normal");
	failedloginscheck ("Signup");
	stdhead($lang_signup['head_signup']);
}


$s = "<select name=\"sitelanguage\" onchange='submit()'>\n";

$langs = langlist("site_lang");

foreach ($langs as $row)
{
	if ($row["site_lang_folder"] == get_langfolder_cookie()) $se = " selected"; else $se = "";
	$s .= "<option value=". $row["id"] . $se. ">" . htmlspecialchars($row["lang_name"]) . "</option>\n";
}
$s .= "\n</select>";
?>
<style xmlns="http://www.w3.org/1999/html">
	.lang{
		font-family: "Century Gothic", "Microsoft yahei";
		font-size: 15px;
		color: #00aaaa;
	}
	.cookie{
		font-size: 20px;
		font-family: "Century Gothic", "Microsoft yahei";
		color: white;
		background-color: #00a8c6;
		width: 100%;
	}
	.signup{
		font-family: "Century Gothic", "Microsoft yahei";
		height: 80%;
		width: 45%;
		background-color: white;
		margin-left: 27%;
		margin-top: 2px;
		margin-bottom: 2px;
		padding-top: 2px;
		padding-left: 5%;
	}
	.bg{
		background-color: #333;
		height: 720px;
		margin-top: 2px;

	}
	.navfont{
		font-family: "Century Gothic", "Microsoft yahei";
		font-size: 17px;
		margin-left: 10%;
	}
	.input{
		margin-left: 20%;
	}
	.disfont{
		color: #da3f3f;
		margin-top: 0px;
		margin-left: 20%;

	}
	.btnfont{
		font-family: "Century Gothic", "Microsoft yahei";
		font-size: 16px;
		color: #df5959;
	}
	.footerbar{
		margin-top: 17px;
		color: white;
		background-color: #00a8c6;
		height: 70px;
		width: 260%;
		font-size: 20px;
		font-family: "Consolas", "Menlo", "Courier", monospace;
		text-align: center;
		margin-left: -480px;
		margin-bottom: 0px;

	}
	.footfont{
		padding-top: 20px;
	}
</style>
<form method="get" action=<?php echo $_SERVER['PHP_SELF'] ?>>
<?php
if ($type == 'invite')
print("<input type=hidden name=type value='invite'><input type=hidden name=invitenumber value='".$code."'>");
print("<div align=right valign=top class='lang'>".$lang_signup['text_select_lang']. $s . "</div>");
?>
</form>
<b ></b>

<p>
<form method="post" action="takesignup.php">
<?php if ($type == 'invite') print("<input  type=\"hidden\" name=\"inviter\" value=\"".$inviter."\"><input type=hidden name=type value='invite'");?>
	<?php
	print("<div class=cookie align=center colspan=2>".$lang_signup['text_cookies_note']."<div style=\"color=red\">".$lang_signup['text_all_fields_required']."</div>"."</div>");
	?>


<div class="bg">
<div class="signup">
<!--<div><span>--><?php //echo $lang_signup['row_desired_username']?><!--</span><input class="form-control form-control-solid placeholder-no-fix " type="text" style="width: 200px" name="wantusername" /></div>-->
<!--	<font class=small>--><?php //echo "<span class='disfont'>".$lang_signup['text_allowed_characters']."</span>" ?><!--</font></div></div>-->

<div class=rowhead><?php echo  "<span class='navfont'>".$lang_signup['row_desired_username']."</span>" ?></div><div class=rowfollow align=left><input class="form-control form-control-solid placeholder-no-fix input" type="text" style="width: 200px" name="wantusername" />
<?php echo "<span class='disfont'>".$lang_signup['text_allowed_characters']."</span>" ?>
<div class=rowhead><?php echo "<span class='navfont'>".$lang_signup['row_pick_a_password']."</span>" ?></div><div class=rowfollow align=left><input class="form-control form-control-solid placeholder-no-fix input" type="password" style="width: 200px" name="wantpassword" />
<?php echo "<span class='disfont'>".$lang_signup['text_minimum_six_characters']."</span>" ?>
<div class=rowhead><?php echo "<span class='navfont'>".$lang_signup['row_enter_password_again']."</span>" ?></div><div class=rowfollow align=left><input class="form-control form-control-solid placeholder-no-fix input"  type="password" style="width: 200px" name="passagain" /></div>
			<br>
<?php
show_image_code ();
?>
<div class=rowhead><?php echo "<span class='navfont'>".$lang_signup['row_email_address']."</span>" ?></div><div class=rowfollow align=left><input class="form-control form-control-solid placeholder-no-fix input" type="text" style="width: 200px" name="email" /></div>
<?php echo "<span class='navfont'>".($restrictemaildomain == 'yes' ? $lang_signup['text_email_note'].allowedemails() : "")."</span>" ?>




<div class=rowhead><?php echo "<span class='navfont'>".$lang_signup['row_gender']."</span>" ?></div>
	<div class=rowfollow align=left style="margin-left: 20%">
<input type=radio name=gender value=Male><?php echo "<span style='color: #00c4ff;font-family: Century Gothic, Microsoft yahei;font-size: 17px;'>".$lang_signup['radio_male'] ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio name=gender value=Female><?php echo "<span style='color: #da3f3f'>".$lang_signup['radio_female'] ?></div>

<tr style="margin-left: 20%"><td style="padding-left: 20%" class=rowhead ></td><td style="padding-left: 20%" class=rowfollow align=left><input style="margin-left: 20%" type=checkbox name=rulesverify value=yes><?php echo "<span class='btnfont'>".$lang_signup['checkbox_read_rules'] ?><br />
<input style="margin-left: 20%" class="input" type=checkbox name=faqverify value=yes><?php echo "<span class='btnfont'>".$lang_signup['checkbox_read_faq'] ?> <br />
<input style="margin-left: 20%" type=checkbox name=ageverify value=yes><?php echo "<span class='btnfont'>".$lang_signup['checkbox_age'] ?></td></tr>
<input style="margin-left: 20%" style="margin-left: 20%" type=hidden name=hash value=<?php echo $code?>><br><br>
<input class="btn green uppercase" style="margin-left: 37%;background-color: #00a8c6;color: white;" type=submit value=<?php echo $lang_signup['submit_sign_up'] ?>>
</div>
</div>
	<div class="footerbar">
	<p class="footfont">NWU        PT</p>
	</div>
</form>

<?php
//stdfoot();
