(function($) {
	var microwaveWorkingStatus = false;
	var microwavePausedStatus = false;
	var microwaveIsProgram = false;
	var microwaveTimer = 0;
	var microwavePotency = 0;
	var microwaveTimeout;
	var microwaveString = '.';
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
			if(microwaveIsProgram) {
				$('#btnAqc').prop('disabled', false);
			}
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
			if(microwaveIsProgram) {
				$('#btnAqc').prop('disabled', true);
			}
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
						} else if(response.action == 'microwave_new_program_register') {
							window.location.reload();
						} else if(response.action == 'microwave_program_setup') {
							microwaveSetup(response.args);
							microwaveProgramSetup();
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
		microwaveIsProgram = false;
		microwaveTimer = 0;
		microwavePotency = 0;
		microwaveString = '.';
		$('.microondas').removeClass('microondas-funcionando');
		$('#timer').val('');
		$('#potencia').val('');
		$('#potencia').prop('disabled', false);
		$('[data-add_time]').prop('disabled', false);
		$('#btnAqc').prop('disabled', false);
	}
	function microwaveSetup(args) {
		microwaveVisorSetup(args.formatted_timer);
		microwaveTimer = args.timer;
		microwavePotency = args.potency_factor;
		if(typeof args.custom_character != 'undefined') {
			microwaveString = args.custom_character;
		}
		$('#timer').val(microwaveTimer);
		$('#potencia').val(microwavePotency);
		$('#potencia').prop('disabled', true);
		$('[data-add_time]').prop('disabled', true);
	}
	function microwaveProgramSetup() {
		microwaveIsProgram = true;
		$('#btnAqc').prop('disabled', true);
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
		var html = $('.status').html() + (microwaveString.repeat(microwavePotency)) + ' ';
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

	$('.programas .programa').on('mouseenter', function(){
		var e = $(this);
		$('#programaDescricao').html(e.data('html'));
	});

	$('.programas [data-programa] input').on('click', function(){
		var e = $(this);
		$('.programas').parent('.form').submit();
	});
}(jQuery));