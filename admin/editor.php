<?php

/*

	OpenSerene
	
	Version: 0.1.0 (Bender)
	
	File: admin/editor.php
	
*/

session_start();

require("../engine/config.inc.php");
require("../engine/function.inc.php");
require("../engine/dbconnect.php"); 

$userTitle = $_SESSION['s_username'];
$newslimit = "5";
$getnews = mysql_query("SELECT * FROM stories ORDER BY id DESC LIMIT $newslimit");

# User Type Query
$uQuery = mysql_query("SELECT * FROM users WHERE username='$userTitle'") or die(mysql_error());
$uRow = mysql_fetch_array($uQuery);

# User Type Definitions
$uType = $uRow['type']; // Either "admin" or "user" 

# Initial Settings Query
$baseQuery = mysql_query("SELECT * FROM settings");
$fArray = mysql_fetch_array($baseQuery);

# Settings Definitions
$siteName = $fArray['title'];
$wysiwyg = $fArray['wysiwyg'];

# Page Specific Queries
$themeDir = $_GET['dir'];
$getFile = file_get_contents("$themeDir");

if(!isset($_GET['dir'])){
	header("Location: themes.php");
}

if(isset($userTitle)){
	if($uType == "admin") {
	if($_POST['submit']) {
		$filename = $_POST['hDir'];
		$contentOne = $_POST['content'];
		$newContent = stripslashes($contentOne);
			if (is_writable($filename)) {
			    if(!$handle = fopen($filename, 'w')) {
			        header("Location: editor.php?$filename&act=fail");
			        exit;
			    }				
			    if (fwrite($handle, $newContent) === FALSE) {
					header("Location: editor.php?dir=$filename&act=fail");
					exit;
			    }	  				
			    header("Location: editor.php?dir=$filename&act=success");	  
			    fclose($handle);
			} else {
				header("Location: editor.php?dir=$filename&act=fail");
			}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Theme Editor &mdash; <?= "$siteName";?> | OpenSerene</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<style type="text/css" media="all">@import "global.css";</style>
</head>
<body>
<div id="header">
	<?php echo "$siteName"; ?>
</div>
<div id="nav">
	<a href="index.php">Dashboard</a>
	<a href="submit.php">Write</a>
	<a href="themes.php" class="active">Appearance</a>
	<a href="users.php">User Management</a>
</div>
<div id="subnav">
	<a href="themes.php">Quit Editing</a>
	<a href="http://github.com/vicegirls/OpenSerene">Find Themes</a>
</div>
<div id="container">
	<h1 class="section">Theme Editor</h1>
	<?php if($_GET['act'] == "success") { ?>
	<div class="sectionBox">
		<h2 class="alert">Changes have been successfully applied.</h2>
	</div>
	<?php } if($_GET['act'] == "fail") { ?>
	<div class="sectionBox">
		<h2 class="alert">Whoops! That didn't work. Are the theme files writable?</h2>
	</div>
	<?php } ?>
	<div class="sectionBox">
		<h2 class="subsect">editing file <?= $themeDir ?></h2>
		<form action="editor.php?dir=<?= $themeDir ?>&act=success" method="post">
			<textarea name="content" rows="20" cols="90"><?= $getFile ?></textarea><br />
			<input type="hidden" name="hDir" value="<?= $themeDir ?>" />
			<input type="submit" name="submit" value="Edit File" class="submit" />
		</form>
	</div>
</div>
<div id="footer">
	POWERED BY <a href="http://github.com/vicegirls/OpenSerene">OPENSERENE</a><br />
	<a href="http://vicegirls.us">A VICEGIRLS JOINT</a>
</div>
</body>
</html>
<?php } else {
	header("Location: ../index.php");
	}
} else if(!isset($userTitle)) {
	header("Location: login.php");
} ?>