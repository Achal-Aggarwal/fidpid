<?php require_once("includes/initialize.php"); ?>
<?php 
	$username = $_POST["name"];
	$user = User::find_by_email($username);
	if($user){
		sendMailTo($user->email,$user->name." your password is ".$user->password ,"Password for fidpid login");
		echo "true";
	}
	else echo "false";
?>
