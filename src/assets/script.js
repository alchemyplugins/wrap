jQuery(document).ready(function($) {
	$('body').addClass($('#post_type').val());
	if ($('body.widgets-php').length) {

	}



	// debug
	$('.widget-action', '#widgets-right').trigger('click');
	$('.sidebar-name-arrow', '#widgets-right .sidebars-column-2').trigger('click');
});
