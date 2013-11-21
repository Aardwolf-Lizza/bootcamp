<?
require_once ( 'settings.php' );
require ( 'downloadfunctions.php' );
checkLogin('1 2 3 4 5 6 7 8');
mysql_connect($easyhostname,$easyusername, $easypassword) OR DIR ('Unable to connect to database! Please try again later.'. mysql_error());
mysql_select_db($easydb);

$thissucks="Closed Clan Files";
$uname = get_username ( $_SESSION['user_id'] );

if($_GET["cmd"]=="download" || $_POST["cmd"]=="download")
{
$id    = $_GET['id'];
$query = "SELECT * FROM Files WHERE ID = '$id'";

$result = mysql_query($query) or die('Error, query failed');
$myrow 	= mysql_fetch_array($result);

//header("Content-length: $FileSize");
//header("Content-type: $ContentType");
//header("Content-Disposition: attachment; filename=$FileName");

header('Content-Description: File Transfer');
header('Content-Type: ' . $myrow['ContentType']);
header('Content-Disposition: attachment; filename=' . $myrow['Name']);
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . $myrow['FileSize']);

echo $myrow['FileData'];

exit;
}

if ($_GET['cmd']=="delete")
   {
	  $id 	  = $_GET['id'];	  
	  $result = mysql_query("DELETE FROM Files WHERE ID='$id'");
	  checkdownloaderror($result,'deleted');
	}
	
if ( isset($_POST['upload']) && $_FILES['userfile']['size'] >  0)
{
	$fileName = $_FILES['userfile']['Name'];
	$tmpName  = $_FILES['userfile']['tmp_name'];
	$fileSize = $_FILES['userfile']['size'];
	$fileType = $_FILES['userfile']['type'];
	$madename = $_POST["Name"];
	$filedesc = $_POST["lvl"];
	$filerank = $_POST["Rank"];
	$filecate = $_POST["Category"];
	$fileadde = $_POST["added"];

	$fp      = fopen($tmpName, 'r');
	$content = fread($fp, filesize($tmpName));
	$content = addslashes($content);
	fclose($fp);

		if(!get_magic_quotes_gpc())
			{
			    $fileName = addslashes($fileName);
			}


	$query = "INSERT INTO Files (ID, FileName, FileSize, FileData, ContentType, LinkID, Rlvl, Name, Addedby, Permissions, Category, Rank, Locked, LastUpdate) VALUES (NULL, '".$fileName."', '".$fileSize."', '".$content."', '".$fileType."', NULL, '".$filedesc."', '".$madename."', '".$fileadde."', '".$filerank."', '".$filecate."', '".$filerank."', NULL, NULL)";

	$result = mysql_query($query);
	checkdownloaderror($result,'uploaded');
}
//this page calles the catagory list
require ( 'downloadnvtable.php' );

if ($_GET['cmd']=="edit") {
	if (isset($_POST['edit'])) {
		$name 	= $_POST['Name'];
		$rank 	= $_POST['Rank'];
		$level 	= $_POST['lvl'];
		$id 	= $_GET['id'];
		
		$sql = "UPDATE Files SET Name='$name',Rank='$rank',Rlvl='$level' WHERE ID=$id";
      $result = mysql_query($sql);
	  checkdownloaderror($result,'edited');
	}
	echo '<br>';
	echo '<br>';
require ('downloadedit.php');	
}

if(isset($_GET['id']) && $_GET["type"]=="text") {
$id    = $_GET['id'];
$query = "SELECT FileName, FileSize, FileData, ContentType, LinkID, Rlvl, Name, Addedby, Permissions, Category, Rank, Locked, LastUpdate FROM Files WHERE ID = '$id'";

$result = mysql_query($query) or die('Error, query failed'. mysql_error());
list($FileName, $FileSize, $FileData, $ContentType, $LinkID, $Rlvl, $Name, $Addedby, $Permissions, $Category, $Rank, $Locked, $LastUpdate) = mysql_fetch_array($result);

$thissucks="Goal Information(".$Name.")";
echo '<div style="width:600px; oveflow:hidden;"><br>';
echo nl2br($FileData);
echo '</div>';
if ($thissucks=="") {
	$thissucks="Main Page";
}
$pageContents = ob_get_contents (); // Get all the page's HTML into a string
ob_end_clean (); // Wipe the buffer

// Replace <!--TITLE--> with $pageTitle variable contents, and print the HTML
echo str_replace ('<!--TITLE-->', "Aardwolf's Bootcamp - ".$thissucks, $pageContents);
exit;	
}

if ($_GET["cmd"]=="view")
{
	$catname=$_GET["name"];
	$catid=$_GET["id"];
	$thissucks="Closed Clan Files(".$catname.")";
require ('downloadlist.php');
echo '<br>';
echo '<br>';
require ('downloadadd.php');
}
?>
