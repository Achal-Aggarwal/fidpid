<?php require_once("includes/initialize.php"); ?>
<?php 
		$form = Forms::find_by_user_id($session->user_id);
		if($form == false){$session->message('First create a form.');redirect_to('./?FeedBackForm');}
		$message = "";
		$getquery = "./?Notification";

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

		$temp_form = $form;
		if (isset($_POST['trigger'])) { // Form has been submitted.
				if( isset($_POST['activated']) && count($_POST['activated'])>0){
					if(in_array("less",$_POST['activated']))$form->less_trigger=trim($_POST['trigger'][0]);
					else $form->less_trigger="-1";
					if(in_array("equal",$_POST['activated']))$form->equal_trigger=trim($_POST['trigger'][1]);
					else $form->equal_trigger="-1";
					if(in_array("greater",$_POST['activated']))$form->greater_trigger=trim($_POST['trigger'][2]);
					else $form->greater_trigger="26";
				}
				else{
					$form->less_trigger="-1";
					$form->equal_trigger="-1";
					$form->greater_trigger="0";
				}
			if($form->update() || $temp_form==$form){
				$message = "Trigger is Set";
			}
			else $message = $message." "."Trigger Set was unsuccessful";
		}

		//$responses = Response::find_all_by_formid($form->id,$form->less_trigger,$form->equal_trigger,$form->greater_trigger);

		// 1. the current page number ($current_page)
		$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
		$page = $page<1?1:$page;
		// 2. records per page ($per_page)
		$per_page = 5;

		// 3. total record count ($total_count)
		$total_count = Response::count_all_by_formid($form->id,$form->less_trigger,$form->equal_trigger,$form->greater_trigger,$startTime, $endTime);//26 for all responses

		$pagination = new Pagination($page, $per_page, $total_count);

		$responses = Response::find_all_by_formid_paginated($form->id,$form->less_trigger,$form->equal_trigger,$form->greater_trigger,$per_page,$pagination->offset(),$startTime,$endTime);//26 for all responses
		if($total_count ==0 )$message = $message." "."There are not any notifications.";
		$t = "fed in score ";
?>

	<div class="container" style="position:relative;">
		<div class="notifications" style="font-size:1.1em;font-weight:bold;">
			<div class="filter-bar">
				<div class="datePicker"style="display: inline-block;">

					<form action method="get">
						<input type="hidden" name="Notification">
						<input type="button" class="dateBut startbut" value="<?php if(isset($_GET["startDate"]) || isset($_GET["endDate"]))echo $startDate; else echo 'START DATE';?>">
							<input type="text" class="hide" name="startDate" id="startDate">
						<img src="./images/calender.png" width="20" style="vertical-align: middle;cursor:pointer;" onclick="startDateShow();"  > - 
						<input type="button" class="dateBut endbut"value="<?php if(isset($_GET["startDate"]) || isset($_GET["endDate"]))echo $endDate; else echo 'END DATE';?>">
							<input type="text" class="hide" name="endDate" id="endDate" >
						<img src="./images/calender.png" width="20" style="vertical-align: middle;cursor:pointer;"  onclick="endDateShow();">
						<input type="submit" class="css3button" value="FILTER">
						<input type="button" class="css3button" value="ALL"  onclick="window.location.href = './?Notification';">
						
					</form>

				</div>
				<div class="datePicker"style="float:right; background-color:#CEE1E5;height: 38px;padding-left: 5px;">
					<input type="button" class="css3button setTri"  value="TRIGGER">
				</div>
			</div>
			<table style="width: 100%;font-weight: normal;">
				<?php foreach($responses as $resp){?>
					<tr style="cursor:pointer;" id="resp<?php echo $resp->id; ?>" class="<?php if($resp->notified==0)echo "new";?>"><td><?php echo $resp->name; ?> <?php echo $t." ".$resp->total_avg; ?> (Click to view feedback)</td><td title="<?php echo strftime("%B %d, %Y at %I:%M %p", $resp->time);?>"><?php echo nicetime($resp->time);?></td></tr>
				<?php //update feedbacks(response) table to make all viewed
					if($resp->notified!="1"){
						$resp->notified="1";
						$resp->update();
					}
				}?>
			</table>
			<?php if($pagination->total_pages()>1){?>
				<div style="text-align:center;padding:5px;border-top: 5px solid #CEE1E5;border-bottom: 5px solid #CEE1E5;">
						<a style="margin:0 4px;"  href="<?php if($page!=1){
							echo $getquery.'&page=1';
						} else echo '';?>">
							<img src="./images/newest.png" style="width:27px;vertical-align: 3px;">
						</a>
						<a style="margin:0 4px;" href="<?php if($pagination->has_previous_page()){
							echo $getquery.'&page='.$pagination->previous_page();
						} else echo '';?>">
							<img src="./images/newer.png" style="width:33px;">
						</a>
						<span style="vertical-align:10px;margin:0 4px;"><?php echo number_format(1+($page-1)*$pagination->per_page)."-".number_format($page*$pagination->per_page>$pagination->total_count?$pagination->total_count:$page*$pagination->per_page)." of ".number_format($pagination->total_count);?></span>
						<a style="margin:0 4px;" href="<?php if($pagination->has_next_page()){
							echo $getquery.'&page='.$pagination->next_page();
						} else echo '';?>">
							<img src="./images/older.png" style="width:33px;">
						</a>
						<a style="margin:0 4px;" href="<?php if($page!= $pagination->total_pages()){
							echo $getquery.'&page='.$pagination->total_pages();
						} else echo '';?>">
							<img src="./images/oldest.png" style="width:27px;vertical-align: 3px;">
						</a>
				</div>
			<?php }?>
		</div>
		
			<div id="trigger-dialog" title="SET TRIGGER">
				<form action method="post" id="trform">
					<div class="msg" style="font-size:0.85em;">NOTIFY IF TOTAL IS 
						<div class="btn" style="width:110px;height:15px;">LESS THAN</div>
						<input style="width:15px;" class="aTrigger less"  name="trigger[]" value="<?php if($form->less_trigger!=-1)echo $form->less_trigger;else echo "0";?>" type="text">
						<input type="checkbox" name="activated[]" <?php if($form->less_trigger!=-1)echo "checked";?> value="less">
						<span style="float:right;font-size:0.8em;margin-right:38px;">MIN:2</span>
					</div>
					<div class="msg" style="font-size:0.85em;margin:15px 0;">NOTIFY IF TOTAL IS 
						<div class="btn" style="width:110px;height:15px;">EQUAL TO</div>
						<input style="width:15px;" class="aTrigger equal"  name="trigger[]" value="<?php if($form->equal_trigger!=-1)echo $form->equal_trigger;else echo "0";?>" type="text">
						<input type="checkbox" name="activated[]" <?php if($form->equal_trigger!=-1)echo "checked";?> value="equal">
						<span style="float:right;font-size:0.8em;margin-right:38px;">MAX:25</span>
					</div>
					<div class="msg" style="font-size:0.85em;">NOTIFY IF TOTAL IS 
						<div class="btn" style="width:110px;height:15px;">GREATER THAN</div>
						<input style="width:15px;" class="aTrigger greater" name="trigger[]" value="<?php if($form->greater_trigger!=26 && $form->greater_trigger!=0)echo $form->greater_trigger;else echo "0";?>" type="text">
						<input type="checkbox" name="activated[]" <?php if($form->greater_trigger!=26 && $form->greater_trigger!=0)echo "checked";?> value="greater">
						
						<span style="float:right;font-size:0.8em;margin-right:38px;">MAX:24</span>
					</div>
				</form>
			</div>

					
		
	</div>

<script type="text/javascript">
/*function maximize_bg(){
		var v = $(window).height();
		v=v+'px';
	     $('.container').css('height',v);
	}
	$(window).resize(function() {
		maximize_bg();
	});*/
	jQuery(document).ready(function(){
		//maximize_bg();
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
        $('.notifications table tr').click(function(){
        	window.location.href = './?Response=' + $(this).attr("id").substring(4) + '<?php echo '&'.	substr($getquery, 3)?>';
        });
	});
	function startDateShow(){
		$( "#startDate" ).datepicker( "show" );
	}
	function endDateShow(){
		$( "#endDate" ).datepicker( "show" );
	}
	$(function() {
	    var dd = $( ".container #trigger-dialog" ).dialog({
		    		draggable: false,
		    		resizable: false,
		    		modal: true,
		    		width:350,
		    		buttons: [ { text: "SET", click: function() { 
		    			var submitForm=true;
		    			var msg;
		    			var numberfilter =/(^\d+$)|(^\d+\.\d+$)/;
						$("#trform .aTrigger").each(function(){
							if($(this).next().attr('checked') ){
								var v = $(this).val();
								if(v == "" || !numberfilter.test(v)){msg="Please enter a number only";submitForm=false;return false;}
								if($(this).hasClass("less") && (v<2  || v>25)){msg="Less trigger must be in range 2 to 25";submitForm=false;return false;}
								if($(this).hasClass("equal") && (v<1  || v>26)){msg="Equal trigger must be in range 1 to 25";submitForm=false;return false;}
								if($(this).hasClass("greater") && (v<1 || v>25)){msg="Greater trigger must be in range 1 to 24";submitForm=false;return false;}
							}
						});
						
		    			if(submitForm)$("#trform").submit();
		    			else dialog_msg(msg,"ERROR"); 
		    		} },
		    					{ text: "CLOSE", click: function() { $( this ).dialog( "close" ); }
		    				 } ],
		    		create: function( event, ui ) {
		    			$(event.target).parent().css("position", "fixed");
		    			$(this).closest(".ui-dialog").find(".ui-dialog-titlebar-close").hide();
			            $buttonPane = $(this).next();
			            $buttonPane.find("button").addClass("css3button");       
		    		},
		    		autoOpen: false,
		    		hide: {effect: "fadeOut", duration: 1000},
		    		show: {effect: "fadeIn", duration: 500},
		    		dialogClass: "modal-dialog-withtitle"
        	});
		    $('.setTri').click(function(){
		    	dd.dialog( "open" );
		    });
	  });
</script>
