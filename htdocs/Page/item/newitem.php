

<form action="" method="post">
<!-- author info -->

<ul>
	<li>
		<label for="username">Username:</label>
		<input type="text" name="username" readonly="readonly" value="<?php echo $this->accObj->username;?>" /> 
	</li>
	<li>
		<label for="email">Email:</label>
		<input type="email" name="email" readonly="readonly"value="<?php echo $this->accObj->email; ?>" />
	</li>
	<li>
		<input type="checkbox" name="hideEmail" value="1" /><label for="hideEmail">Show Email</label>
	</li>
</ul>

<!-- listing details -->
<ul>
<label for="subject">Subject:</label>
<input type="text" name="subject" />
</ul>
<label for="content">Content:</label>
<textarea name="content" rows="15" cols="100">
</textarea>

<!-- uploadify -->
<input type="file" name="file_upload" id="file_upload" />

<!-- youtube -->
<label for="ytlink">Youtube Link:</label>
<input type="text" name="ytlink" />


<!-- location details-->
<input type="checkbox" name="storedAddress" value="1" />
<label for="storedAddress">Use Primary</label>

<label for="address_name">Location Name:</label>
<input type="text" name="address_name" />
<label for="address">Street:</label>
<input type="text" name="address" />
<label for="city">City:</label>
<input type="text" name="city" />
<label for="state">State:</label>
<input type="text" name="state" />
<label for="zipcode">Zipcode</label>
<input type="text" name="zipcode" maxlength="6" />
<label for="allowGeo">Allow Location Track:</label>
<input type="checkbox" name="allowGeo" value="1" />


<input type="hidden" name="request" value="item_new" />
<input type="submit" value="Create New Item" />
</form>