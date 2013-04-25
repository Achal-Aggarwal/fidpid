<?php require_once("includes/initialize.php"); ?>
<?php
	if($session->is_logged_in()==false) {
	  redirect_to("index.php");
	}
	if(!isset($_GET['Update']))redirect_to('./?FeedBackForm');

	// Remember to give your form's submit tag a name="submit" attribute!
	$form = Forms::find_by_user_id($session->user_id);
	if($form == false){$session->message('First create a form.');redirect_to('./?FeedBackForm');}
	$temp_form = $form;
	if (isset($_POST['submit'])) { // Form has been submitted.
		if($_POST['submit']=="UPDATE" || (isset($_POST['fghfh']) && $_POST['submit']!="EDIT")){
			if($_POST['submit']=="UPDATE" && !isset($_POST['fghfh'])){
				if(isset($_POST['quest']))$form->question=trim(implode("$#%",$_POST['quest']));
				if(isset($_POST['lestrate']))$form->ch_lowest=trim(implode("$#%",$_POST['lestrate']));
				if(isset($_POST['lrate']))$form->ch_low=trim(implode("$#%",$_POST['lrate']));
				if(isset($_POST['arate']))$form->ch_average=trim(implode("$#%",$_POST['arate']));
				if(isset($_POST['hrate']))$form->ch_high=trim(implode("$#%",$_POST['hrate']));
				if(isset($_POST['hestrate']))$form->ch_highest=trim(implode("$#%",$_POST['hestrate']));
				if(isset($_POST['gmsg']))$form->greet_msg=trim($_POST['gmsg']);
				if(isset($_POST['qa']))$form->fc_qm=trim($_POST['qa']);
				if(isset($_POST['choices']))$form->fc_ch=trim($_POST['choices']);
				if(isset($_POST['chose-answer']))$form->fc_ca=trim($_POST['chose-answer']);
				if(isset($_POST['backgrd']))$form->fc_bg=trim($_POST['backgrd']);
				if(isset($_FILES['logo'])){
			  		$file = new File();
				  	if($file->attach_file($_FILES['logo']) && $file->save()){
				  		$prev_uploaded_logo = $form->logo;
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
				  		$prev_uploaded_icon = $form->icon;
				  		$form->icon = $file->file_path();
				  		log_action('File Upload', "{$session->user_name} uploaded ".$file->file_path());
				  	}
				  	else {
				  		if(!in_array("No file.",$file->errors))
				  			$message = "Something went wrong during icon upload.";
				  	}
				  	
				}
			}
			else{
				$form = $_SESSION["form"];
				if(is_array($form->question))$form->question=trim(implode("$#%",$form->question));
				if(is_array($form->ch_lowest))$form->ch_lowest=trim(implode("$#%",$form->ch_lowest));
				if(is_array($form->ch_low))$form->ch_low=trim(implode("$#%",$form->ch_low));
				if(is_array($form->ch_average))$form->ch_average=trim(implode("$#%",$form->ch_average));
				if(is_array($form->ch_high))$form->ch_high=trim(implode("$#%",$form->ch_high));
				if(is_array($form->ch_highest))$form->ch_highest=trim(implode("$#%",$form->ch_highest));
				unset($_SESSION["form"]);
				echo "from preview";
			}
			if($form->update()){
				if(isset($prev_uploaded_logo))File::destroy($prev_uploaded_logo);
				if(isset($prev_uploaded_icon))File::destroy($prev_uploaded_icon);
				$session->message($message." "."FeedBack Form updated successfully.",0);
			}
			else{
				if($temp_form==$form){
					$session->message($message." "."FeedBack Form updated successfully.",0);
				}
				else 
				$session->message($message." "."Apologies. Something went wrong. FeedBack form can not be updated right now. Please try again. ",1);
			}
			redirect_to("./?FeedBackForm&Update");

		}

	}
	if(isset($_POST['submit']) && $_POST['submit']=="EDIT"){
		$form_temp = $_SESSION["form"];
		unset($_SESSION["form"]);
		if($form_temp->icon!=$form->icon)File::destroy($form_temp->icon);
		if($form_temp->logo!=$form->logo)File::destroy($form_temp->logo);
		$form = $form_temp;
		
		

	}
	else{
		$form->question=explode("$#%",$form->question);
		$form->ch_lowest=explode("$#%",$form->ch_lowest);
		$form->ch_low=explode("$#%",$form->ch_low);
		$form->ch_average=explode("$#%",$form->ch_average);
		$form->ch_high=explode("$#%",$form->ch_high);
		$form->ch_highest=explode("$#%",$form->ch_highest);
	}
?>
<div class="feedform" style="font-size:1.1em;padding-top:10px;position:relative;">
	<span style="margin-left:20px;">CUSTOMIZE THE COLOUR SCHEME</span>
	<div class="colorbox" style="display: inline-block;">
		<div class="qa"  style="<?php echo 'background-color:'.$form->fc_qm;?>"></div>
		<div class="choices"  style="<?php echo 'background-color:'.$form->fc_ch;?>"></div>
		<div  class="chose-answer"  style="<?php echo 'background-color:'.$form->fc_ca;?>"></div>
		<div class="backgrd"  style="<?php echo 'background-color:'.$form->fc_bg;?>"></div>
	</div>
	<div class="colormap" style="margin-top:20px;border-bottom:3px solid #CEE1E5;">
		<table style="width:100%">
			<tr>
				<td>
					<div class="qa out"><div style="<?php echo 'background-color:'.$form->fc_qm;?>"></div></div>
					<p>FONT COLOUR FOR QUESTIONS AND MESSAGES</p>
				</td>
				<td>
					<div class="choices out"><div style="<?php echo 'background-color:'.$form->fc_ch;?>"></div></div>
					<p>FONT COLOUR FOR CHOICES</p>
				</td>
				<td>
					<div class="chose-answer out"><div style="<?php echo 'background-color:'.$form->fc_ca;?>"></div></div>
					<p>FONT COLOUR FOR CHOSEN ANSWER</p>
				</td>
				<td>
					<div class="backgrd out"><div style="<?php echo 'background-color:'.$form->fc_bg;?>"></div></div>
					<p>COLOUR FOR TOP &nbsp;NAVIGATION</p>
				</td>
			</tr>
		</table>
	</div>
	<form action method="post"  enctype="multipart/form-data" id="form">
		<div class="greet-msg">
			<textarea name="gmsg" id="gmsg" placeholder="TYPE A MESSAGE TO GREET YOUR CUSTOMERS"><?php echo $form->greet_msg;?></textarea>
		</div>
		<div style="position:relative;">
			<div class="arrow prev" style="top:43px;left:10px;"><img src="./images/arrow_left.png" style="height:40px;"></div>
			<div class="paginate" style="float:left;">
				<div class="qanda quest1 current">
					<textarea name="quest[]" class="questionT" placeholder="TYPE YOUR FIRST QUESTION"><?php echo $form->question[0]; ?></textarea>
					<div style="text-align:left; padding:15px 25px;">
						ENTER YOUR CHOICES
						<br>
						<input type="text" class="optionT" placeholder="LOWEST RATING" <?php echo "value='".$form->ch_lowest[0]."'"; ?> name="lestrate[]"> 
						<input type="text" class="optionT" placeholder="LOW RATING" <?php echo "value='".$form->ch_low[0]."'"; ?> name="lrate[]">
						<input type="text" class="optionT" placeholder="AVERAGE RATING" <?php echo "value='".$form->ch_average[0]."'"; ?> name="arate[]">
						<input type="text" class="optionT" placeholder="HIGH RATING" <?php echo "value='".$form->ch_high[0]."'"; ?> name="hrate[]">
						<input type="text" class="optionT" placeholder="HIGHEST RATING" <?php echo "value='".$form->ch_highest[0]."'"; ?> name="hestrate[]">
					</div>
				</div>
				<div class="qanda quest2 hide">
					<textarea name="quest[]" class="questionT" placeholder="TYPE YOUR SECOND QUESTION"><?php echo $form->question[1]; ?></textarea>
					<div style="text-align:left; padding:15px 25px;">
						ENTER YOUR CHOICES
						<br>
						<input type="text" class="optionT" placeholder="LOWEST RATING" <?php echo "value='".$form->ch_lowest[1]."'"; ?> name="lestrate[]"> 
						<input type="text" class="optionT" placeholder="LOW RATING" <?php echo "value='".$form->ch_low[1]."'"; ?> name="lrate[]">
						<input type="text" class="optionT" placeholder="AVERAGE RATING" <?php echo "value='".$form->ch_average[1]."'"; ?> name="arate[]">
						<input type="text" class="optionT" placeholder="HIGH RATING" <?php echo "value='".$form->ch_high[1]."'"; ?> name="hrate[]">
						<input type="text" class="optionT" placeholder="HIGHEST RATING" <?php echo "value='".$form->ch_highest[1]."'"; ?> name="hestrate[]">
					</div>
				</div>
				<div class="qanda quest3 hide">
					<textarea name="quest[]" class="questionT" placeholder="TYPE YOUR THIRD QUESTION"><?php echo $form->question[2]; ?></textarea>
					<div style="text-align:left; padding:15px 25px;">
						ENTER YOUR CHOICES
						<br>
						<input type="text" class="optionT" placeholder="LOWEST RATING" <?php echo "value='".$form->ch_lowest[2]."'"; ?> name="lestrate[]"> 
						<input type="text" class="optionT" placeholder="LOW RATING" <?php echo "value='".$form->ch_low[2]."'"; ?> name="lrate[]">
						<input type="text" class="optionT" placeholder="AVERAGE RATING" <?php echo "value='".$form->ch_average[2]."'"; ?> name="arate[]">
						<input type="text" class="optionT" placeholder="HIGH RATING" <?php echo "value='".$form->ch_high[2]."'"; ?> name="hrate[]">
						<input type="text" class="optionT" placeholder="HIGHEST RATING" <?php echo "value='".$form->ch_highest[2]."'"; ?> name="hestrate[]">
					</div>
				</div>
				<div class="qanda quest4 hide">
					<textarea name="quest[]" class="questionT" placeholder="TYPE YOUR FOURTH QUESTION"><?php echo $form->question[3]; ?></textarea>
					<div style="text-align:left; padding:15px 25px;">
						ENTER YOUR CHOICES
						<br>
						<input type="text" class="optionT" placeholder="LOWEST RATING" <?php echo "value='".$form->ch_lowest[3]."'"; ?> name="lestrate[]"> 
						<input type="text" class="optionT" placeholder="LOW RATING" <?php echo "value='".$form->ch_low[3]."'"; ?> name="lrate[]">
						<input type="text" class="optionT" placeholder="AVERAGE RATING" <?php echo "value='".$form->ch_average[3]."'"; ?> name="arate[]">
						<input type="text" class="optionT" placeholder="HIGH RATING" <?php echo "value='".$form->ch_high[3]."'"; ?> name="hrate[]">
						<input type="text" class="optionT" placeholder="HIGHEST RATING" <?php echo "value='".$form->ch_highest[3]."'"; ?> name="hestrate[]">
					</div>
				</div>
				<div class="qanda quest5 hide">
					<textarea name="quest[]" class="questionT" placeholder="TYPE YOUR FIFTH QUESTION"><?php echo $form->question[4]; ?></textarea>
					<div style="text-align:left; padding:15px 25px;">
						ENTER YOUR CHOICES
						<br>
						<input type="text" class="optionT" placeholder="LOWEST RATING" <?php echo "value='".$form->ch_lowest[4]."'"; ?> name="lestrate[]"> 
						<input type="text" class="optionT" placeholder="LOW RATING" <?php echo "value='".$form->ch_low[4]."'"; ?> name="lrate[]">
						<input type="text" class="optionT" placeholder="AVERAGE RATING" <?php echo "value='".$form->ch_average[4]."'"; ?> name="arate[]">
						<input type="text" class="optionT" placeholder="HIGH RATING" <?php echo "value='".$form->ch_high[4]."'"; ?> name="hrate[]">
						<input type="text" class="optionT" placeholder="HIGHEST RATING" <?php echo "value='".$form->ch_highest[4]."'"; ?> name="hestrate[]">
					</div>
				</div>
			</div>

			<div class="arrow next" style="right:10px;top:43px;"><img src="./images/arrow_right.png" style="height:40px;"></div>
		</div>
		<div class="upload" style="text-align:center;font-weight:bolder;margin:7px 0;">
			UPLOAD <div onclick="showuploadIcon();">ICON</div> | <div onclick="showuploadLogo();">LOGO</div>
			<input style="display:none;" type="file" name="icon" id="iconFile">
			<input style="display:none;" type="file" name="logo" id="logoFile">
		</div>
		<div style="background:#CEE1E5;text-align:center;height:40px;padding-top:5px;">
			<input type="button" class="css3button" value="DISCARD" onclick="window.location.href = './?FeedBackForm&Update';">
			<input type="submit" class="css3button" name="submit" id="preview" value="PREVIEW">
			<input type="submit" class="css3button" name="submit" value="UPDATE">
		</dv>
		<input class="qa" type="hidden" name="qa" value = "<?php echo $form->fc_qm;?>">
		<input class="choices" type="hidden" name="choices" value = "<?php echo $form->fc_ch;?>">
		<input class="chose-answer" type="hidden" name="chose-answer" value ="<?php echo $form->fc_ca;?>">
		<input class="backgrd" type="hidden" name="backgrd" value ="<?php echo $form->fc_bg;?>">
		
	</form>
</div>
<script type="text/javascript">
	function showuploadIcon(){
		$('.feedform .upload #iconFile').click();
		return false;
	}
	function showuploadLogo(){
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
				$('.feedform .questionT, .feedform .optionT, .feedform #gmsg').each(function(){
					if($(this).val().length<=0){ dialog_msg("Please fill all fields.","ERROR");sub=false; return;}
				});
				//alert(sub);
				return sub;
		});
		$(".feedform #form #preview").click(function(e) {
	       
	        	$(".feedform #form").attr("action", "./?Preview");
	     
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
