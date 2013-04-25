<?php 
ob_start();
require_once("includes/initialize.php"); ?>
<?php
	if($session->is_logged_in()) {
	  redirect_to("./?Home");
	}

	// Remember to give your form's submit tag a name="submit" attribute!
	if (isset($_POST['submit'])) { // Form has been submitted.

		  $username = trim($_POST['name']);
		  $password = trim($_POST['psswrd']);
	  // Check database to see if username/password exist.
		  $found_user = User::authenticate($username, $password);
	  if ($found_user) {
	    	$session->login($found_user->id,$found_user->name);
	    	$session->message("Welcome to fidpid.",0);
			log_action('Login', "{$found_user->name} logged in.");
	    	redirect_to("./?Home");
	  } else {
	    // username/password combo was not found in the database
	    $message="Username/password combination is incorrect. Please try again.";
	    $message_id=1;
	  }
	  
	} else { // Form has not been submitted.
	  $username = "";
	  $password = "";
	}
?>
<!DOCTYPE html>
<html lang="en-US" style="overflow:hidden;">
<head>
	<?php include("head.php");?>
</head>

<body style="background:#cee1e5;">
	
	<div class="wrap clearfix" style="text-align:center;">	
		<div class="page-content admin-login" style="text-align:center">
				<div class="login-box"style="display:inline-block;">
					<div class="clogo" style=""><img style="height:40px;" src="./images/logo.png"></div>
					<form action="login.php" method="post" id="login_form">
						<div style="padding:20px 10px;">
							User Email <input type="text" name="name" id="name"><br>
							Password &nbsp;<input type="password" autocomplete="off" name="psswrd" id="psswrd">
						</div>
						<div style="text-align:left;padding:10px;">
							<img src="./images/forgot_pass.png" style="width:15px;vertical-align:middle;">
							<span class="forgotpsw" style="cursor:pointer;">Forgot password?</span>
							<input type="submit" name="submit" value="LOGIN" class="css3button" style="float:right;margin-top:-10px;">
						</div>
					</form>
				</div>
		</div>
	</div>
<!-- Close Wrap -->

<footer>
	<?php //include("footer.php");?>
</footer>
	<script type="text/javascript">
	function centerLoginBox(){
		var sh = $(window).height();
		var h = $('div.login-box').height();
		//alert(sh + '--' +h);
		var freespace = (sh-h)/2;
		freespace=freespace+'px';
        $('div.login-box').css('margin-top',freespace);
	}
	$(window).resize(function() {
		centerLoginBox();
	}); 
	jQuery(document).ready(function(){ 
		centerLoginBox();
	});
	
 </script>
<div id="dialog">
  <p><?php if($message)
  echo $message; ?></p>
</div>
<div id="alert_dialog"></div>
</body>
<script type="text/javascript">

	$(function() {
	    <?php if($message)
	    echo '$( "#dialog" ).dialog({
	    		draggable: false,
	    		resizable: false,
	    		modal: true,
	    		dialogClass: "modal-dialog",
        		create: function( event, ui ) {
			    			$(event.target).parent().css("position", "fixed");
			    		}
        	});';?>
        	
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
		$('#login_form').submit(function() {
		  if($('#login_form #name').val() == "" || $('#login_form #psswrd').val() == ""){
		  	dialog_msg("ERROR","Please fill the credentials first.");
		  	return false;
		  }
		  else return true;
		});
			$("body").animate({opacity:'1'},1000,"swing");
	function dialog_msg(t,msg){
			$( "#alert_dialog" ).dialog({ title: t });
			$( "#alert_dialog" ).html("<p>"+msg+"</p>");
			$( "#alert_dialog" ).dialog("open");
	}
	$(".forgotpsw").click(function(){
	 var uname = $('#login_form #name').val();	
	 var filter=/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
	 if (!filter.test(uname))dialog_msg("ERROR","Enter correct email id.");
	 else if(uname.length>0){
	 		var request = $.ajax({
			  url: "forgotpwd.php",
			  type: "POST",
			  data: {name : uname},
			  dataType: "html"
			});
			 
			request.done(function(msg) {
			  if(msg=="true")msg = "We have sent you your password on your email.";
			  else msg = "Sorry this email doesn't exist.";
			  dialog_msg("ERROR",msg);
			});
			 
			request.fail(function(jqXHR, textStatus) {
			  dialog_msg("ERROR","Sorry password retrival failed. Please try again.");
			});
	    }
	    else dialog_msg("ERROR","Enter your email id first.");

	});


    });
</script>
</html>
<?php
  ob_end_flush();
?>
