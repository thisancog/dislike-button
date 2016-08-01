
/**************************************************************
			Theme Options
 **************************************************************/

jQuery(document).ready(function() {
	jQuery("#dislike-button-menu li:first-child").addClass('active');
	jQuery("#dislike-button-tabs .panel:first-of-type").addClass('active-tab');

	jQuery("#dislike-button-menu li").not('#submitli').click(function() {
		jQuery('#dislike-button-menu li').removeClass('active');
		jQuery('#dislike-button-tabs .panel').removeClass('active-tab');

		jQuery(this).addClass('active');
		var dest = jQuery(this).find('a').attr('href').substring(1);
		jQuery('#' + dest).addClass('active-tab');
	});
});
