<?php require_once("includes/initialize.php"); ?>
<?php
	if($session->is_logged_in()==false) {
	  redirect_to("index.php");
	}
	if(!isset($_GET['Preview']) && !isset($_GET['FormView']))redirect_to('./?FeedBackForm');
	// Remember to give your form's submit tag a name="submit" attribute!
	$form = Forms::find_by_user_id($session->user_id);
	if($form == false && isset($_GET['FormView'])){$session->message('First create a form.');redirect_to('./?FeedBackForm');}
	if(isset($_GET['Preview']) && !isset($_POST['submit']))redirect_to('./?FeedBackForm');
	if($form == false)$form = new Forms();
	$temp_form = $form;
	if (isset($_POST['submit'])) { // Form has been submitted.
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
	$temp_form = Forms::find_by_user_id($session->user_id);
	if($temp_form!=$form)$form_edit = true; else $form_edit = false;
	$form->question=explode("$#%",$form->question);
	$form->ch_lowest=explode("$#%",$form->ch_lowest);
	$form->ch_low=explode("$#%",$form->ch_low);
	$form->ch_average=explode("$#%",$form->ch_average);
	$form->ch_high=explode("$#%",$form->ch_high);
	$form->ch_highest=explode("$#%",$form->ch_highest);
	$_SESSION["form"] = $form;
?>
<div class="page-content feedback-form-view" 
		style="text-align:center;
		background-size:cover;">
		<div style="height:50px; background:<?php echo $form->fc_bg; ?>; text-align:left;">
			<img src="<?php echo"./".str_replace("\\", "/", $form->logo); ?>" style="padding:5px 15px;height:70%;">
		</div>
		<form action="./?FeedBackForm&Update" method="post">
			<div style="width:85%; margin:0 auto;margin-top:15px;">
				<span style="font-size:1.4em;color:<?php echo $form->fc_bg; ?>;"><?php echo $form->greet_msg; ?></span>
				<br><input type="hidden" name="form_id" value="<?php echo $form->id; ?>"><br><input type="hidden" name="fghfh" value="yeertgdfs">
				<input type="text" disabled style="margin-right:20px;" placeholder="Bill No">
				<input type="text" disabled style="margin-right:20px;" placeholder="Name">
				<input type="text" disabled placeholder="Email ID"><br>
				<table class="questions" style="margin:10px;width:100%;font-weight: bolder;">

					<?php $i=0;for($i=0;$i<5;$i++):?>

					<tr style=""><td><span style="color:<?php echo $form->fc_qm; ?>;"><?php echo ($i+1).'. '.$form->question[$i]; ?></span></td></tr>
					<tr class="option-container ">
						<td class="quest<?php echo ($i+1); ?> " style="color:<?php echo $form->fc_ch; ?>;">
							<div class="option" style="text-align:left;"><div><?php echo $form->ch_lowest[$i]; ?></div></div>
							<div class="option" style="margin-right:2%;"><div><?php echo $form->ch_low[$i]; ?></div></div>
							<div class="option"  style="color:<?php echo $form->fc_ca; ?>"><div><?php echo $form->ch_average[$i]; ?></div></div>
							<div class="option" style="margin-left:3%;"><div><?php echo $form->ch_high[$i]; ?></div></div>
							<div class="option" style="text-align:right;"><div><?php echo $form->ch_highest[$i]; ?></div></div>
							<input type="hidden" class="quest<?php echo ($i+1); ?>" value="3">
						</td>
					</tr>
					<tr class="slider-container"><td ><div class="slider  quest<?php echo ($i+1); ?> " ></div></td></tr>
					<?php endfor;?>
				</table>
				<textarea placeholder="Comments..." disabled></textarea>
				<br>
				<div style="padding-bottom:10px;">
					<?php if(isset($_GET['FormView'])){ ?>
					<input type="button" value="EDIT" class="css3button"  onclick="window.location.href = './?FeedBackForm&Update';">
					<input type="button" value="LINK" class="css3button showLink">
					<?php }
					else {?>
					<input type="button" value="DISCARD" class="css3button" onclick="window.location.href = './?Home';">
					<input type="submit" value="EDIT" name="submit" class="css3button">
					<?php if($form_edit){?><input type="submit" value="UPDATE" name="submit" class="css3button"><?php }?>
					
					<?php }?>
				</div>
			</div>
		</form>
		<div id="link-dialog" title="Distribuatable Link">
		  <p></p>
		</div>
</div>
<script type="text/javascript">
	
	jQuery(document).ready(function(){
		$( ".feedback-form-view .slider-container .slider" ).each(function(){
			
			var s = $(this).slider({
				max: 5,
				min: 1,
				value: 3,
				change: function( event, ui ) {
					var clas = $(this).attr('class').split(/\s+/)[1];
					var op = $( ".feedback-form-view table tr td").filter("."+clas);
					var value = $(this).slider( "value" );
					op.children().each(function(){
						$(this).css('color','<?php echo $form->fc_ch; ?>');
					});
					op.children().eq(value-1).css('color','<?php echo $form->fc_ca; ?>');
					op.children().eq(5).attr('value',value);
				}
			});
			
		});
		$( ".feedback-form-view .slider-container .slider a" ).css('background',"url('./<?php echo str_replace('\\','/',$form->icon); ?>')");
		$( ".feedback-form-view .slider-container .slider a" ).css('background-size',"100% 100%");
	});
	$(function() {
	    var dd=$( "#link-dialog" ).dialog({
		    		draggable: false,
		    		resizable: false,
		    		autoOpen: false,
		    		width:315,
		    		modal: true,
			    		buttons: [ { text: "GENERATE", click: function() { 
			    			var cc = $('#link-dialog #count');
			    			if(!isNaN(cc.val()) && cc.val().length>0)generate(cc.val());
			    			else {dialog_msg("Please enter a number.","ERROR");cc.val('');}
			    		} } ,
			    		{ text: "CLOSE", click: function() {
			    			$( this ).dialog( "close" ); 
			    		} }],
			    		create: function( event, ui ) {
			    			$(event.target).parent().css("position", "fixed");

			    		},
			    	hide: {effect: "fadeOut", duration: 500},
		    		show: {effect: "fadeIn", duration: 500},
		    		dialogClass: "modal-dialog-withtitle",
			    	open: function() {
			    		$(this).closest(".ui-dialog").find(".ui-dialog-titlebar-close").hide();
			    		$('#link-dialog p').html('Number of accesses <br><input type="text" id="count">');
			            $buttonPane = $(this).next();
			            $buttonPane.find("button:first").css("display",'inline');
			            $buttonPane.find("button:first").css("width",'90px');
			            $buttonPane.find("button").addClass("css3button");      
	        		}
	        	});
	    	$('.showLink').click(function(){
	    		dd.dialog( "open" );
	    	});
	     	function generate(count){
	     		
	     		var request = $.ajax({
				  url: "dlink_gen.php",
				  type: "POST",
				  data: {c : count},
				  dataType: "html"
				});
				 
				request.done(function(msg) {
					$(".modal-dialog-withtitle .ui-dialog-buttonset button:first" ).css("display",'none');
				  	$('#link-dialog p').html("www.fidpid.com"+msg +" (with "+ count +" accesses)");
				});
				 
				request.fail(function(jqXHR, textStatus) {
					dialog_msg("Link generation is failed due to some reason. Please click again.","ERROR");
				  
				});
		    }
	  });
</script>
