<!-- Contains commonly used functions like addcontact, addgroup throughout the application to maintain readability.-->

<?php
include_once ("mysqlclass.php");
$con = mysqli_connect($dbhost, $dbuser, $dbpass, $database) ;
if ($con->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

function user_pass_check($username, $pass)
{
  global $con;
  $qry = "select * from account where username='$username'";
  $result = mysqli_query($con, $qry);
  
  if(!$result)
  {
    die("user_pass_check() failed.");
  }
  else
  {
    $row = mysqli_fetch_row($result);
    if(!$row)
    {
      return 3;//user doesn't exist
    }
    elseif(strcmp($row[2], $pass))
    {
      return 2; //Wrong Password
    }
    else
    {
      return 0; //user created successfully
    }
  }
}

function upload_error($result)
{
	switch ($result){
	case 1:
		return "UPLOAD_ERR_INI_SIZE"; //There is no error, the file uploaded with success.
	case 2:
		return "UPLOAD_ERR_FORM_SIZE"; //The uploaded file exceeds the upload_max_filesize directive
	case 3:
		return "UPLOAD_ERR_PARTIAL"; //The uploaded file was only partially uploaded.
	case 4:
		return "UPLOAD_ERR_NO_FILE"; //No file was uploaded.
	case 5:
		return "File has already been uploaded"; 
	case 6:
		return  "Failed to move file from temporary directory";
	case 7:
		return  "Upload file failed"; 
	case 8:
		return 	"Title should not be empty";
	}
}

function addContact($username, $contactname, $relation)
{
	global $con;

	$query = "SELECT id FROM account WHERE username='$username'";
	$result = mysqli_query($con, $query);
	$row = mysqli_fetch_row($result);
	$userid = $row[0];

	$query = "SELECT * FROM account WHERE username='$contactname'";
	$result = mysqli_query($con, $query );
	if (!$result)
	{
	   die ("addContact() failed. Could not query the database: <br />". mysqli_error($con));
	}
	$row = mysqli_fetch_row($result);
	if(!$row) 
		return 1; // user doesn't exist
	$query = "SELECT id FROM account WHERE username='$contactname'";
	$result = mysqli_query($con, $query);
	$row = mysqli_fetch_row($result);
	$contactid = $row[0];

	$query = "SELECT * FROM contacts WHERE userid='$userid' and contactid='$contactid'";
	$result = mysqli_query($con, $query);
	$row = mysqli_fetch_row($result);

	if($row)
		return 2; // contact alreay exist
	$query = "SELECT isblock FROM contacts WHERE userid='$contactid' and contactid='$userid'";
	$result = mysqli_query($con, $query);
	$row = mysqli_fetch_row($result);
	if($row != NULL && $row[0]=="block")
		return 4;
	$query = "INSERT INTO contacts(userid, contactid, priority,isblock) VALUES ('$userid', '$contactid', '$relation','unblock')";
	$result = mysqli_query($con, $query);
	if(!$result)
  {
		return 3;
	}
	else
  {
		return 0;
	}
}

function addGroup($username, $groupname, $topic, $discussion)
{
	global $con;

	$query = "SELECT * FROM `groups` WHERE `groupname`='$groupname'";
	$result = mysqli_query($con, $query );
	$row = mysqli_fetch_row($result);
	if ($row)
	{
	   return 1; //group already exists
	}
	$query = "INSERT INTO `groups` (`groupname`, `topic`, `discussion`) VALUES('$groupname', '$topic', '$discussion')";
	$result = mysqli_query($con, $query);
	if(!$result){
		echo mysqli_error($con);
		return 2;
	}
	else{
		return 0;
	}
}
?>
