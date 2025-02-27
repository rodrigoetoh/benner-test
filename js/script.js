(function($) {
	// $('.mask-time').mask('00:00', {reverse: true});
	$('[data-add_time]').on('click', function(){
		var e = $(this);
		$('#timer').val($('#timer').val().toString().concat(e.data('add_time')));
	});
}(jQuery));