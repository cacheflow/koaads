<div class="container">
	<div class="six columns center">

	<h3>Account Settings</h3>
	<form method="post" action="">
		<label for="username">Username</label>
		<input type="text" name="username" />

		<label for="email">Email</label>
		<input type="email" name="email" />

		<label for="current">Current Password</label>
		<input type="password" name="current" />

		<label for"newpass">New Password</label>
		<input type="password" name="newpass[]" />

		<label for"newconf">Confirm Password</label>
		<input type="password" name="newpass[]" />

		<input type="hidden" name="request" value="settings" /> 
		<input type="submit" value="Save Changes" />
	</form>
	</div>
</div>

<style type="text/css">
	
</style>