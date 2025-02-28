(function($) {
	var microwaveWorkingStatus = false;
	var microwavePausedStatus = false;
	function updateDebug(){
		$('#debug').html('microwaveWorkingStatus: ' + microwaveWorkingStatus + '<br>microwavePausedStatus: ' + microwavePausedStatus);
	}

	$('#btnCanc').on('click', function(){
		if(microwavePausedStatus) {
			microwaveWorkingStatus = false;
			microwavePausedStatus = false;
			$('.microondas').removeClass('microondas-funcionando');
			$('#potencia').prop('disabled', false);
			$('#timer').val('');
			$('#potencia').val('');
		} else if(microwaveWorkingStatus) {
			microwaveWorkingStatus = false;
			microwavePausedStatus = true;
		} else {
			$('#timer').val('');
			$('#potencia').val('');
		}
		updateDebug();
	});
	$('#btnAqc').on('click', function(e){
		if(microwavePausedStatus) {
			microwavePausedStatus = false;
			microwaveWorkingStatus = true;
			alert('resume');
		} else if(microwaveWorkingStatus) {
			alert('add 30s');
		}
		updateDebug();
	});
	$('[data-add_time]').on('click', function(e){
		var e = $(this);
		$('#timer').val($('#timer').val().toString().concat(e.data('add_time')));
	});

	$('.form-ajax').on('submit', function(e){
		e.preventDefault();
		if(!microwavePausedStatus && !microwaveWorkingStatus) {
			var form = $(this);
			var formArgs = {
				type: form.attr('method'),
				url: form.attr('action'),
				data: new FormData(form[0]),
				contentType: false,
				processData: false,
				datatype: 'json',
				success: function(response){
					if(response.alert) {
						alert(response.alert);
					}
					if(response.action != false) {
						if(response.action == 'microwave_start') {
							microwaveStart(response.args);
						}

					}
				},
			};
			console.log(formArgs);
			$.ajax(formArgs);
		}
	});

	function microwaveStart(args) {
		$('.microondas').addClass('microondas-funcionando');
		microwaveWorkingStatus = true;
		microwaveVisorSetup(args.formatted_timer);
		var timer = args.timer;
		var potencia = args.potency_factor;
		$('#potencia').val(potencia);
		$('#potencia').prop('disabled', true);
		// alert('potencia: ' +potencia);
		updateDebug();
	}
	function microwaveVisorSetup(formattedTime) {
		$('#visor').val(formattedTime);
	}
}(jQuery));