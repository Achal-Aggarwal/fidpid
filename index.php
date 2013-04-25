<?php
ob_start();
 require_once("includes/initialize.php"); ?>
<?php 

	if(count($_GET)==0 || (!isset($_GET["Settings"]) && !isset($_GET["Home"]) && !isset($_GET["FeedBackForm"]) 
		&& !isset($_GET["Report"]) && !isset($_GET["Notification"]) && !isset($_GET["FeedBack"])
		 && !isset($_GET["View"]) && !isset($_GET["Preview"]) && !isset($_GET["FormView"]) && !isset($_GET["Logout"]) && !isset($_GET["Response"]))){
				if($session->is_logged_in()==true) redirect_to("./?Home");
				else redirect_to("login.php");
	}
	else if($session->is_logged_in()==false && !isset($_GET["View"])) redirect_to("login.php");
	if(!isset($_GET["View"])){
		if(isset($_GET["Logout"]))redirect_to("logout.php");
		$user = User::find_by_id($session->user_id);
		$config = Configuration::make($user->name,$user->pic);
	}
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
	<?php include("head.php");?>
</head>
<body>
	<div id="alert_dialog"></div>
	<script type="text/javascript">

		$(function() {
			$( "#alert_dialog" ).dialog({
		    		draggable: false,
		    		resizable: false,
		    		modal: true,
		    		autoOpen:false,
		    		dialogClass: "modal-dialog",
				create: function( event, ui ) {
				    			$(event.target).parent().css("position", "fixed");
				    		}
			});
		});
		function dialog_msg(msg,t){
			$( "#alert_dialog" ).dialog({ title: t });
			$( "#alert_dialog" ).html("<p>"+msg+"</p>");
			$( "#alert_dialog" ).dialog("open");
		}
	</script>
	<div class="wrap clearfix">	
		<?php if(isset($_GET["View"]))
				 include("form_view.php");
			else{ ?>
				<header>

				<?php include("header.php");?>
					
				</header>
				
				<?php include("nav.php");?>
				
				<?php if(isset($_GET["Preview"]) || isset($_GET["FormView"]))
						include("form_preview.php");
					else if(isset($_GET["Response"]))
						include("response.php");
					else {?>

				<div class="page-content">
						<?php if(isset($_GET["Settings"]))include("setting.php");
							else if(isset($_GET["Home"]))include("admin_home.php");//admin_home
							else if(isset($_GET["FeedBackForm"]))
								{
									if(isset($_GET["Create"]))
										include("form_create.php");
									else if(isset($_GET["Update"]))
										include("form_update.php");
									else
										include("form_menu.php");
								}
							else if(isset($_GET["Report"]) && empty($_GET["Report"]))include("reports.php");
							else if(isset($_GET["Report"]))include("report_per_quest.php");
							else if(isset($_GET["Notification"]))include("notifications.php");
							else if(isset($_GET["FeedBack"]))include("form_feedbacks.php");
						?>

				</div>

		<?php }
	} ?>
	</div>
<!-- Close Wrap -->

<footer>
	<?php //include("footer.php");?>
</footer>
<div id="dialog">
  <p><?php if($message)
  echo $message; ?></p>
</div>
</body>
<script type="text/javascript">
	
	$(function() {

	    <?php 
	    if($message){
		    echo '$( "#dialog" ).dialog({
		    		draggable: false,
		    		resizable: false,';
		    		if($message_id==1 || $message_id==2)
			    		echo 'modal: true,
			    		create: function( event, ui ) {
			    			$(event.target).parent().css("position", "fixed");
			    		},';
			    	else 
			    		echo'create: function( event, ui ) {
			    			setTimeout(function(){$("#dialog").dialog("close");}, 2300);
			    			$(event.target).parent().css("position", "fixed");
			    		},';
			    	echo 'hide: {effect: "fadeOut", duration: 1000},
		    		show: {effect: "fadeIn", duration: 500},
		    		dialogClass: "modal-dialog"
	        	});';
			/*if($message_id==0)
				echo "";*/
		}?>
		//setTimeout(function(){$('#dialog').dialog("close");}, 2000);
	  });
$("body").animate({opacity:'1'},1000,"swing");
</script>
</html>
<?php
  ob_end_flush();
?>
