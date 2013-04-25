<?php require_once("includes/initialize.php"); ?>
<?php
	if($session->is_logged_in()==false) {
	  redirect_to("index.php");
	}
	$temp_user = $user;
	// Remember to give your form's submit tag a name="submit" attribute!
	if (isset($_POST['submit'])) { // Form has been submitted.
		  $user = User::find_by_id($session->user_id);
		  if(isset($_POST['name']) && !empty($_POST['name'])){$user->name = trim($_POST['name']);}
		  if(isset($_POST['email']) && !empty($_POST['email'])){
		  	$u = User::find_by_email(trim($_POST['email']));
		  	if($u == false)$user->email = trim($_POST['email']);
		  }
		  if(isset($_POST['cpsswrd']))$current_password = trim($_POST['cpsswrd']);
		  if(isset($_POST['npsswrd']))$new_password = trim($_POST['npsswrd']);
		  if(isset($_FILES['logoFile'])){
		  		$file = new File();
			  	if($file->attach_file($_FILES['logoFile']) && $file->save()){
			  		$prev_uploaded_pic = $user->pic;
			  		$user->pic = $file->file_path();
			  		log_action('File Upload', "{$session->user_name} uploaded ".$file->file_path());
			  	}
			  	else {
			  		if(!in_array("No file.",$file->errors))
			  			$message = "Something went wrong during pic upload.";
			  	}
			  	
			}
		
  		if(!empty($_POST['cpsswrd']) && !empty($_POST['npsswrd']))
  			if($current_password == $user->password)$user->password =  $new_password;
  		else {
		    // username/password combo was not found in the database
		    $message =  $message." "."Current password is incorrect.";
		    $credit_incorrect=true;
		  }
  		if($temp_user != $user)
  			if($user->update()){
  				if(isset($prev_uploaded_pic))File::destroy($prev_uploaded_pic);
				log_action('User Updated', "{$session->user_name} settings changed.");
				$session->message("Setting updated successfully.",0);
				redirect_to("./?Settings");
			}
			else $message = $message." "."Settings can't be updated right now.";
			$message_id = 1;
	  
	} else { // Form has not been submitted.
	  $username = "";
	  $password = "";
	}
?>
<div class="profilePage" style="font-size:1.2em;font-weight:bold;margin:0 5%;padding-top:5%;height:100%;">
	<form action method="post" enctype="multipart/form-data" id="form">
		<img src="<?php echo "./".str_replace("\\", "/", $user->pic);?>" id="dp" style="margin-bottom:30px;display:block;border:5px solid #CEE1E5; height:200px;"/>
		NAME : <input type="text" name="name" id="name" disabled value='<?php echo $user->name;?>'>
		<img src="./images/profile_page_edit.png" onclick="toggleDisable('name');"class="link" style="vertical-align:middle;margin:0px 5px;height:17px;"><br><br>
		EMAIL : <input type="text" name="email" id="email" disabled value='<?php echo $user->email;?>'>
		<img src="./images/profile_page_edit.png" onclick="toggleDisable('email');"class="link" style="vertical-align:middle;margin:0px 5px;height:17px;"><br><br>
		
		<a class="inpageLink chPass" style="cursor:pointer;">CHANGE PASSWORD</a>
		<br><br>
	
		<div id="changePass" class="hide" >
			<table style="width:100%;">
				<tr ><td style="width:240px;">CURRENT PASSWORD<br><br></td><td><input type="text" disabled style="width:90%" name="cpsswrd" id="cpsswrd"></td></tr>
				<tr ><td style="width:240px;">NEW PASSWORD<br><br></td><td><input type="password" disabled style="width:90%" name="npsswrd" id="npsswrd"></td></tr>
				<tr ><td style="width:240px;">CONFIRM<br><br></td><td><input type="password" disabled style="width:90%" id="rpsswrd"></td></tr>
			</table>
		</div>
		
		<a class="inpageLink link"  onclick="uploadLogo();">UPLOAD</a> PROFILE PICTURE
		<input style="display:none;" type="file" name="logoFile" id="logoFile">
		<dir style="margin: 25px auto;max-width: 240px;min-height:70px;">
			<input type="button" value="DISCARD" class="css3button" style="width:90px;float:left;">
			<input type="submit" name="submit" value="SAVE" class="css3button" style="width:90px;float:right;">
		</dir>
	</form>
</div>
<script type="text/javascript"> 
	function uploadLogo(){
		$('.profilePage #logoFile').click();
		return false;
	}
	function toggleDisable(ele){
		var name = $('.profilePage #'+ele);
			name.attr('disabled',false);
	}
	jQuery(document).ready(function(){
        $('.profilePage a.chPass').click(function(){
        	$('#changePass input').each(function(){
        		$(this).attr('disabled',false);
        	});
        	$('#changePass').removeClass('hide');
        });
        var mess;
        $('.profilePage #form').submit(function(event) {
        	var dosubmit = true;
        	var name = $('.profilePage #form #name').val();
        	var email = $('.profilePage #form #email').val();
        	var filter=/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
        	var cpass = $('.profilePage #form #cpsswrd');
        	var npass = $('.profilePage #form #npsswrd').val();
        	var rpass = $('.profilePage #form #rpsswrd').val();
		 	var error = new Array();
		 	var msg="";
		 	var i=0;
		 	if(name.length<=0){error[i++] ="name"; dosubmit =  false;}
		 	if(email.length<=0){error[i++] ="email"; dosubmit =  false;}
		 	else if(!filter.test(email)){error[i++] ="email"; dosubmit =  false;}
		 	if(cpass.attr('disabled') == undefined){//Password change menu is opened
		 		if(cpass.val().length+npass.length+rpass.length !== 0){
		 			if(cpass.val().length <5){error[i++] ="current password"; dosubmit =  false;}
		 			if(npass.length < 5){error[i++] ="new password"; dosubmit =  false;}
		 			if(rpass.length < 5){error[i++] ="repeat password"; dosubmit =  false;}
		 			if(rpass != npass){msg ="New and repeat passwords doesn't match."; dosubmit =  false;}
		 		}
		 	}
		 	if(error.length!=0)msg = "Please correctly fill " + error.join(",") + " field(s). " + msg;
		 	if(msg.length!=0)dialog_msg(msg,"ERROR");
		 	return dosubmit;
		});
	});
 </script>
