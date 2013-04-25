<?php require_once("includes/initialize.php"); ?>
<?php 
	$aLink = Dlink::make($session->user_id,$_POST["c"]);
	$aLink->create();
	echo "/?View=".$aLink->hash;
?>