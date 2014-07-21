<!--  JAVASCRIPT
	================================= -->
<script src="<?php echo URL;?>Plugin/uploadify/jquery.uploadify-3.1.js"></script>
<script src="<?php echo URL;?>Javascript/uploadEditPic.js"></script>

<link rel="stylesheet" href="<?php echo URL; ?>Plugin/uploadify/uploadify.css" />


<img width="150" height="150" src="<?php echo $this->accObj->picPath; ?>"></img>
<h4><?php echo $this->accObj->username;?></h4>

Your Account
<ul>
	<li>Name: 
		<?php 
			if($this->accObj->first != null) {
				echo " " . $this->accObj->first;
				if($this->accObj->last != null) {
					echo " " .$this->accObj->last;
				}
			}
		?>
	</li> 
	<li>Email: <?php echo " " . $this->accObj->email; ?></li>
	<li>Verified: <?php echo $this->accObj->checkPermissions('create_post') ? "Yes" : "No" ;?></li>
</ul>

Your Location
<ul>
	<li>Primary
		<ul>
			<li>
				Address: <?php echo $this->accObj->address; ?>
			</li>
			<li>
				City: <?php echo $this->accObj->city; ?>
			</li>
			<li>
				State:  <?php echo $this->accObj->state; ?>
			</li>
			<li>
				Zipcode: <?php echo $this->accObj->zipcode; ?>
			</li>
		</ul>
	</li>
</ul>
<input type="file" name="file_upload" id="file_upload" />

<div id="modal-window">
<div id="modal-overlay">
</div>
<div id="modal-container">
</div>
</div>



<!--  CSS
	================================= -->
<style type="text/css">

	/* Modal Window 
	===================================== */
	#modal-overlay{
		position:absolute; left:0; top:0; z-index:9999;
		width:100%; height:100%;
		background:#000;
		opacity:0.6; -moz-opacity:0.6; filter:alpha(opacity=6);
	}
	#modal-container{
  		background-clip: padding-box;  
 		background-color: white; 
 		border: 1px solid #808080;
 		border-radius: 3px; 
 		height: 50px;
		left: 50%;
		margin-left: -200px; 
		margin-top: -25px;
		-moz-background-clip: padding;
		overflow: hidden;
		padding: 25px;
		position: absolute;
		top: 20%;
		-webkit-border-radius: 3px;
		-webkit-background-clip: padding-box; 
		width: 370px;
		z-index: 10000;
	}
	#modal-content{
	}
	#upload_edit-done{
		width: 60px;
	}
	#upload_edit-cancel{
	}
	.uploadify-queue-item{
		top: 50px;
	}

</style>