<?php require_once("includes/initialize.php"); ?>
<div class="header-content">
	<a href="./"><img id="logo" src="<?php echo "./".str_replace("\\", "/", $config->company_logo);?>" class="left"/></a>
	<a href="./?Logout"><img src="./images/logout.png" class="right" id="logout"></a>
	<div class="right">
			<img src="<?php echo "./".str_replace("\\", "/", $config->admin_pic);?>"  class="left" id="profile-pic"/>
			<p id="udetails" class="right">
				<span style="display:block;margin-top:-13px;margin-bottom:8px;"><?php echo $config->admin_name;?></span>
				<span><?php echo $config->company_name;?></span>
			</p>
		</div>
</div>