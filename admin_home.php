<?php require_once("includes/initialize.php"); ?>
<?php if(isset($_SESSION["form"]))unset($_SESSION["form"]); 

$form = Forms::find_by_user_id($session->user_id);
$count = 0;
if($form){
	$responses = Response::find_all_by_formid($form->id,$form->less_trigger,$form->equal_trigger,$form->greater_trigger);

	foreach($responses as $resp){
		if($resp->notified==0)$count++;
	}
}/*
if(sendMailTo("theachalaggarwal@gmail.com","BOdy of email" ,"Triggered FeedBack"))
	echo "Mail Send.";
else echo "Mail Send. Fail";*/
?>
<div class="adminHome" style="height:100%;font-size:1.2em;font-weight:bold;margin:0 5%;">
	<div class="menu-item"><a href="./?FeedBackForm"><img src="./images/admin_form.png"/></a></div>
	<div class="menu-item"><a href="./?Report"><img src="./images/admin_report.png"/></a></div>
	<div class="menu-item"><a  style="position:relative;" href="./?Notification"><img src="./images/admin_notification.png"/>
		<?php if($count!=0){ ?>
			<div class="noticount" style="
			    display: inline-block;
			    float: right;
			    top: -120px;
			    position: absolute;
			    background: #f7bf24;
			    min-width: <?php echo (strlen($count)*10 + 15);?>px;
			    vertical-align: middle;
			    text-align: right;
			    line-height: 33px;
			    color: white;
			    border-radius: 7px;
			    padding-right: 5px;
			    right: -<?php echo (strlen($count)*10 + 7);?>px;
			    z-index: -50;
			"><?php echo $count; ?></div>
			<?php }?>
	</a></div>
	<div class="menu-item"><a href="./?FeedBack"><img src="./images/admin_feedback.png"/></a></div>
	<div class="menu-item"><a href="./?Settings"><img src="./images/admin_setting.png"/></a></div>
</div>
<script type="text/javascript">
	$(window).resize(function() {
		arrangeIcons();
	}); 
	jQuery(document).ready(function(){ 
		arrangeIcons();
	});
	function arrangeIcons(){
		var iconWidth= $('.adminHome div.menu-item').width();
		var adminHomeWidth= $('div.adminHome').width();
		var margin = $('.adminHome div.menu-item').css('margin-left');
		//alert(margin);
		margin = margin.substring(0,margin.lastIndexOf('px'));
		var n = Math.floor(adminHomeWidth/(iconWidth + 2*20));//n specify number of items in a row
		var freeSpace = adminHomeWidth - n*(iconWidth + 2*margin);
		var newMargin = parseInt(margin) + freeSpace/(2*n);
		$('.adminHome div.menu-item').css('margin',newMargin + 'px');
		$('.adminHome div.menu-item').css('margin-bottom','5%');
		$('.adminHome div.menu-item').css('margin-top','5%');	
		$('body').css('background-color','#cee1e5');
		newMargin = -(newMargin + 15);
		//$('.adminHome .noticount').css("left",newMargin + "px");
	}
 </script>