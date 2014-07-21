<script type="text/javascript">
	$(document).ready(function(){
		$('#login').submit(function(){
			if( !$("#email-input").val() || !$("#pass-input").val() )
			{
				return false;
			}
		})
	});
</script>
<div class="content">
<div class="container">
	<h1 class="logo-img">
		koaads
	</h1>

	<div class="six columns">
		<div class="login-box">

			<form id="login" method="post" action="">
				<table>
					<tr>
						<label for="email">Email</label> 
						<input id="email-input" type="text" name="email" />
					</tr>
					<tr>
						<label for="email">Password</label>
						<input id="pass-input" type="password" name="password" />
					</tr>
				</table>

				<input type="submit" name="request" value="Login" />
			</form>

		</div> <!-- .login-box -->
	</div><!-- .six -->
	
	<div class="clearfix"></div>

	<div class="register-box">
		Not with Koaads? <a href="<?php echo URL;?>?process=user&amp;thread=register">Sign up</a>
	</div><!-- .register-box -->

</div><!-- .container -->
</div>

<style type="text/css">

h1.logo-img{
	width: 600px;
	height: 50px;
	background: url("<?php echo URL;?>pages/images/klogo.png") no-repeat;
	text-indent: -9999px;
}

.content{
	max-width: 500px;
	margin-left: auto;
	margin-right: auto;
}

.login-box{
	margin-left: 40px;
	padding: 30px 30px 0px 30px;
	background: #f1f1f1;
	border: 1px solid #e5e5e5;
	width: 220px;
}

.login-box input:focus{
	border: 1px solid #51b2d5;	
}

.register-box{
	margin-left: 50px;
}

.clearfix{
	clear: both;
	margin: 0px;
	padding: 0px;
}

</style>