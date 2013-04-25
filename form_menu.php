<?php require_once("includes/initialize.php"); ?>
<?php
	if($session->is_logged_in()==false) {
	  redirect_to("index.php");
	}
	if(isset($_SESSION["form"]))unset($_SESSION["form"]);
?>
<div class="formMenu" style="height:100%;font-size:1.2em;font-weight:bold;margin:0 5%;">
	<a href="./?FeedBackForm&Create"><img src="./images/form_create.png"/></a>
	<a href="./?FeedBackForm&Update"><img src="./images/form_edit.png"/></a>
	<a href="./?FormView"><img src="./images/form_view.png"/></a>
</div>
<script type="text/javascript">
	$(window).resize(function() {
		arrangeIcons();
	}); 
	jQuery(document).ready(function(){ 
		arrangeIcons();
	});
	function arrangeIcons(){
		var iconWidth= $('.formMenu img').width();
		var adminHomeWidth= $('div.formMenu').width();
		var margin = $('.formMenu img').css('margin-left');
		margin = margin.substring(0,margin.lastIndexOf('px'));
		var n = Math.floor(adminHomeWidth/(iconWidth + 2*20));
			var freeSpace = adminHomeWidth - n*(iconWidth + 2*margin);
			var newMargin = parseInt(margin) + freeSpace/(2*n);
			$('.formMenu img').css('margin',newMargin + 'px');
			$('.formMenu img').css('margin-bottom','5%');
			$('.formMenu img').css('margin-top','5%');	
			$('body').css('background-color','#cee1e5');
	}
 </script>