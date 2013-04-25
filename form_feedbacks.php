<?php require_once("includes/initialize.php"); ?>
<?php 
		$form = Forms::find_by_user_id($session->user_id);
		if($form == false){$session->message('First create a form.');redirect_to('./?FeedBackForm');}

		$message = "";
		$getquery = "./?FeedBack";

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

		// 1. the current page number ($current_page)
			$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
			$page = $page<1?1:$page;


		// 2. records per page ($per_page)
		$per_page = 5;

		// 3. total record count ($total_count)
		$total_count = Response::count_all_by_formid($form->id,26,0,0,$startTime, $endTime);//26 for all responses

		$pagination = new Pagination($page, $per_page, $total_count);


		if($page<1 || ($page>$pagination->total_pages() && $pagination->total_pages()!=0)){redirect_to("./?FeedBack");}

		$responses = Response::find_all_by_formid_paginated($form->id,26,0,0,$per_page,$pagination->offset(),$startTime,$endTime);//26 for all responses

		if($total_count ==0 )$message = $message." "."There are no feedbacks.";
		$message_id = 0;
?>
<div class="form-feedback" style="font-size:1.1em;font-weight:bold;">
	<div class="filter-bar">
		<div class="datePicker"style="display: inline-block;">
			<form action method="get">
				<input type="hidden" name="FeedBack">
				<input type="button" class="dateBut startbut" value="<?php if(isset($_GET["startDate"]) || isset($_GET["endDate"]))echo $startDate; else echo 'START DATE';?>">
					<input type="text" class="hide" name="startDate" id="startDate">
				<img src="./images/calender.png" width="20" style="vertical-align: middle;cursor:pointer;" onclick="startDateShow();"  > - 
				<input type="button" class="dateBut endbut"value="<?php if(isset($_GET["startDate"]) || isset($_GET["endDate"]))echo $endDate; else echo 'END DATE';?>">
					<input type="text" class="hide" name="endDate" id="endDate" >
				<img src="./images/calender.png" width="20" style="vertical-align: middle;cursor:pointer;"  onclick="endDateShow();">
				<input type="submit" class="css3button" value="FILTER">
				<input type="button" class="css3button" value="ALL"  onclick="window.location.href = './?FeedBack';">
				
			</form>

		</div>
		
		
	</div>
	<table style="width: 100%;font-weight: normal;">
		<?php foreach($responses as $resp){?>
			<tr style="cursor:pointer;" id="resp<?php echo $resp->id; ?>" class="<?php if($resp->viewed==0)echo "new";?>" >
				<td><?php echo $resp->name;?> submitted a feedback  (Click to view feedback)</td>
				<td title="<?php echo strftime("%B %d, %Y at %I:%M %p", $resp->time);?>"><?php echo nicetime($resp->time);?></td>
			</tr>
		<?php }?>
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
        $('.form-feedback table tr').click(function(){
        	window.location.href = './?Response=' + $(this).attr("id").substring(4) + '<?php echo '&'.	substr($getquery, 3)?>';
        });
	});
	function startDateShow(){
		$( "#startDate" ).datepicker( "show" );
	}
	function endDateShow(){
		$( "#endDate" ).datepicker( "show" );
	}
 </script>