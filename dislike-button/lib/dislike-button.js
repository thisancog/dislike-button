var dislike_button_active = false;

jQuery(document).ready(function() {
	jQuery('.dislike-button').each(function() {
		var id = jQuery(this).attr('id').substr(15);
		
		var cookie = (jQuery(this).hasClass('active')) ? 'active' : 'default';
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
	
		dislike_button_update(elem, data);
	});

	jQuery('.dislike-button').click(function() {
		if (!dislike_button_active) {
			dislike_button_active = true;
			var query = (jQuery(this).hasClass('default')) ? 'dislike' : 'undo';
			var data = {
				'action':	'update_dislike_button',
				'id':		jQuery(this).attr('id').substr(15),
				'query':	query,
				'state': 	"update" 
			}, elem = this;
	
			dislike_button_update(elem, data);
		}
	});
});

function dislike_button_update(elem, data) {
	jQuery.ajax({
		url: dislike_ajax_url,
		type: 'POST',
		data: data,
		success: function(response) {
			console.log('response: ' + response);
			var state = (response.state) ? response.state : 'default';
			jQuery(elem).removeClass('default active').addClass(state);
			jQuery(elem).find('.dislike-button-text').html(eval('dislike_' + state));
			jQuery(elem).find('.dislike-button-count').html(response.count);
			var expiry = new Date();
			expiry.setTime(expiry.getTime()+(99*365*24*60*60*1000)); 
			document.cookie = "dislike-" + response.id + "=" + state + "; expires=" + expiry.toGMTString();
			dislike_button_active = false;
		}
	});
}
