
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




/**************************************************************
			Featured images
 **************************************************************/

 jQuery(document).ready(function(jQuery) {
 	jQuery('.imagepreview, .addimgbutton').click(function(e) {
		addimg(this, e);
	});

	jQuery('.deleteimgbutton').click(function(e) {
		deleteimg(this);
	});
});

function addimg(elem, e) {
	e.preventDefault();
	var image = wp.media({ 
		title: dislikestrings.chooseIcon,
		multiple: false
	}).open()
	.on('select', function(e){
		var uploaded_image = image.state().get('selection').first();
		jQuery(elem).parentsUntil('tr').find('.imgid').val(uploaded_image.toJSON().id);
		jQuery(elem).parentsUntil('tr').find('.imagepreview').attr('src', uploaded_image.toJSON().url);
		jQuery(elem).parentsUntil('tr').find('.addimgbutton').val(dislikestrings.changeIcon);
		jQuery(elem).parentsUntil('tr').find('.deleteimgbutton').removeClass('hidden');
	});
}

function deleteimg(elem) {
	jQuery(elem).parentsUntil('tr').find('.imgid').val('');
	jQuery(elem).parentsUntil('tr').find('.imagepreview').attr('src', '');
	jQuery(elem).parentsUntil('tr').find('.addimgbutton').val(dislikestrings.chooseIcon);
	jQuery(elem).parentsUntil('tr').find('.deleteimgbutton').addClass('hidden');
}