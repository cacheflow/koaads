<script src="<?php echo URL;?>Plugin/nivo-slider/jquery.nivo.slider.js"></script>
<script src="<?php echo URL;?>Plugin/nivo-slider/jquery.nivo.slider.pack.js"></script>
<link rel="stylesheet" href="<?php echo URL;?>Plugin/nivo-slider/nivo-slider.css" media="screen" />
<link rel="stylesheet" href="<?php echo URL;?>Plugin/nivo-slider/themes/default/default.css" media="screen" />


<link rel="stylesheet" href="Page/home/landing-style.css" />
<div class="container">
	<div class="nav-pane">
		<div class="small-logo">
			Koaads
		</div>
		<form action="<?php echo URL;?>?process=user&amp;thread=login" method="post">
			<a id="register-link" href="<?php echo URL;?>?process=user&amp;thread=register">Not registered?</a>
			<div class="submit-pane">
				<input type="hidden" name="request" value="login" />
				<input type="submit" value="Log In" />
			</div>
			<table class="input-pane">
				<tr>
					<td><label for="email">Email</label></td>
					<td><label for="password">Password</label></td>
				</tr>
				<tr>
					<td><input type="email" name="email" /></td>
					<td><input type="password" name="password" /></td>
				</tr>
				<tr>
					<td><input type="checkbox" name="remember" value="1" />Keep me logged in</td>
					<td><a href="?process=user&amp;thread=forgot">Forgot your password?</a></td>
				</tr>
			</table>
		</form> 
	</div>
	<div class="contain-pane">
		<div class="search-pane">
			<form action="<?php echo URL;?>?process=search" method="post" autocomplete="off">
				<ul>
					<li>
						<label for="location">What City Are You In?</label>
					</li>
					<li>
						<input id="location-search" type="text" name="location" /> 
						<input type="hidden" name="ajax" value="search_location" />
						<input type="submit" value="Search" />
					</li>
				</ul>
			</form>
		</div>

		<div class="signup-pane">
			<form action="<?php echo URL;?>?process=user&amp;thread=signup" method="post">
				<ul>
					<li>
						<label for="first">First Name:</label>
						<input type="text" name="first" />
					</li>			
					<li>			
						<label for="last">Last Name:</label>
						<input type="text" name="last" />
					</li>			
					<li>
						<label for="email">Email:</label>
						<input type="email" name="email" />
					</li>
					<li>
						<label for="username">Username:</label>
						<input type="text" name="username" />
					</li>
					<li>			
						<label for="password">Password:</label>
						<input type="password" name="password[]" />
					</li>
					<li>			
						<label for="password">Re-enter Password:</label>
						<input type="password" name="password[]" />
					</li>
					<li>			
						<label for="gender">I am:</label>
						<select name="gender">
							<option value="2">Select Your Sex</option>
							<option value="1">Male</option>
							<option value="0">Female</option>
						</select>
					</li>
					<li>			
						<label for="month">Birthday:</label>
						<input type="text" class="length2" name="month" maxlength="2" />
						<input type="text" class="length2" name="day" maxlength="2" />
						<input type="text" class="length4" name="year" maxlength="4" />
					</li>
					<li>
						<input type="hidden" name="request" value="signup" />
						<input type="submit" value="Sign Me Up!" /> 			
					</li>
				</ul>
			</form>
		</div>

		<!--placeholders til tmr where it will be dynamically generated via php -->
		<div class="slider-pane theme-default">
		    <div id="slider" class="nivoSlider">
		        <img src="<?php echo URL;?>Plugin/nivo-slider/themes/images/toystory.jpg" />
		        <img src="<?php echo URL;?>Plugin/nivo-slider/themes/images/up.jpg" />
		        <img src="<?php echo URL;?>Plugin/nivo-slider/themes/images/walle.jpg" />
		        <img src="<?php echo URL;?>Plugin/nivo-slider/themes/images/nemo.jpg" />
		    </div>
		</div>
	</div> <!-- END .contain-pane -->
	<div class="footer-nav">

	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){

		$('#register-link').click(function(){
			if(!$('#closeBttn').size())
			{
				$('.signup-pane').animate({left:'+=50%'}, 600, function(){
					var $closeBttn = $('<div id="closeBttn">X</div>').appendTo('.signup-pane')
							.css({position:'absolute', right:'10px', top:'10px'})
							.click(function(){
								$('.signup-pane').animate({left:'-50%'}, 600, function(){
									$closeBttn.remove();
								})
							});
				});
			}
			return false;
		})
		$('#slider').nivoSlider({
			effect : 'slideInLeft',
			pauseTime : 10000
		});
	});
</script>

