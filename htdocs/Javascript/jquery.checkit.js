(function($){
//Private Variables
	var state = {
		backScript: "",
		sendtype: "POST",
		returntype: "json",
		sendData: {
			'ajax' : '',
			'input_value' : ''
		},
		passImg: "../pages/images/greencheck.png",
		failImg: "../pages/images/redx.png"
	};

	$.fn.checkit = function(options){
		state = $.extend(state, options);

		return this.each(function(){
			var $this = $(this);
			addFailImage($this);

			//storing data of matched element within it self
			$this.data('self', $this);
			//adding ajax processing to keyup event
			$this.on('keyup', function(){
				delay(function(){
					var $self = $this.data('self');

					if(matchRule($self))
					{
						/* Produces two data values which are sent to backend
						 * 'ajax'        -> valid_(name of element)
						 * 'input_value' -> val()
						 */
						state.sendData.ajax = 'valid_' + $this.attr('name');
						state.sendData.input_value = $this.val();
						$.ajax({
							type: state.sendtype,
							url: state.backScript,
							data: state.sendData,
							async: false,
							beforeSend: function(x){
								if(x && x.overrideMimeType){
									x.overrideMimeType("application/json;charset=UTF-8");
								}
							},
							dataType: state.returntype,
							success: function(response){
								if(response == null || response.valid)
								{
									removeImage($self);
									addPassImage($self);
								}
								else
								{
									removeImage($self);
									addFailImage($self);
								}

							}
						}); //end ajax
					}
					else
					{
						removeImage($self);
						addFailImage($self);				
					}
				}, 500);			
			});
		});
	}; //end constructor

	function addFailImage(e){
		var p = e.parent('div');
		$('<div class="line status"><h6></h6></div>')
		.insertAfter(p)
		.children('h6')
		.css({
			background: 'url('+ state.failImg +')',
			textIndex: '-9999px',
			width: '24px',
			height: '24px'
		});
	};

	function addPassImage(e){
		var p = e.parent('div');
		$('<div class="line status"><h6></h6></div>')
		.insertAfter(p)
		.children('h6')
		.css({
			background: 'url('+ state.passImg +')',
			textIndex: '-9999px',
			width: '24px',
			height: '24px'
		});
	};

	function removeImage(e){
		var p = e.parent('div');
		p.siblings('div.status').remove();	
	}

	function matchRule(e){
		$.metadata.setType('attr', 'rule');
		var checkVal = e.val();
		var rules = e.metadata();
		
		//minimum length
		if(rules.minlength && rules.minlength > checkVal.length){
			return false;
		}

		//maximum length
		if(rules.maxlength && rules.maxlength < checkVal.length){
			return false;
		}

		//connected inputs
		if(rules.connect)

		//
		if(rules.pattern.alphanumeric){
			//TODO
		}
		return true;
	}
})(jQuery);