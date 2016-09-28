<?php
require "include/bittorrent.php";
dbconn();
failedloginscheck ("Recover",true);
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
			height: 70%;
			width: 45%;
			background-color: #fff;
			margin-left: 28%;
			border-radius: 9px;
		}
		.footer{
			color: white;
		}
		h1{
		text-align: center;
		}
		td{
			border: 0px;
		}
		table{
		border: 0px;
		margin-left: 20%;
		}
		input[type=submit]{
			margin-left: 10%;
		}
	</style>
";
$take_recover = !isset($_GET['sitelanguage']);
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

function bark($msg) {
	global $lang_recover;
	stdhead();
	stdmsg($lang_recover['std_recover_failed'], $msg);
	stdfoot();
	exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	if ($iv == "yes")
	check_code ($_POST['imagehash'], $_POST['imagestring'],"recover.php",true);
	$email = unesc(htmlspecialchars(trim($_POST["email"])));
	$email = safe_email($email);
	if (!$email)
	failedlogins($lang_recover['std_missing_email_address'],true);
	if (!check_email($email))
	failedlogins($lang_recover['std_invalid_email_address'],true);
	$res = sql_query("SELECT * FROM users WHERE email=" . sqlesc($email) . " LIMIT 1") or sqlerr(__FILE__, __LINE__);
	$arr = mysql_fetch_assoc($res);
	if (!$arr) failedlogins($lang_recover['std_email_not_in_database'],true);
	if ($arr['status'] == "pending") failedlogins($lang_recover['std_user_account_unconfirmed'],true);

	$sec = mksecret();

	sql_query("UPDATE users SET editsecret=" . sqlesc($sec) . " WHERE id=" . sqlesc($arr["id"])) or sqlerr(__FILE__, __LINE__);
	if (!mysql_affected_rows())
	stderr($lang_recover['std_error'], $lang_recover['std_database_error']);

	$hash = md5($sec . $email . $arr["passhash"] . $sec);
	$ip = getip() ;
	$title = $SITENAME.$lang_recover['mail_title'];
	$body = <<<EOD
{$lang_recover['mail_one']}($email){$lang_recover['mail_two']}$ip{$lang_recover['mail_three']}
<b><a href="javascript:void(null)" onclick="window.open('http://$BASEURL/recover.php?id={$arr["id"]}&secret=$hash')"> {$lang_recover['mail_this_link']} </a></b><br />
http://$BASEURL/recover.php?id={$arr["id"]}&secret=$hash
{$lang_recover['mail_four']}
EOD;

	sent_mail($arr["email"],$SITENAME,$SITEEMAIL,change_email_encode(get_langfolder_cookie(), $title),change_email_encode(get_langfolder_cookie(),$body),"confirmation",true,false,'',get_email_encode(get_langfolder_cookie()));

}
elseif($_SERVER["REQUEST_METHOD"] == "GET" && $take_recover && isset($_GET["id"]) && isset($_GET["secret"]))
{
	$id = 0 + $_GET["id"];
	$md5 = $_GET["secret"];

	if (!$id)
	httperr();

	$res = sql_query("SELECT username, email, passhash, editsecret FROM users WHERE id = " . sqlesc($id)) or sqlerr(__FILE__, __LINE__);
	$arr = mysql_fetch_array($res) or httperr();

	$email = $arr["email"];

	$sec = hash_pad($arr["editsecret"]);
	if (preg_match('/^ *$/s', $sec))
	httperr();
	if ($md5 != md5($sec . $email . $arr["passhash"] . $sec))
	httperr();

	// generate new password;
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

	$newpassword = "";
	for ($i = 0; $i < 10; $i++)
	$newpassword .= $chars[mt_rand(0, strlen($chars) - 1)];

	$sec = mksecret();

	$newpasshash = md5($sec . $newpassword . $sec);

	sql_query("UPDATE users SET secret=" . sqlesc($sec) . ", editsecret='', passhash=" . sqlesc($newpasshash) . " WHERE id=" . sqlesc($id)." AND editsecret=" . sqlesc($arr["editsecret"])) or sqlerr(__FILE__, __LINE__);

	if (!mysql_affected_rows())
	stderr($lang_recover['std_error'], $lang_recover['std_unable_updating_user_data']);
	$title = $SITENAME.$lang_recover['mail_two_title'];
	$body = <<<EOD
{$lang_recover['mail_two_one']}{$arr["username"]}
{$lang_recover['mail_two_two']}$newpassword
{$lang_recover['mail_two_three']}
<b><a href="javascript:void(null)" onclick="window.open('http://$BASEURL/login.php')">{$lang_recover['mail_here']}</a></b>
{$lang_recover['mail_three_1']}
<b><a href="http://www.google.com/support/bin/answer.py?answer=23852" target='_blank'>{$lang_confirm_resend['mail_google_answer']}</a></b>
{$lang_recover['mail_three_2']}
{$lang_recover['mail_two_four']}

EOD;

	sent_mail($email,$SITENAME,$SITEEMAIL,change_email_encode(get_langfolder_cookie(), $title),change_email_encode(get_langfolder_cookie(),$body),"details",true,false,'',get_email_encode(get_langfolder_cookie()));

}
else
{
	stdhead();
	$s = "<select name=\"sitelanguage\" onchange='submit()'>\n";
	
	$langs = langlist("site_lang");
	
	foreach ($langs as $row)
	{
		if ($row["site_lang_folder"] == get_langfolder_cookie()) $se = " selected=\"selected\""; else $se = "";
		$s .= "<option value=\"". $row["id"]."\"" . $se. ">" . htmlspecialchars($row["lang_name"]) . "</option>\n";
	}
	$s .= "\n</select>";
	?>
	<form  method="get" action="<?php echo $_SERVER['PHP_SELF'] ?>">
	<?php
	print("<div class='langselect' align=\"right\">".$lang_recover['text_select_lang']. $s . "</div>");
	?>
	</form>
	<div class="loginbox">
	<h1><?php echo $lang_recover['text_recover_user'] ?></h1>
	<p><?php echo $lang_recover['text_use_form_below'] ?></p>
 	<p><?php echo $lang_recover['text_reply_to_confirmation_email'] ?></p>
  	<p><b><?php echo $lang_recover['text_note'] ?><?php echo $maxloginattempts;?></b><?php echo $lang_recover['text_ban_ip'] ?></p>
	<p><?php echo $lang_recover['text_you_have'] ?><b><?php echo remaining ();?></b><?php echo $lang_recover['text_remaining_tries'] ?></p>
	<form method="post" action="recover.php">
	<table border="1" cellspacing="0" cellpadding="10">
	<tr><td class="rowhead"><?php echo $lang_recover['row_registered_email'] ?></td>
	<td class="rowfollow"><input type="text" style="width: 150px" name="email" /></td></tr>
	<?php
	show_image_code ();
	?>
	<tr><td class="toolbox" colspan="2" align="center"><input type="submit" value="<?php echo $lang_recover['submit_recover_it'] ?>" class="btn" /></td></tr>
	</table></form>
	</div>
	<?php
	echo "<div class='footer' >";
	stdfoot();
	echo "</div>";
}
