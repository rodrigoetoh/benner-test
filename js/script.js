(function($) {
	var microwaveWorkingStatus = false;
	var microwavePausedStatus = false;
	var microwaveTimer = 0;
	var microwavePotency = 0;
	var microwaveTimeout;
	function updateDebug(){
		// $('#debug').html('microwaveWorkingStatus: ' + microwaveWorkingStatus
		//  + '<br>microwavePausedStatus: ' + microwavePausedStatus
		//  + '<br>microwaveTimer: ' + microwaveTimer
		//  + '<br>microwavePotency: ' + microwavePotency
		// );
	}

	$('#btnCanc').on('click', function(){
		if(microwavePausedStatus) {
			microwaveReset();
			$('.status').html('');
		} else if(microwaveWorkingStatus) {
			microwaveWorkingStatus = false;
			microwavePausedStatus = true;
			clearTimeout(microwaveTimeout);
		} else {
			$('#timer').val('');
			$('#potencia').val('');
			microwaveResetStatus();
		}
		updateDebug();
	});
	$('#btnAqc').on('click', function(e){
		if(microwavePausedStatus) {
			microwavePausedStatus = false;
			microwaveWorkingStatus = true;
			microwaveRun();
		} else if(microwaveWorkingStatus) {
			microwaveTimer += 30;
		}
		updateDebug();
	});
	$('[data-add_time]').on('click', function(e){
		var e = $(this);
		$('#timer').val($('#timer').val().toString().concat(e.data('add_time')));
		microwaveResetStatus();
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
							microwaveSetup(response.args);
							microwaveResetStatus();
							microwaveStart();
						}
					}
				},
			};
			console.log(formArgs);
			$.ajax(formArgs);
		}
	});

	function microwaveReset() {
		microwaveWorkingStatus = false;
		microwavePausedStatus = false;
		microwaveTimer = 0;
		microwavePotency = 0;
		$('.microondas').removeClass('microondas-funcionando');
		$('#potencia').prop('disabled', false);
		$('#timer').val('');
		$('#potencia').val('');
	}
	function microwaveSetup(args) {
		microwaveVisorSetup(args.formatted_timer);
		microwaveTimer = args.timer;
		microwavePotency = args.potency_factor;
		$('#timer').val(microwaveTimer);
		$('#potencia').val(microwavePotency);
		$('#potencia').prop('disabled', true);
	}
	function microwaveStart() {
		$('.microondas').addClass('microondas-funcionando');
		microwavePausedStatus = false;
		microwaveWorkingStatus = true;
		microwaveRun();
		updateDebug();
	}
	function microwaveRun() {
		if(microwaveTimer > 0) {
			microwaveTimeout = setTimeout(function(){
				microwaveUpdateStatus();
				microwaveTimer--;
				microwaveRun();
				updateDebug();
			}, 1000);
		} else {
			microwaveReset();
			microwaveUpdateStatus();
		}
	}
	function microwaveUpdateStatus() {
		var html = $('.status').html() + ('.'.repeat(microwavePotency)) + ' ';
		if(microwaveTimer < 1) {
			html = $('.status').html() + ' Aquecimento concluído';
		}
		$('.status').html(html);
	}
	function microwaveResetStatus() {
		$('.status').html('');
	}
	function microwaveVisorSetup(formattedTime) {
		$('#visor').val(formattedTime);
	}
}(jQuery));