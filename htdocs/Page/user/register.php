<?php
	if(isset($_SESSION['registerSaved']))
	{
		$temp = explode(',', $_SESSION['registerSaved']);		
		$registerSaved = null;
		for($i = 0; $i < count($temp); $i++){
			$temp[$i] = explode(':', $temp[$i]);
			$registerSaved[$temp[$i][0]] = $temp[$i][1];
		}
	}
?>
<script type="text/javascript" src="<?php echo URL;?>javascripts/jquery.checkit.js"></script>
<!-- The container is a centered 960px -->
<div class="container">
	<div class="big-spacer"></div>
	<div class="row clearfix">
		<h2>Create a New Account</h2>
	</div>

	<div class="spacer"></div>

	<form method="post" action="">
		
		<div id="notice">Inputs marked <span class="required">*</span> are required</div>
		<table>
			<tr>
				<td>
					<div class="field-name">
						Username<sup><span class="required">*</span></sup>
					</div>
				</td>
				<td>
					<div class="field-input">
						<input type="text" name="username" value="<?php echo isset($registerSaved['username']) ? $registerSaved['username'] : ""; ?>" />
					</div>
				</td>
			</tr>

			<tr> 
				<td>
					<div class="field-name">
						Email<sup><span class="required">*</span></sup>
					</div>
				</td>
				<td>
					<div class="field-input">
						<input type="email" name="email" value="<?php echo isset($registerSaved['email']) ? $registerSaved['email'] : ""; ?>" />
					</div>
				</td>
			</tr>

			<tr> 
				<td>
					<div class="field-name">
						First Name
					</div>
				</td>
				<td>
					<div class="field-input">
						<input type="text" name="first" value="<?php echo isset($registerSaved['first']) ? $registerSaved['first'] : ""; ?>" />
					</div>
				</td>
			</tr>

			<tr> 
				<td>
					<div class="field-name">
						Last Name
					</div>
				</td>
				<td>
					<div class="field-input">
						<input type="text" name="last" value="<?php echo isset($registerSaved['last']) ? $registerSaved['last'] : ""; ?>" />
					</div>
				</td>
			</tr>
			<tr> 
				<td>
					<div class="field-name">
						Birthday
					</div>
				</td>
				<td>
					<div class="field-input">
						<input class="input-length2" type="text" name="month" maxlength="2" value="<?php echo isset($registerSaved['month']) ? $registerSaved['month'] : ""; ?>" />-
						<input class="input-length2" type="text" name="day" maxlength="2" value="<?php echo isset($registerSaved['day']) ? $registerSaved['day'] : ""; ?>" />-
						<input class="input-length4" type="text" name="year" maxlength="4" value="<?php echo isset($registerSaved['year']) ? $registerSaved['year'] : ""; ?>" />
					</div>
				</td>
			</tr>			
			<tr> 
				<td>
					<div class="field-name">
						Gender
					</div>
				</td>
				<td>
					<div class="field-input">
						<select>
							<option value="2" <?php echo isset($registerSaved['gender']) && $registerSaved['gender'] == 2 ? "selected=\"selected\"" : ""; ?> name="gender">Not specified</option>
							<option value="1" <?php echo isset($registerSaved['gender']) && $registerSaved['gender'] == 1 ? "selected=\"selected\"" : ""; ?> name="gender">Male</option>
							<option value="0" <?php echo isset($registerSaved['gender']) && $registerSaved['gender'] == 0 ? "selected=\"selected\"" : ""; ?> name="gender">Female</option>
						</select>
					</div>
				</td>
			</tr>

			<tr> 
				<td>
					<div class="field-name">
						New Password<sup><span class="required">*</span></sup>
					</div>
				</td>
				<td>
					<div class="field-input">
						<input type="password" name="password[]" />
					</div>
				</td>
			</tr>

			<tr> 
				<td>
					<div class="field-name">
						Confirm Password<sup><span class="required">*</span></sup>
					</div>
				</td>
				<td>
					<div class="field-input">
						<input type="password" name="password[]" />
					</div>
				</td>
			</tr>
		</table>

		<input type="hidden" name="request" value="signup" />
		<input type="submit" value="Sign Me Up!" />
	</form>
</div>


<script type="text/javascript">
	$(document).ready(function(){
		//$('input[type!="submit"]').not('input[type="hidden"]').checkit();
	});
</script>

<style type="text/css">
#notice{ font-size: 12px;}
.field-name{ width: 175px;}
.required{ color: #FF0000;}
.input-length2{ width: 15px;}
.input-length4{ width: 30px;}
.spacer{
	height: 20px;
}
.big-spacer{
	height: 30px;
}

div.line{
	float: left;
}

div.line.status{
	padding-top: 25px;
	padding-left: 30px; 
}

.invalid-input{
	text-indent: -9999px;
	background: url("<?php echo URL;?>pages/images/redx.png");
	width: 24px;
	height: 24px;
}

.valid-input{
	text-indent: -9999px;
	background: url("<?php echo URL;?>pages/images/greencheck.png");
	width: 24px;
	height: 24px;
}
</style>