<?php require_once("includes/initialize.php"); ?>
<?php
	if($session->is_logged_in()==true){
		$session->message("You can not submit feedback.");
	 	redirect_to("./?FeedBackForm");
	 }
	 $hash = $_GET["View"];
	 $link = Dlink::find_by_hash($hash);
	 if($link == false){
		$message="Invalid form link.";
		$message_id = 1;
		}
	else{

			$form = Forms::find_by_user_id($link->user_id);
			
			//$form = Forms::find_by_user_id($session->user_id);
			if (isset($_POST['submit'])) { // Form has been submitted.

				
				$total = $_POST['response'][0]+$_POST['response'][1]+$_POST['response'][2]+$_POST['response'][3]+$_POST['response'][4];
				$response = Response::make(trim($_POST['bill']),$_POST['form_id'],trim($_POST['name']),trim($_POST['email']),$_POST['response'][0],
					$_POST['response'][1], $_POST['response'][2], $_POST['response'][3],
					$_POST['response'][4], $total ,$_POST['comment']);
				if($response->create()){
					//First delete the link and then submit form
					$link->count--;
					if($link->count!=0)
						$link->update();
					else $link->delete();

					$message="Response submitted successfully. <br><a href='./?View=".$_GET["View"]."'>Clcik here to submit new response.</a>";
					$user = User::find_by_id($form->user_id);
					if($total<$form->less_trigger || $total=$form->equal_trigger || $total>$orm->greater_trigger){
							sendMailTo($user->email,$response->name." (".$response->email.") fed in score ".$total ,"Triggered FeedBack");
					}
					
				}
				else{
					$message="Response submittion was unsuccessfully.";
				}
				$message_id=2;// A message but modal dialog box is shown

			}
			else{
			$form->question=explode("$#%",trim($form->question));
			$form->ch_lowest=explode("$#%",trim($form->ch_lowest));
			$form->ch_low=explode("$#%",trim($form->ch_low));
			$form->ch_average=explode("$#%",trim($form->ch_average));
			$form->ch_high=explode("$#%",trim($form->ch_high));
			$form->ch_highest=explode("$#%",trim($form->ch_highest));
		?>
<div class="page-content feedback-form-view" 
		style="text-align:center;
		background-size:cover;">
		<div style="height:50px; background:<?php echo $form->fc_bg; ?>; text-align:left;">
			<img src="<?php echo ".".DS.$form->logo; ?>" style="padding:5px 15px;height:70%;">
		</div>
		<form action method="post" id="responseForm">
			<div style="width:85%; margin:0 auto;margin-top:15px;">
				<span style="font-size:1.4em;color:<?php echo $form->fc_bg; ?>;"><?php echo $form->greet_msg; ?></span>
				<br><input type="hidden" name="form_id" value="<?php echo $form->id; ?>"><br>
				<input type="text" style="margin-right:20px;" placeholder="Bill No" readonly id="billno" name="bill">
				<input type="text" style="margin-right:20px;" placeholder="Name" name="name">
				<input type="text" placeholder="Email ID" name="email" id="email"><br>
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
							<input type="hidden" name="response[]" class="quest<?php echo ($i+1); ?>" value="3">
						</td>
					</tr>
					<tr class="slider-container"><td ><div class="slider  quest<?php echo ($i+1); ?> " ></div></td></tr>

					<?php endfor;?>

				</table>
				<textarea placeholder="Comments..." name="comment"></textarea>
				<br>
				<div style="padding-bottom:10px;">
					<input type="submit" value="DISCARD" class="css3button">
					<input type="submit" value="SUBMIT" name="submit" class="css3button">
				</div>
			</div>
		</form>
		<div id="bill-dialog" title="Requires Bill Number">
		  <p>Enter Bill No <input type="text" id="billinput" placeholder="Bill No"></p>
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
		$( ".feedback-form-view .slider-container .slider a" ).css('background',"url('<?php echo ".".DS.$form->icon; ?>')");
		$( ".feedback-form-view .slider-container .slider a" ).css('background-size',"100% 100%");
		$('.feedback-form-view  #responseForm').submit(function(){
			var dosubmit=true;
			$('.feedback-form-view  #responseForm').find("input").each(function(){
				if($(this).val().length == 0){dialog_msg("Please fill all fields.");dosubmit=false;return false;}
			});
			var email = $('.feedback-form-view  #responseForm #email').val();
			var filter=/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
			if(email.length!=0 && !filter.test(email) && dosubmit){dialog_msg("Invalid email address entered."); dosubmit =  false;}
			return dosubmit;
		});
	});
</script>
<script type="text/javascript">
	
	$(function() {
		$( "#bill-dialog" ).dialog({
    		draggable: false,
    		resizable: false,
    		modal: true,
	    		buttons: [ { text: "OKAY", click: function() { 
	    			var f = $('#bill-dialog #billinput').attr('value');
	    			if(f.length>0){
	    				$('#responseForm #billno').attr('value',f);
	    				$( this ).dialog( "close" ); 
	    			}
	    	} } ],
	    	hide: {effect: "fadeOut", duration: 1000},
    		show: {effect: "fadeIn", duration: 500},
    		dialogClass: "modal-dialog-withtitle",
	    	open: function() {
	    		$(this).closest(".ui-dialog").find(".ui-dialog-titlebar-close").hide();
	            $buttonPane = $(this).next();
	            $buttonPane.find("button:first").addClass("css3button");                        
    		}
    	});
	  });
</script>
<?php }
}
?>