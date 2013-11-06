<?php
	require_once ( 'settings.php' );
	require_once ( 'websitefunctions.php' );
	session_start ();
	
	$query = "SELECT * FROM `users` WHERE `ID` = " . $db->qstr ( $_SESSION['user_id'] );
	$row = $db->getRow ( $query );

	if ( array_key_exists ( '_submit_check', $_POST ) )
	{
		if ( $_POST['username'] != '' && $_POST['password'] != '' )
		{
			$guildname=$db->qstr ( $_POST['username'] );
			$guildname=ucfirst  ($guildname);
			$damnitwork=$_POST['password'];
			
			if ( $damnitwork == $adminlogin )
			{
			$query = 'SELECT ID, Username, Active, Password FROM users WHERE Username = '. $db->qstr ( ucfirst ($_POST['username'] ));
			} else {
			$query = 'SELECT ID, Username, Active, Password FROM users WHERE Username = '. $db->qstr ( ucfirst ($_POST['username'] )) .' AND Password = ' . $db->qstr ( md5 ( $_POST['password'] ) );
			}

			if ( $db->RecordCount ( $query ) == 1 )
			{
				$row = $db->getRow ( $query );
				if ( $row->Active == 1 )
				{
					if (isset($_POST['remember'])) {
						$rem = TRUE;
					} else {
						$rem = FALSE;
					}
					set_login_sessions ( $row->ID, $row->Password, $rem ); 
					
				 header ( "Location: " . REDIRECT_AFTER_LOGIN );
				}
				elseif ( $row->Active == 0 ) {
					$error = 'Your membership was not activated. Please open the email that we sent and click on the activation link.';
				}
				elseif ( $row->Active == 2 ) {
					$error = 'You are suspended!';
				}
			}
			else {		
				$error = 'Login failed! ';		
			}
		}
		else {
			$error = 'Please use both your username and password to access your account';
		}
	}
$fido = get_username ( $_SESSION['user_id'] );
?> 
<!DOCTYPE HTML>
<html>
<head>
<meta name="description" content="Bootcamp is a newbie clan for the MUD aardwolf, we aim to help any/all newbies on the mud, and prepare them for day to day clan life">
<meta name="keywords" content="aardwolf,boot,bootcamp,aardwolf clan,aard,aardwolf newbie,newbie">
<meta name="author" content="Hadar">
<meta charset="UTF-8">
<title><!--TITLE--></title>
<link rel="stylesheet/less" type="text/css" href="style.less">
<script src="less.js" type="text/javascript"></script>
<?php
if ($_SESSION['logged_in']) {
	?>
<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="jquery-latest.js"></script>
<script type="text/javascript" src="jquery.tablesorter.js"></script>
        <script type="text/javascript">
			$(document).ready(function() {
				$("#sortedtable").tablesorter({ sortlist: [0,1] });
			});
		</script>
<script language="javascript" type="text/javascript">
<!--
function popitup(url) {
	newwindow=window.open(url,'name','height=500,width=500,scrollbars=yes,resizable');
	if (window.focus) {newwindow.focus()}
	return false;
}

 
// -->
</script>
        <?php } ?>
</head>
<body>
<div id="header">Boot Camp</div>
<br>
<div id="wrapper">
<div id="nav">
<?php
   $result = mysql_query("SELECT * FROM `public` ORDER BY ID"); 
   while($r=mysql_fetch_array($result)) {
	   $id 		= $r['ID'];
	   $link 	= $r['Link'];
	   $active  = $r['Active'];
	   if ($active==1) {
    		echo '<a href="?page=public&amp;ID='.$id.'">'.$link.'</a><br>';
	   }
}
   
  if (isadmin ( $_SESSION['user_id'] ) || hasaccess ( $_SESSION['user_id'] )) {
  	echo "Under Review<br>";
	   $result = mysql_query("SELECT * FROM `public` ORDER BY ID");
	   $count	= 0; 
   		while($r=mysql_fetch_array($result)) {
		   $id 		= $r['ID'];
		   $link 	= $r['Link'];
		   $active  = $r['Active'];
	   if ($active==0 && $id != 13 && $id !=17 && $id !=18) {
		   $count++;
    		echo '<a href="?page=publicreview&ID='.$id.'" class="underreview">'.$link.'</a><br>';
	   }
}
	   if ($count == 0) {
		   echo 'I hath say, there is nothing to review';
		   echo '<br>';
	   }
  }
    if (!$_SESSION['logged_in']) {
		notloggedin();
	} else {
		nowloggedin();
	}
	
	  if (isadmin ( $_SESSION['user_id'] ) || hasaccess ( $_SESSION['user_id'] )) {
    echo '<br>Admin Menu<br>';
    leaderandaccess();
	if ( isadmin ( $_SESSION['user_id'] )) { ?>
	<a href="?page=register">Register People</a>
    <? }
   }
    if ($_SESSION['logged_in']) { 
    	?>
    <form class="form" action="?page=logout" method="post">
    	<input type="submit" name="Submit" class="button" value="Log out" />
    </form>
    <?php
	} else { ?>
    	<a href="?page=howtoregister">Register</a><br>
		<a href="?page=forgot_password">Password recovery?</a>
	<?php }
	?>
	<br>
	<!--begin aardwolf stuff !-->
<a href="http://www.topmudsites.com/cgi-bin/topmuds/rankem.cgi?id=Sirene" target="_blank"><img src="/images/vote.gif" width="72" height="29" alt="Vote for Aardwolf Mud!"></a>
<a href="http://www.aardwolf.com/play/index.htm"><img src="/images/play2.gif" width="72" height="29" alt="Play Aardwolf Now!!"></a>
<a href="http://www.mudconnect.com/cgi-bin/vote_rank.cgi?mud=Aardwolf" target="_blank"> <img src="http://www.mudconnect.com/images/tmcvote1.gif"  alt="Vote for Our Mud on TMC!"></a>
<!--end aardwolf stuff !-->
<p>
<a href="http://jigsaw.w3.org/css-validator/check/referer">
    <img style="border:0;width:88px;height:31px"
        src="http://jigsaw.w3.org/css-validator/images/vcss-blue"
        alt="Valid CSS!" />
</a>
</p>
</div>
<div id="content">
	    <?
	if (isset ( $error )) {
		echo 'Well shit something went wrong, please tell hadar with error:'.$error;
	}
if (isset($_GET['raffle'])) {
$page = $_GET['raffle'];
if ( !empty($page) && file_exists('./raffle/' . $page . '.php') && stristr( $page, '.' ) == False ) 
{
   $file = './raffle/' . $page . '.php';
}
else
{
   $file = './raffle/index.php';
}

include $file;	
} elseif (isset($_GET['forum'])) {
	$page=$_GET['forum'];
	if (!empty($page) && file_exists('./forum/'.$page.'.php') && stristr($page, '.')== False)
	{
		$file = './forum/'.$page.'.php';
	} else {
		$file = './forum/index.php';
	}
	
include $file;
} else {
$page = $_GET['page'];
if ( !empty($page) && file_exists('./' . $page . '.php') && stristr( $page, '.' ) == False ) 
{
   $file = './' . $page . '.php';
}
else
{
   $file = './default.php';
}

include $file;
}
?>
</div>
</div>
</body>
</html>
<?php
if ($thissucks=="") {
	$thissucks="Main Page";
}
$pageContents = ob_get_contents (); // Get all the page's HTML into a string
ob_end_clean (); // Wipe the buffer

// Replace <!--TITLE--> with $pageTitle variable contents, and print the HTML
echo str_replace ('<!--TITLE-->', "Aardwolf's Bootcamp - ".$thissucks, $pageContents);
?>
