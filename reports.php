<?php require_once("includes/initialize.php"); ?>
<?php 
		$form = Forms::find_by_user_id($session->user_id);
		if($form == false){$session->message('First create a form.');redirect_to('./?FeedBackForm');}
		$message = "";
		$getquery = "./?Report";

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


		$responses = Response::find_all_by_formid_paginated($form->id,26,0,0,-1,-1,$startTime,$endTime);//26 for all responses
		$report = new Report();
		$totalavg = 0;
		$count=0;
		if($responses){
			$form->question=explode("$#%",trim($form->question));
			$form->ch_lowest=explode("$#%",$form->ch_lowest);
			$form->ch_low=explode("$#%",$form->ch_low);
			$form->ch_average=explode("$#%",$form->ch_average);
			$form->ch_high=explode("$#%",$form->ch_high);
			$form->ch_highest=explode("$#%",$form->ch_highest);
			$resp2darr = array();
			
			foreach($responses as $resp){
				$resp2darr[] = array($resp->response_one,$resp->response_two,$resp->response_three,$resp->response_four,$resp->response_five);
				$count++;
			}
			$totalavg = 0;
			for($i=0;$i<5;$i++){
				//for one question
				$one = 0;$two = 0;$three = 0;$four = 0;$five = 0;
				foreach($resp2darr as $resp){
					if($resp[$i]==1)$one++;
					else if($resp[$i]==2)$two++;
					else if($resp[$i]==3)$three++;
					else if($resp[$i]==4)$four++;
					else if($resp[$i]==5)$five++;
				}
				$report->response_{($i+1)} = round((1*$one + 2*$two + 3*$three + 4*$four + 5*$five)/($one + $two + $three + $four + $five),1);
				$totalavg = $totalavg + $report->response_{($i+1)};
				$report->response_2darr[] = array($one, $two, $three, $four, $five);
			}
			$totalavg = round($totalavg);
			$report->count = $count;
		}
		else{
			if(!empty($message))$message = "Responses are filtered and there"; 
			else $message = "There ";
			$message = $message."are no responses to your feedback form.";
			$message_id = 0;
		}
?>
<div class="reports-filter" style="font-size:1.1em;font-weight:bold;">
	<div class="filter-bar" >
		<div class="datePicker"style="display: inline-block;">
			<form action method="get">
				<input type="hidden" name="Report">
				<input type="button" class="dateBut startbut" value="<?php if(isset($_GET["startDate"]) || isset($_GET["endDate"]))echo $startDate; else echo 'START DATE';?>">
					<input type="text" class="hide" name="startDate" id="startDate">
				<img src="./images/calender.png" width="20" style="vertical-align: middle;cursor:pointer;" onclick="startDateShow();"  > - 
				<input type="button" class="dateBut endbut"value="<?php if(isset($_GET["startDate"]) || isset($_GET["endDate"]))echo $endDate; else echo 'END DATE';?>">
					<input type="text" class="hide" name="endDate" id="endDate" >
				<img src="./images/calender.png" width="20" style="vertical-align: middle;cursor:pointer;"  onclick="endDateShow();">
				<input type="submit" class="css3button" value="FILTER">
				<input type="button" class="css3button" value="ALL"  onclick="window.location.href = './?Report';">
				
			</form>
		</div>
		<div class="floating-button" style="float:right; background-color:#CEE1E5;padding-left: 5px;padding-right: 2px;">
			<a class ="css3button" target="_blank"href="./createxls.php" style="text-decoration:none;">GENERATE XLS</a>
		</div>
	</div>
	<div class="overall-report" style="height: 30px;background-color: #29859a;color: #fff;border-bottom: 5px solid #CEE1E5;">
			
			<p style="display:inline-block;">

				<?php if($responses){?>NO. OF RESPONDENTS : 
						<span class="feedback-count" style="padding:3px 7px;vertical-align:text-bottom;margin-left:5px;">
							<?php echo $count;?>
						</span>
				<?php }
					else echo "NO RESPONDENTS";
				?>
			</p>
			<p style="display:inline-block;float:right">

				<?php if($responses){?>AVERAGE TOTAL SCORE : 
						<span class="feedback-count" style="padding:3px 7px;vertical-align:text-bottom;margin-left:5px;">
							<?php echo $totalavg;?>
						</span>
				<?php }
					else echo "NO RESPONSES";
				?>
			</p>
		
	</div>
	<div class="response-container" style="font-weight:normal;">

		<?php if($responses)for($i=0;$i<5;$i++):?>

		<div style="padding:10px; border-bottom:2px solid #CEE1E5;">
			<?php echo "Q".($i+1)." ".$form->question[$i]; ?><br>
			<table class="bargraph">
				<tr onClick="viewOption(<?php echo ($i+1); ?>,'A');" style="cursor:pointer;"><td><?php echo $form->ch_lowest[$i]; ?></td><td><div style="width:<?php echo ($report->response_2darr[$i][0]/$report->count)*100 + 10; ?>%;"><?php printf("%.1f",($report->response_2darr[$i][0]/$report->count)*100); ?>%</div></td><td><?php echo $report->response_2darr[$i][0]; ?></td></tr>
				<tr onClick="viewOption(<?php echo ($i+1); ?>,'B');" style="cursor:pointer;"><td><?php echo $form->ch_low[$i]; ?></td><td><div style="width:<?php echo ($report->response_2darr[$i][1]/$report->count)*100 + 10; ?>%;"><?php printf("%.1f",($report->response_2darr[$i][1]/$report->count)*100); ?>%</div></td><td><?php echo $report->response_2darr[$i][1]; ?></td></tr>
				<tr onClick="viewOption(<?php echo ($i+1); ?>,'C');" style="cursor:pointer;"><td><?php echo $form->ch_average[$i]; ?></td><td><div style="width:<?php echo ($report->response_2darr[$i][2]/$report->count)*100 + 10; ?>%;"><?php printf("%.1f",($report->response_2darr[$i][2]/$report->count)*100); ?>%</div></td><td><?php echo $report->response_2darr[$i][2]; ?></td></tr>
				<tr onClick="viewOption(<?php echo ($i+1); ?>,'D');" style="cursor:pointer;"><td><?php echo $form->ch_high[$i]; ?></td><td><div style="width:<?php echo ($report->response_2darr[$i][3]/$report->count)*100 + 10; ?>%;"><?php printf("%.1f",($report->response_2darr[$i][3]/$report->count)*100); ?>%</div></td><td><?php echo $report->response_2darr[$i][3]; ?></td></tr>
				<tr onClick="viewOption(<?php echo ($i+1); ?>,'E');" style="cursor:pointer;"><td><?php echo $form->ch_highest[$i]; ?></td><td><div style="width:<?php echo ($report->response_2darr[$i][4]/$report->count)*100 + 10; ?>%;"><?php printf("%.1f",($report->response_2darr[$i][4]/$report->count)*100); ?>%</div></td><td><?php echo $report->response_2darr[$i][4]; ?></td></tr>
				<!--<li style="width:100%;">S</li>-->
			</table>
			<div class="feedback-count" style=""><?php echo $report->response_{($i+1)}; ?></div>
		</div>

	<?php endfor;?>

	</div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function(){
		$('body').css('background-color','#CEE1E5');
		$("#startDate").datepicker({
				maxDate:"0", 
              	altField: ".startbut",
              	dateFormat: "dd-mm-yy"
        });
        $("#endDate").datepicker({ 
				maxDate:"0", 
              	altField: ".endbut",
              	dateFormat: "dd-mm-yy"
        });
	});
	function startDateShow(){
		$( "#startDate" ).datepicker( "show" );
	}
	function endDateShow(){
		$( "#endDate" ).datepicker( "show" );
	}
	function viewOption(quest, option){
		window.location.href = './?Report=' + quest + option;
	}
</script>