$(document).ready(function(){
	/*
		 *	Variable Declarations
	 ******************************/
	var imgPath;
	var $modalWindow = $("#modal-window").hide();
	var $modalContent = $("#modal-container");
	var $imgPrivy = $("<img></img>");
	var $cancel = $("<button>Cancel</button>").click(function(){
		$imgPrivy.remove();
		$cancel.remove();
		$done.remove();
		$modalContent.css({'height': '50'});
		$modalWindow.hide();
		$.ajax({
			data : { 
				ajax : 'cancel_profilePic',
				img : imgPath
			}
		});
		$(window).unbind('beforeunload');
	});
	var $done = $("<button>Done</button>").click(function(){
		$.ajax({data : {
				ajax : 'edit_profilePic',
				img : imgPath
			},
			success: function(data){
				if(!data.error){
					$imgPrivy.remove();
					$cancel.remove();
					$done.remove();
					$modalContent.css({'height': '50'});
					$modalWindow.hide();
				}
			} 
		});
		$(window).unbind('beforeunload');
	});

	/*
	 *  Ajax Setup
	 ******************************/
	$.ajaxSetup({
		url : '?process=user', 
		type : 'POST',
		dataType: 'jsonp',
		cache : false
	});

	/*
	 * Picture Upload & Edit Logic
	 ******************************/
	$('#file_upload').uploadify({
		swf: '../../Plugin/uploadify/uploadify.swf',
		uploader: '/Plugin/uploadify/booyah.php',
		method: 'post',
		formData: {
			'ajax' : 'upload_profilePic',
			'<?php echo session_name();?>' : '<?php echo session_id();?>'
		},
		queueID: 'modal-content',
		removeTimeout : '1',
		buttonText: 'Upload From File',
		onDialogClose : function(queueData){
			if(queueData.filesSelected){
				$modalWindow.show();
			}
		},
		onUploadSuccess: function(file, data, response){
			var wait = setInterval(function(){
				if(!$('.uploadify-queue-item').size()){
					clearInterval(wait);
					$(window).bind('beforeunload', function(){
						$.ajax({
							data : { 
								ajax : 'cancel_profilePic',
								img : imgPath
							}
						});
						return "Wait! You haven't finished!\nWould you like to leave?";
					});
					$modalContent.animate({ height: '+=250'}, 200, 'linear');
					if(data != '0'){
						imgPath = data;
						$imgPrivy.attr({'src': imgPath, 'width' : 200, 'height' : 200});
						$modalContent.append($imgPrivy);
						$modalContent.append($cancel);
						$modalContent.append($done);							
					}
				}
			}, 400);
		},
		onUploadError: function(){
			var wait = setInterval(function(){
				if(!$('.uploadify-queue-item').size()){
					clearInterval(wait);	
					var $errorMsg = $('<div>Seems the serverbot couldn\'t handle that byte.<br />Please try another.</div>');
					var $confirm = $('<button>Ok</button>').click(function(){
						$(this).remove();
						$errorMsg.remove();
						$modalWindow.hide();
					});

					$('#modal-content').append($errorMsg);
					$('#modal-content').append($confirm);
					//user will have to click but in the future use ExternalInterface with javascript
					//http://help.adobe.com/en_US/FlashPlatform/reference/actionscript/3/flash/external/ExternalInterface.html
				}
			}, 200);

		}
	});
});