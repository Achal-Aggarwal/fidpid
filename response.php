<?php require_once("includes/initialize.php"); ?>
<?php
	if($session->is_logged_in()==false) {
	  redirect_to("index.php");
	}
	if(!isset($_GET['Response']) && empty($_GET['Response']))redirect_to('./?Report');

	// Remember to give your form's submit tag a name="submit" attribute!
	$form = Forms::find_by_user_id($session->user_id);
	if($form == false){$session->message('First create a form.');redirect_to('./?FeedBackForm');}
	
	

	if(isset($_GET["FeedBack"])){
		$parent_menu = "FeedBack";
		$less_trigger = 26;
		$equal_trigger = 0;
		$greater_trigger = 0;
	}
	else if(isset($_GET["Notification"])){
		$parent_menu = "Notification";
		$less_trigger = $form->less_trigger;
		$equal_trigger = $form->equal_trigger;
		$greater_trigger = $form->greater_trigger;
	}
	else if(isset($_GET["Report"])){
		$parent_menu = "Report";
		$quest_option = $_GET["Report"];
		$quest = substr($quest_option,0,1);
		$option = substr($quest_option,1,1);
		$opt = $option;
		$option = ord($option)-ord("A")+1;
	}
	else{
		$parent_menu = "";
		$less_trigger = 26;
		$equal_trigger = 0;
		$greater_trigger = 0;
	}

	if( $parent_menu == ""){
		$getquery="";
		$startTime = 0;
		$endTime = 0;
	}
	else
	{
		$getquery = "&".$parent_menu;
		if(isset($_GET["Report"]))$getquery.="=".$_GET["Report"];
		if(isset($_GET["startDate"]) && !empty($_GET["startDate"]) && ($startTime = strtotime($_GET["startDate"]))){
				$startDate = $_GET["startDate"];
				$getquery .= "&startDate=".$startDate;
		}else{
			$startTime = 0;
			$startDate = "Beg. Of Time";
		}

		if(isset($_GET["endDate"]) && !empty($_GET["endDate"]) && ($endTime = strtotime($_GET["endDate"]))){
				$endDate = $_GET["endDate"];
				$getquery .= "&endDate=".$endDate;
		}else{
			$endTime = time('now');
			$endDate = "Today";
		}
	}


	if($parent_menu == "Report"){
		$total_count = Response::count_all_by_quest_option($form->id,$quest,$option,$startTime, $endTime);
		$responses = Response::find_all_by_quest_option_paginated($form->id,$quest,$option,$total_count,0,$startTime,$endTime);
	}
	else{
		$total_count = Response::count_all_by_formid($form->id,$less_trigger,$equal_trigger,$greater_trigger,$startTime, $endTime);//26 for all responses
		$responses = Response::find_all_by_formid_paginated($form->id,$less_trigger,$equal_trigger,$greater_trigger,$total_count,0,$startTime, $endTime);
	}
	if($responses == false){
		$message="No responses.";
	}
	else{
	$doBreak = false;
	$nextresp_id = $prevresp_id = -1;
	$resp = false;
	$firstresp_id = $responses[0]->id;
	$lastresp_id = $responses[count($responses)-1]->id;
	$curreresp_id = $_GET['Response'];
	$currentPage = 0;
	foreach ($responses as $respp) {

		if($doBreak == true){$nextresp_id = $respp->id;break;}
		if($respp->id == $curreresp_id){
			$doBreak = true;
			$resp= $respp;
			$currentPage++;
			
			continue;
		}
		$currentPage++;
		$prevresp_id = $respp->id;
	}
	$resp = Response::find_by_id($_GET['Response'],$form->id);
	if($resp==false){
		redirect_to('./?Report');
	}
	if($resp->viewed!="1"){
		$resp->viewed="1";
		if($resp->notified!="1"){
			$resp->notified="1";
		}
		$resp->update();
	}

	$form->question=explode("$#%",$form->question);
	$form->ch_lowest=explode("$#%",$form->ch_lowest);
	$form->ch_low=explode("$#%",$form->ch_low);
	$form->ch_average=explode("$#%",$form->ch_average);
	$form->ch_high=explode("$#%",$form->ch_high);
	$form->ch_highest=explode("$#%",$form->ch_highest);

	$answers = array($resp->response_one,$resp->response_two,$resp->response_three,$resp->response_four,$resp->response_five);
?>
<div class="page-content feedback-form-view" 
		style="text-align:center;background:url(images/background.jpg);
		background-size:cover;">
		<div style="height:50px; background:<?php echo $form->fc_bg; ?>; text-align:left;">
			<img src="./<?php echo $form->logo; ?>" style="padding:5px 15px;height:70%;">
		</div>
		
			<div style="width:85%; margin:0 auto;margin-top:15px;">
				<span style="font-size:1.4em;color:<?php echo $form->fc_bg; ?>;"><?php echo $form->greet_msg; ?></span>
				<br><br>
				<div style="text-align:left;font-weight:bolder;padding-left:20px;">
					Bill No : <?php echo $resp->bill_no;?><br>
					Name : <?php echo $resp->name;?><br>
					Email ID: <?php echo $resp->email;?>
				</div>
				<br>
				<table class="questions" style="margin:10px;width:100%;font-weight: bolder;">

					<?php $i=0;for($i=0;$i<5;$i++):?>

					<tr style=""><td><span style="color:<?php echo $form->fc_qm; ?>;"><?php echo ($i+1).'. '.$form->question[$i]; ?></span></td></tr>
					<tr class="option-container ">
						<td class="quest<?php echo ($i+1); ?> " style="color:<?php echo $form->fc_ch; ?>;">
							<div class="option" style="text-align:left;"><div><?php echo $form->ch_lowest[$i]; ?></div></div>
							<div class="option" style="margin-right:2%;"><div><?php echo $form->ch_low[$i]; ?></div></div>
							<div class="option" style=""><div><?php echo $form->ch_average[$i]; ?></div></div>
							<div class="option" style="margin-left:3%;"><div><?php echo $form->ch_high[$i]; ?></div></div>
							<div class="option" style="text-align:right;"><div><?php echo $form->ch_highest[$i]; ?></div></div>
						</td>
					</tr>
					<tr class="slider-container"><td ><div class="slider quest<?php echo ($i+1); ?> " >
						<span class="hide"><?php echo $answers[$i]?></span></div></td></tr>
					<?php endfor;?>
				</table>
				<div style="text-align:left;min-height:50px;font-weight:bolder;padding-left:20px;">
					Comments : <br><?php if($resp->comment)echo $resp->comment;else echo "No comments...";?>
				</div>
	</div>
				<?php if($total_count>1){?>
					<div style="text-align:center;padding:5px;border-top: 5px solid #CEE1E5;border-bottom: 5px solid #CEE1E5;">
							<a style="margin:0 4px;"  href="<?php if($firstresp_id!=-1 && $firstresp_id!=$curreresp_id){
								echo './?Response='.$firstresp_id.$getquery;
							} else echo '';?>">
								<img src="./images/newest.png" style="width:27px;vertical-align: 3px;">
							</a>
							<a style="margin:0 4px;" href="<?php if($prevresp_id!=-1 && $prevresp_id!=$curreresp_id){
								echo './?Response='.$prevresp_id.$getquery;
							} else echo '';?>">
								<img src="./images/newer.png" style="width:33px;">
							</a>
							<span style="vertical-align:10px;margin:0 4px;"><?php echo number_format($currentPage)." of ".number_format($total_count);?></span>
							<a style="margin:0 4px;" href="<?php if($nextresp_id!=-1 && $nextresp_id!=$curreresp_id){
								echo './?Response='.$nextresp_id.$getquery;
							} else echo '';?>">
								<img src="./images/older.png" style="width:33px;">
							</a>
							<a style="margin:0 4px;" href="<?php if($lastresp_id!=-1 && $lastresp_id!=$curreresp_id){
								echo './?Response='.$lastresp_id.$getquery;
							} else echo '';?>">
								<img src="./images/oldest.png" style="width:27px;vertical-align: 3px;">
							</a>
					</div>
				<?php }?>
			</div>
		

<script type="text/javascript">
	
	jQuery(document).ready(function(){
		
		$( ".feedback-form-view .slider-container .slider" ).each(function(){
			var ans = $(this).find('span').html();
			var s = $(this).slider({
				max: 5,
				min: 1,
				value: ans,
				stop: function( event, ui ) {
					$(this).slider( "option", "value", ans );
				},
				create: function( event, ui ) {
					var clas = $(this).attr('class').split(/\s+/)[1];
					var op = $( ".feedback-form-view table tr td").filter("."+clas);
					op.children().eq(ans-1).css('color','<?php echo $form->fc_ca; ?>');
				}
			});
			
		});
		$( ".feedback-form-view .slider-container .slider a" ).css('background',"url('./<?php echo str_replace('\\','/',$form->icon); ?>')");
		$( ".feedback-form-view .slider-container .slider a" ).css('background-size',"100% 100%");
	});
</script>
<?php }?>