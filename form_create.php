<?php require_once("includes/initialize.php"); ?>
<?php
	if($session->is_logged_in()==false) {
	  redirect_to("index.php");
	}
	//Found out whether the user already have one form or not
	$form = Forms::find_by_user_id($session->user_id);
	if($form){
		$session->message("You can not create a new form as you already have one.",1);
		redirect_to("./?FeedBackForm");
	}
	//else continue

	// Remember to give your form's submit tag a name="submit" attribute!
	if (isset($_POST['submit'])) { // Form has been submitted.
		$form = Forms::make($session->user_id,trim(implode("$#%", $_POST['quest'])), trim(implode("$#%", $_POST['lestrate'])), 
			trim(implode("$#%", $_POST['lrate'])), trim(implode("$#%", $_POST['arate'])), 
				trim(implode("$#%", $_POST['hrate'])), trim(implode("$#%", $_POST['hestrate'])), 
					trim($_POST['gmsg']), trim($_POST['qa']), 
						trim($_POST['choices']), trim($_POST['chose-answer']), 
							trim($_POST['backgrd']));

		$form->icon = 'images'.DS.'default_scroller.png';
		if(isset($_FILES['logo'])){
	  		$file = new File();
		  	if($file->attach_file($_FILES['logo']) && $file->save()){
		  		$form->logo = $file->file_path();
		  		log_action('File Upload', "{$session->user_name} uploaded ".$file->file_path());
		  	}
		  	else {
		  		if(!in_array("No file.",$file->errors))
		  			$message = "Something went wrong during logo upload.";
		  	}
		  	
		}
		if(isset($_FILES['icon'])){
	  		$file = new File();
		  	if($file->attach_file($_FILES['icon']) && $file->save()){
		  		$form->icon = $file->file_path();
		  		log_action('File Upload', "{$session->user_name} uploaded ".$file->file_path());
		  	}
		  	else {
		  		if(!in_array("No file.",$file->errors))
		  			$message = "Something went wrong during icon upload.";
		  	}
		  	
		}

		if($form->create()){
			$session->message("FeedBack Form published successfully. Now you can update it here.",0);
			redirect_to("./?FeedBackForm&Update");
		}
		else{
			$message = "Apologies. Something went wrong. FeedBack form can not be published right now. Please try again. ";
			$message_id = 1;
		}
	}
?>
<div class="feedform" style="font-size:1.1em;padding-top:10px;position:relative;">
	<span style="margin-left:20px;">CUSTOMIZE THE COLOUR SCHEME</span>
	<div class="colorbox" style="display: inline-block;">
		<div class="qa"></div>
		<div class="choices"></div>
		<div  class="chose-answer"></div>
		<div class="backgrd"></div>
	</div>
	<div class="colormap" style="margin-top:20px;border-bottom:3px solid #CEE1E5;">
		<table style="width:100%">
			<tr>
				<td>
					<div class="qa out"><div></div></div>
					<p>FONT COLOUR FOR QUESTIONS AND MESSAGES</p>
				</td>
				<td>
					<div class="choices out"><div></div></div>
					<p>FONT COLOUR FOR CHOICES</p>
				</td>
				<td>
					<div class="chose-answer out"><div></div></div>
					<p>FONT COLOUR FOR CHOSEN ANSWER</p>
				</td>
				<td>
					<div class="backgrd out"><div></div></div>
					<p>COLOUR FOR &nbsp;&nbsp; TOP NAVIGATION</p>
				</td>
			</tr>
		</table>
	</div>
	<form action method="post" id="form"   enctype="multipart/form-data" >
		<div class="greet-msg">
			<textarea name="gmsg" id="gmsg" placeholder="TYPE A MESSAGE TO GREET YOUR CUSTOMERS"></textarea>
		</div>
		<div style="position:relative;">
			<div class="arrow prev" style="top:43px;left:10px;"><img src="./images/arrow_left.png" style="height:40px;"></div>
			<div class="paginate" style="float:left;">
				<div class="qanda quest1 current">
					<textarea name="quest[]" class="questionT" placeholder="TYPE YOUR FIRST QUESTION"></textarea>
					<div style="text-align:left; padding:15px 25px;">
						ENTER YOUR CHOICES
						<br>
						<input type="text" class="optionT" placeholder="LOWEST RATING" name="lestrate[]"> 
						<input type="text" class="optionT" placeholder="LOW RATING" name="lrate[]">
						<input type="text" class="optionT" placeholder="AVERAGE RATING" name="arate[]">
						<input type="text" class="optionT" placeholder="HIGH RATING" name="hrate[]">
						<input type="text" class="optionT" placeholder="HIGHEST RATING" name="hestrate[]">
					</div>
				</div>
				<div class="qanda quest2 hide">
					<textarea name="quest[]" class="questionT" placeholder="TYPE YOUR SECOND QUESTION"></textarea>
					<div style="text-align:left; padding:15px 25px;">
						ENTER YOUR CHOICES
						<br>
						<input type="text" class="optionT" placeholder="LOWEST RATING" name="lestrate[]"> 
						<input type="text" class="optionT" placeholder="LOW RATING" name="lrate[]">
						<input type="text" class="optionT" placeholder="AVERAGE RATING" name="arate[]">
						<input type="text" class="optionT" placeholder="HIGH RATING" name="hrate[]">
						<input type="text" class="optionT" placeholder="HIGHEST RATING" name="hestrate[]">
					</div>
				</div>
				<div class="qanda quest3 hide">
					<textarea name="quest[]" class="questionT" placeholder="TYPE YOUR THIRD QUESTION"></textarea>
					<div style="text-align:left; padding:15px 25px;">
						ENTER YOUR CHOICES
						<br>
						<input type="text" class="optionT" placeholder="LOWEST RATING" name="lestrate[]"> 
						<input type="text" class="optionT" placeholder="LOW RATING" name="lrate[]">
						<input type="text" class="optionT" placeholder="AVERAGE RATING" name="arate[]">
						<input type="text" class="optionT" placeholder="HIGH RATING" name="hrate[]">
						<input type="text" class="optionT" placeholder="HIGHEST RATING" name="hestrate[]">
					</div>
				</div>
				<div class="qanda quest4 hide">
					<textarea name="quest[]" class="questionT" placeholder="TYPE YOUR FOURTH QUESTION"></textarea>
					<div style="text-align:left; padding:15px 25px;">
						ENTER YOUR CHOICES
						<br>
						<input type="text" class="optionT" placeholder="LOWEST RATING" name="lestrate[]"> 
						<input type="text" class="optionT" placeholder="LOW RATING" name="lrate[]">
						<input type="text" class="optionT" placeholder="AVERAGE RATING" name="arate[]">
						<input type="text" class="optionT" placeholder="HIGH RATING" name="hrate[]">
						<input type="text" class="optionT" placeholder="HIGHEST RATING" name="hestrate[]">
					</div>
				</div>
				<div class="qanda quest5 hide">
					<textarea name="quest[]" class="questionT" placeholder="TYPE YOUR FIFTH QUESTION"></textarea>
					<div style="text-align:left; padding:15px 25px;">
						ENTER YOUR CHOICES
						<br>
						<input type="text" class="optionT" placeholder="LOWEST RATING" name="lestrate[]"> 
						<input type="text" class="optionT" placeholder="LOW RATING" name="lrate[]">
						<input type="text" class="optionT" placeholder="AVERAGE RATING" name="arate[]">
						<input type="text" class="optionT" placeholder="HIGH RATING" name="hrate[]">
						<input type="text" class="optionT" placeholder="HIGHEST RATING" name="hestrate[]">
					</div>
				</div>
			</div>
			
			<div class="arrow next" style="right:10px;top:43px;"><img src="./images/arrow_right.png" style="height:40px;"></div>
		</div>
		<div class="upload" style="text-align:center;font-weight:bolder;margin:7px 0;">
			UPLOAD <div onclick="uploadIcon()">ICON</div> | <div onclick="uploadLogo()">LOGO</div>
			<input style="display:none;" type="file" name="icon" id="iconFile">
			<input style="display:none;" type="file" name="logo" id="logoFile">
		</div>
		<div style="background:#CEE1E5;text-align:center;height:40px;padding-top:5px;">
			<input type="button" class="css3button" value="DISCARD" onclick="javascript:window.location.href='./?FeedBackForm&Create';">
			
			<input type="submit" class="css3button" name="submit" value="PUBLISH">
		</dv>
		<input class="qa" type="hidden" name="qa" value ="#f7bf24">
		<input class="choices" type="hidden" name="choices" value ="#f7bf24">
		<input class="chose-answer" type="hidden" name="chose-answer" value ="#f7bf24">
		<input class="backgrd" type="hidden" name="backgrd" value ="#f7bf24">
	</form>
</div>
<script type="text/javascript">
	function uploadIcon(){
		$('.feedform .upload #iconFile').click();
		return false;
	}
	function uploadLogo(){
		$('.feedform .upload #logoFile').click();
		return false;
	}

	jQuery(document).ready(function(){
		$('body').css('background-color','#CEE1E5');
		$('.feedform .next').click(function(){
			var $current = $('.feedform .paginate div.qanda').filter('.current');
			var $next = $('.feedform .paginate div.qanda').filter('.current').next('div');
			if($next.length !== 0){
				$current.removeClass('current');
				$current.addClass('hide');
				$next.removeClass('hide');
				$next.addClass('current');
			}
			return false;
		});
		$('.feedform .prev').click(function(){
			var $current = $('.feedform .paginate div.qanda').filter('.current');
			var $prev = $('.feedform .paginate div.qanda').filter('.current').prev('div');
			if($prev.length !== 0){
				$current.removeClass('current');
				$current.addClass('hide');
				$prev.removeClass('hide');
				$prev.addClass('current');
			}
			return false;
		});
		$('.feedform #form').submit(function() {
				var sub=true;
				$('.feedform .questionT, .feedform .optionT, .feedform #gmsg, .feedform #logoFile').each(function(){
					if($(this).val().length<=0){ dialog_msg("Please fill all fields.","ERROR");sub=false; return;}
				});
				return sub;
		});
	});
	
	$(".colormap .out div").each(function(){
		var divison = $(this);
		var icolor = divison.css('background-color');
		var linktoit;
		if(divison.parent().hasClass('qa'))linktoit = $('.feedform input.qa');
		else if(divison.parent().hasClass('choices'))linktoit = $('.feedform input.choices');
		else if(divison.parent().hasClass('chose-answer'))linktoit = $('.feedform input.chose-answer');
		else if(divison.parent().hasClass('backgrd'))linktoit = $('.feedform input.backgrd');
		divison.spectrum({
		    color: icolor,
		    showButtons: false,
		    clickoutFiresChange: true,
			move: function(color) {
			    divison.css('background-color',color.toHexString());
			},
			change: function(color){
				linktoit.val(color.toHexString());
			}
		});
	});
</script>
