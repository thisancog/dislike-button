jQuery(document).ready(function() {
	jQuery('.dislike-button').each(function() {
		var id = jQuery(this).attr('id').substr(15);

		var cookie = (jQuery(this).hasClass('default')) ? 'default' : 'active';
		var cname = "dislike-" + id + "=";
    	var ca = document.cookie.split(';');
    	for (var i = 0; i < ca.length; i++) {
        	var c = ca[i];
        	while (c.charAt(0) == ' ') {
            	c = c.substring(1);
        	}
        	if (c.indexOf(cname) == 0) {
            	cookie = c.substring(cname.length,c.length);
        	}
    	}

		var data = {
			'action':	'update_dislike_button',
			'id':		id,
			'query':	'establish',
			'cookie': 	cookie
		},
		elem = this;
	
		jQuery.post(dislike_ajax_url, data, function(response) {
			dislike_button_update(elem, response);
		});
	});

	jQuery('.dislike-button').click(function() {
		var data = {
			'action':	'update_dislike_button',
			'id':		jQuery(this).attr('id').substr(15),
			'query':	(jQuery(this).hasClass('default')) ? 'dislike' : 'undo'
		}, elem = this;
	
		jQuery.post(dislike_ajax_url, data, function(response) {
			dislike_button_update(elem, response);			
		});
	});
});

function dislike_button_update(e, response) {
	jQuery(e).removeClass('default active').addClass(response.state);
	jQuery(e).find('.dislike-button-text').html(eval('dislike_' + response.state));
	jQuery(e).find('.dislike-button-count').html(response.count);
	var expiry = new Date();
	expiry.setTime(expiry.getTime()+(99*365*24*60*60*1000)); 
	document.cookie = "dislike-" + response.id + "=" + response.state + "; expires=" + expiry.toGMTString();
}

