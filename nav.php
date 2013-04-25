<nav>
		<div class="menu">
				
						<a href="./?Home"><img <?php if(count($_GET)==1 && isset($_GET["Home"])) echo 'class="current"'; ?> src="./images/nav_home.png"></a>
						<a href="./?FeedBackForm"><img <?php if(count($_GET)>0 && count($_GET)<3 && (isset($_GET["FormView"]) || isset($_GET["Preview"]) || isset($_GET["FeedBackForm"]))) echo 'class="current"'; ?>  src="./images/nav_form.png"></a>
						<a href="./?Report"><img <?php if(isset($_GET["Report"])) echo 'class="current"'; ?>  src="./images/nav_report.png"></a>
						<a href="./?Notification"><img <?php if(isset($_GET["Notification"])) echo 'class="current"'; ?>  src="./images/nav_notification.png"></a>
						<a href="./?FeedBack"><img <?php if((isset($_GET["FeedBack"]))) echo 'class="current"'; ?>  src="./images/nav_feedback.png"></a>
						<a href="./?Settings"><img <?php if(count($_GET)==1 && isset($_GET["Settings"])) echo 'class="current"'; ?> src="./images/nav_setting.png"></a>
				</div>

	</nav>
	<script type="text/javascript">
	function arrangeMenuItems(){
		var v= $('nav').width();
		v = (v - 216)/7;
		v=v+'px';
         $('nav div img').css('margin-left',v);
	}
	$(window).resize(function() {
		arrangeMenuItems();
	}); 
	jQuery(document).ready(function(){ 
		arrangeMenuItems();
	});
	
 </script>