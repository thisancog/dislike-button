<?php
/*
Plugin Name: Dislike Button
Plugin URI: https://github.com/thisancog/dislike-button
Description: A plugin for more honest reader evaluation.
Author: Matthias Planitzer
*/

/******************************************************************************************
	
	This is a simple dislike button plugin for Wordpress.
	Because many websites and oblivious editors would benefit from it. 

	Copy the folder to your /wp_content/plugins folder, activate the plugin in the Wordpress
	backend and include the PHP snippet <?php dislike_button(); ?> where the button should
	appear in your theme.

	The plugin uses cookies to store the visitors former actions.	

 ******************************************************************************************/


/*****************************************
	Actions
 *****************************************/

add_action('after_setup_theme', 'dislike_options_init');
add_action('admin_init', 'dislike_admin_init');
add_action('admin_menu', 'dislike_setup_theme_admin_menu');
add_action('plugins_loaded', 'dislike_helper_load_textdomain');
add_action('wp_ajax_update_dislike_button', 'update_dislike_button');
add_action('wp_head', 'dislike_helper_header');

function dislike_helper_load_textdomain() {
	load_plugin_textdomain('dislike-2016', false, plugin_basename(dirname( __FILE__ )) . '/lib/languages');
}


/*****************************************
	Init on admin pages
 *****************************************/

function dislike_options_init() {
	$o = get_option('dislike_options');
	if (false === $o) {
		$o = dislike_get_default_options();
		add_option('dislike_options', $o);
	}
}

function dislike_admin_init() {
	$lang = array (
			'chooseIcon' =>	__('Choose icon', 'dislike-2016'),
			'changeIcon' =>	__('Change icon', 'dislike-2016')
	);
	
	wp_localize_script('dislikestrings', 'localizedstring', $lang);
	wp_register_script('dislike-admin-script', plugins_url('lib/admin.js', __FILE__), array('jquery', 'jquery-ui-tabs'));
}

function dislike_setup_theme_admin_menu() {
	add_submenu_page('options-general.php', __('Dislike Button', 'dislike-2016'), __('Dislike Button', 'dislike-2016'), 'manage_options', 'dislike-options', 'dislike_settings');

	wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('iris', admin_url('js/iris.min.js'),array('jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch'), false, 1);
    wp_enqueue_script('wp-color-picker', admin_url('js/color-picker.min.js'), array('iris'), false,1);
    $colorpicker_l10n = array('clear' => __('Clear', 'dislike-2016'), 'defaultString' => __('Default', 'dislike-2016'), 'pick' => __('Select Color', 'dislike-2016'));
    wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $colorpicker_l10n );


	wp_enqueue_script('localize-theme-options', true);
	wp_enqueue_script('dislike-admin-script', plugins_url('lib/admin.js', __FILE__), array('jquery', 'jquery-ui-tabs'));
	wp_enqueue_style('dislike-admin-styles', plugins_url('lib/admin.css', __FILE__));
}



/******************************************************
	Options page
 ******************************************************/

function dislike_get_default_options() {
	$options = array(
		'colorbefore'		=> '#FF3131',
		'colorafter'		=> '#E52C2B',
		'textbefore'		=> __('Dislike', 'dislike-2016'),
		'textafter'			=> __('Disliked', 'dislike-2016'),
		'customcss'			=> ''
	);
	return $options;
}

function dislike_settings() {
	wp_enqueue_media();
	if (!current_user_can('manage_options')) {  
	    wp_die(__('You do not have sufficient permissions to access this page.', 'dislike_options'));
	}

	if (isset($_POST["update_settings"])) { 
		$options = array(
			'colorbefore'		=> (isset($_POST['colorbefore']) && !empty($_POST['colorbefore'])) ? esc_attr($_POST['colorbefore']) : '#FF3131',
			'colorafter'		=> (isset($_POST['colorafter']) && !empty($_POST['colorafter'])) ? esc_attr($_POST['colorafter']) : '#E52C2B',
			'textbefore'		=> (isset($_POST['textbefore']) && !empty($_POST['textbefore'])) ? esc_attr($_POST['textbefore']) : __('Dislike', 'dislike-2016'),
			'textafter'			=> (isset($_POST['textafter']) && !empty($_POST['textafter'])) ? esc_attr($_POST['textafter']) : __('Disliked', 'dislike-2016'),
			'customcss'			=> (isset($_POST['customcss']) && !empty($_POST['customcss'])) ? esc_attr($_POST['customcss']) : '',
		);
		update_option("dislike_options", $options);
	}

	$o = get_option('dislike_options');

	?>

	<h2 class="dislike-button-h2"><?php _e('Dislike Button Options', 'dislike-2016'); ?></h2>
	<?php if (isset($_POST["update_settings"])) : ?>
		<div id="message" class="updated fade"><p><strong><?php _e('Options were saved.', 'dislike-2016'); ?></strong></p></div>
	<?php endif; ?>

	<div id="dislike-button-tabs"><form method="post" action="">
		<ul id="dislike-button-menu">
				<li><a href="#appearance"><?php _e('Appearance', 'dislike-2016'); ?></a></li>

			<li id="submitli">
				<input type="hidden" name="update_settings" id="update_settings" value="Y" />
				<?php $activetab = (isset($_POST["activetab"])) ? $_POST["activetab"] : 0; ?>
				<input type="hidden" name="activetab" id="activetab" value="<?php echo $activetab; ?>" />
				<input type="submit" value="<?php _e('Save', 'dislike-2016'); ?>" id="submit" class="button" />
			</li>
		</ul>

		<div id="appearance" class="panel">
			<table>
				<tr>
					<td rowspan="2"><?php _e('Default appearance:', 'dislike-2016'); ?></td>
					<td><label for="colorbefore"><?php _e('Color:', 'dislike-2016'); ?></label></td>
					<td><input type="text id="colorbefore" name="colorbefore" class="colorpicker" value="<?php echo $o['colorbefore']; ?>"></td>
					</tr>
					<tr><td><label for="textbefore"><?php _e('Text:', 'dislike-2016'); ?></label></td>
					<td><input type="text" name="textbefore" value="<?php echo esc_attr($o['textbefore']); ?>" placeholder="<?php _e('Dislike', 'dislike-2016'); ?>" /></td>
				</tr>
				<tr>
					<td rowspan="2"><?php _e('After dislike:', 'dislike-2016'); ?></td>
					<td><label for="colorafter"><?php _e('Color:', 'dislike-2016'); ?></label></td>
					<td><input type="text id="colorafter" name="colorafter" class="colorpicker" value="<?php echo $o['colorafter']; ?>"></td></tr>
					<tr><td><label for="textafter"><?php _e('Text:', 'dislike-2016'); ?></label></td>
					<td><input type="text" name="textafter" value="<?php echo esc_attr($o['textafter']); ?>" placeholder="<?php _e('Disliked', 'dislike-2016'); ?>" /></td>
				</tr>
				<tr>
					<td><label for="customcss"><?php _e('Custom CSS:', 'dislike-2016'); ?></label></td>
					<td colspan="2"><textarea id="customcss" name="customcss"><?php echo $o['customcss']; ?></textarea></td>
			</table>
		</div>
	</form></div>
<?php
}

function get_smallest_image_size() {
	global $_wp_additional_image_sizes;
	$size = array(
			'size'		=> 'thumbnail',
			'width'		=> get_option("thumbnail_size_w")
	);

	foreach (get_intermediate_image_sizes() as $s) {
		if (in_array($s, array('thumbnail', 'medium', 'medium_large', 'large'))) {
			if (get_option("{$s}_size_w") < $size['width']) {
				$size = array(
					'size' 		=> $s,
					'width'		=> get_option("{$s}_size_w")
				);
			}
		} elseif (isset($_wp_additional_image_sizes[$s])) {
			if ($_wp_additional_image_sizes[$s]['width'] < $size['width']) {
				$sizes[$s] = array(
					'size'		=> $s,
					'width'		=> $_wp_additional_image_sizes[$s]['width']
				);
			}
		}
	}

	return $size['size'];
}


/******************************************************
	Functions
 ******************************************************/

function get_dislike_count($id = null) {
	if (null == $id) $id = get_the_ID();
	$count = get_post_meta($id, 'dislike-count');
	if (is_bool($count) || (is_array($count) && empty($count))) {
		$count = 0;
	} else {
		$count = $count[0];
	}

	return $count;
}

function dislike_button() {
	global $post;
	$id = $post->ID;
	$count = get_dislike_count($id);
	$o = get_option('dislike_options');

	$state = (isset($_COOKIE['dislike-' . $id]) && ('' != $_COOKIE['dislike-' . $id])) ? $_COOKIE['dislike-' . $id] : 'default';

	echo 	'<div class="dislike-button ' . $state . '" id="dislike-button-' . $id . '">
				<div class="dislike-button-icon"></div>
				<div class="dislike-button-text">' . __("Dislike", "dislike-2016") . '</div>
				<div class="dislike-button-count">' . $count . '</div>
			</div>';
}

function dislike_helper_header() {
	$o = get_option('dislike_options'); ?>
	<link rel="stylesheet" href="<?php echo plugins_url('lib/dislike-button.css', __FILE__); ?>" type="text/css" media="screen" />
	<style type="text/css">
		.dislike-button.default, .dislike-button.active:hover { background: <?php echo ('' != $o['colorbefore']) ? $o['colorbefore'] : '#FF3131'; ?>; }
		.dislike-button.active, .dislike-button.default:hover { background: <?php echo ('' != $o['colorafter']) ? $o['colorafter'] : '#E52C2B'; ?>; }
		<?php echo $o['customcss']; ?>
	</style>
	<script type="text/javascript" src="<?php echo plugins_url('lib/dislike-button.js', __FILE__); ?>"></script>
	<script type="text/javascript"> var dislike_ajax_url = '<?php echo admin_url('admin-ajax.php'); ?>';
									var dislike_default = '<?php echo $o['textbefore']; ?>';
									var dislike_active = '<?php echo $o['textafter']; ?>' ;</script>
<?php }


function update_dislike_button() {
	global $wpdb;
	$id = intval($_POST['id']);
	$query = $_POST['query'];
	$cookie = (isset($_POST['cookie'])) ? esc_attr($_POST['cookie']) : '';
	$state = 'default';
	$count = get_dislike_count($id);

	if ($cookie != '') {
		$state = $cookie;
	} else {
		if ($query == 'dislike') {
			$count++;
			$state = 'active';
			update_post_meta($id, 'dislike-count', $count);
		} else if ($query == 'undo') {
			$count--;
			$state = 'default';
			update_post_meta($id, 'dislike-count', $count);
		}
	}

	$response = array(
		'id'	=> $id,
		'count'	=> $count,
		'state'	=> $state
	);
	
    wp_send_json($response);
	wp_die();
}


?>
