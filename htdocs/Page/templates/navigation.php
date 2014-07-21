<div class="container"	
	<div class="row clearfix">		<!--    row #1     -->
		<ul id="nav">
			<li class="nav-item" style="width: 150px">
				<h6 class="logo-img">
					Koaads
				</h6>
			</li>

			<li class="nav-item first">
			<?php echo $this->accObj->__get("username");?>
				<ul class="subnav">
					<li class="subnav-item">
						<a href="<?php echo URL;?>user/profile">Profile</a>
					</li>
					<li class="subnav-item">
						<a href="<?php echo URL;?>user/settings">Settings</a>
					</li>
					<li class="subnav-item">
						<a href="<?php echo URL;?>user/logout/">Logout</a>
					</li>
				</ul>
			</li>

			<li class="nav-item">
				<a href="<?php echo URL;?>user/mail"><img src="<?php echo URL;?>pages/images/icon/message-icon.png" /></a>
			</li>

			<li class="nav-item">
				<a href="<?php echo URL;?>user/subscription"><img src="<?php echo URL;?>pages/images/icon/following-icon.png" /></a>
			</li>

			<li class="nav-item last">
				<a href="<?php echo URL;?>user/newpost"><button>Create Listing</button></a>
			</li>	
		</ul>
	</div> 
</div>							<!--    row #1     -->

<style type="text/css">
	a {text-decoration: none;}
	#nav {overflow: visible; font-size: 18px;}
	#nav > li {float: left;}
	.nav-item {padding-top: 5px; padding-left: 15px; width: 50px; height: 30px;}
	.nav-item.first{padding-left: 30px;}
	.nav-item.last{width: 100px;}
	.nav-item.first:hover .subnav {display: block;  position: relative; z-index: 999; left: -15px;}
	.subnav {width: 100px; height: 150px; overflow: hidden; list-style: none; margin: 0px; padding: 0px; display: none; background: #FFF; border: 1px solid #e5e5e5;}
	.subnav-item {margin-top: 20px; padding: 0px 5px 5px 15px;}
	h6.logo-img {width: 150px; height: 20px; background: url("<?php echo URL;?>pages/images/klogo-150x20.png");text-indent: -9999px;}
</style>