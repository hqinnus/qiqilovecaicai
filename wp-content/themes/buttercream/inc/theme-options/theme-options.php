<?php
/**
 * Buttercream Theme Options
 *
 * @package Buttercream

 */

/**
 * Register the form setting for our buttercream_options array.
 *
 * This function is attached to the admin_init action hook.
 *
 * This call to register_setting() registers a validation callback, buttercream_theme_options_validate(),
 * which is used when the option is saved, to ensure that our option values are complete, properly
 * formatted, and safe.
 *
 * We also use this function to add our theme option if it doesn't already exist.
 *

 */
function buttercream_theme_options_init() {

	// If we have no options in the database, let's add them now.
	if ( false === buttercream_get_theme_options() )
		add_option( 'buttercream_theme_options', buttercream_get_default_theme_options() );

	register_setting(
		'buttercream_options',       // Options group, see settings_fields() call in buttercream_theme_options_render_page()
		'buttercream_theme_options', // Database option, see buttercream_get_theme_options()
		'buttercream_theme_options_validate' // The sanitization callback, see buttercream_theme_options_validate()
	);

	// Register our settings field group
	add_settings_section( 'general', '', '__return_false', 'theme_options' );

	/* Register our individual settings fields */

	add_settings_field( 'buttercream_theme_style', __( 'Theme Style', 'buttercream' ), 'buttercream_settings_field_theme_style', 'theme_options', 'general' );

	add_settings_field( 'buttercream_custom_css', __( 'Custom CSS', 'buttercream' ), 'buttercream_settings_field_custom_css', 'theme_options', 'general' );

	add_settings_field(
		'buttercream_support', // Unique identifier for the field for this section
		__( 'Support Caroline Themes', 'buttercream' ), // Setting field label
		'buttercream_settings_field_support', // Function that renders the settings field
		'theme_options', // Menu slug, used to uniquely identify the page; see _s_theme_options_add_page()
		'general' // Settings section. Same as the first argument in the add_settings_section() above
	);

}
add_action( 'admin_init', 'buttercream_theme_options_init' );

/**
 * Change the capability required to save the 'buttercream_options' options group.
 *
 * @see buttercream_theme_options_init() First parameter to register_setting() is the name of the options group.
 * @see buttercream_theme_options_add_page() The edit_theme_options capability is used for viewing the page.
 *
 * @param string $capability The capability used for the page, which is manage_options by default.
 * @return string The capability to actually use.
 */
function buttercream_option_page_capability( $capability ) {
	return 'edit_theme_options';
}
add_filter( 'option_page_capability_buttercream_options', 'buttercream_option_page_capability' );

/**
 * Add our theme options page to the admin menu, including some help documentation.
 *
 * This function is attached to the admin_menu action hook.
 *
 */
function buttercream_theme_options_add_page() {
	global $buttercream_options_hook;
	$buttercream_options_hook = add_theme_page(
		__( 'Theme Options', 'buttercream' ),   // Name of page
		__( 'Theme Options', 'buttercream' ),   // Label in menu
		'edit_theme_options',                    // Capability required
		'theme_options',                         // Menu slug, used to uniquely identify the page
		'buttercream_theme_options_render_page' // Function that renders the options page
	);

	if ( ! $buttercream_options_hook )
		return;

	add_action('load-'.$buttercream_options_hook, 'buttercream_contextual_help', 10, 3);
}
add_action( 'admin_menu', 'buttercream_theme_options_add_page' );

/**
 * Returns an array of Theme Style options registered for Buttercream.
 *
 */
function buttercream_theme_style() {
	$buttercream_theme_style = array(
		'cupcake' => array(
			'value' => 'cupcake',
			'label' => __( 'Confetti', 'buttercream' ),
			'defaults' => array(
				'default-color' => 'ef3d46',
				'default-description-color' => '82573f',
				'default-header-image' => get_template_directory_uri() . '/img/cupcake.png',
			)
		),
		'yellow' => array(
			'value' => 'yellow',
			'label' => __( 'Chocolate Orange', 'buttercream' ),
			'defaults' => array(
				'default-color' => '663300',
				'default-description-color' => 'ed8d4b',
				'default-header-image' => get_template_directory_uri() . '/img/yellow.png',
			)
		),
		'red' => array(
			'value' => 'red',
			'label' => __( 'Red Velvet', 'buttercream' ),
			'defaults' => array(
				'default-color' => '444',
				'default-description-color' => 'ab3030',
				'default-header-image' => get_template_directory_uri() . '/img/red.png',
			)
		),

	);

	return apply_filters( 'buttercream_theme_style', $buttercream_theme_style );
}

/**
 * Returns the default options for Buttercream.
 *
 */
function buttercream_get_default_theme_options() {
	$default_theme_options = array(
		'theme_style' => 'cupcake',
		'custom_css' => '',
		'support' => 0
	);

	return apply_filters( 'buttercream_default_theme_options', $default_theme_options );
}

/**
 * Returns the options array for Buttercream.
 *
 */
function buttercream_get_theme_options() {
	return get_option( 'buttercream_theme_options', buttercream_get_default_theme_options() );
}

/**
 * Renders the Theme Style setting field.
 *
 */
function buttercream_settings_field_theme_style() {
	$options = buttercream_get_theme_options();

	foreach ( buttercream_theme_style() as $button ) {
	?>
	<div class="layout">
		<label class="description">
			<img src="<?php echo get_template_directory_uri() ?>/img/ss/<?php echo $button['value']; ?>.png" alt="<?php echo $button['label']; ?> Style" /><br />
			<input type="radio" name="buttercream_theme_options[theme_style]" value="<?php echo esc_attr( $button['value'] ); ?>" <?php checked( $options['theme_style'], $button['value'] ); ?> />
			<?php echo $button['label']; ?>
		</label>
	</div>
	<?php
	}
}

/**
 * Renders the Custom CSS setting field.
 *
 */
function buttercream_settings_field_custom_css() {
	$options = buttercream_get_theme_options();
	?>
	<textarea class="large-text" type="text" name="buttercream_theme_options[custom_css]" id="custom_css" cols="50" rows="10" /><?php echo esc_textarea( $options['custom_css'] ); ?></textarea>
	<label class="description" for="custom_css"><?php _e( 'Add any custom CSS rules here so they will persist through theme updates.', 'buttercream' ); ?></label>
	<?php
}

/**
 * Renders the Support setting field.
 */
function buttercream_settings_field_support() {
	$options = buttercream_get_theme_options();

	if ( $options['support'] !== 'on' || !isset( $options['support'] ) ) {

	?>
	<label for"buttercream-support">
		<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6G3NYZ5EN28EY" target="_blank"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" alt="PayPal - The safer, easier way to pay online!" class="alignright"></a>
		<?php _e( 'If you enjoy my themes, please consider making a secure donation using the PayPal button to your right. Anything is appreciated!', 'buttercream' ); ?>

		<br /><input type="checkbox" name="buttercream_theme_options[support]" id="support" <?php checked( 'on', $options['support'] ); ?> />
		<label class="description" for="support">
			<?php _e( 'No, thank you! Dismiss this message.', 'buttercream' ); ?>
		</label>
	</label>
	<?php
	}
	else { ?>
		<label class="description" for="support">
			<?php _e( 'Hide Donate Button', 'buttercream' ); ?>
		</label>
		<input type="checkbox" name="buttercream_theme_options[support]" id="support" <?php checked( 'on', $options['support'] ); ?> />

	</td>

	<?php
	}

}


/**
 * Returns the options array for Buttercream.
 *

 */
function buttercream_theme_options_render_page() {
	?>
	<div class="wrap">
		<?php screen_icon(); ?>
		<h2><?php printf( __( '%s Theme Options', 'buttercream' ), wp_get_theme() ); ?></h2>
		<?php settings_errors(); ?>

		<form method="post" action="options.php">
			<?php
				settings_fields( 'buttercream_options' );
				do_settings_sections( 'theme_options' );
				submit_button();
			?>
		</form>
	</div>
	<?php
}

/**
 * Returns layout defaults
 */
function buttercream_get_layout_defaults() {
	$options = buttercream_get_theme_options();
	$theme_style = $options['theme_style'];

	$default_theme_options = buttercream_get_default_theme_options();
	$default_theme_style = $default_theme_options['theme_style'];

	$theme_style_values = buttercream_theme_style();

	if ( $theme_style ) {
		$defaults = $theme_style_values[$theme_style]['defaults'];
	} else {
		$defaults = $theme_style_values[$default_theme_style]['defaults'];
	}

	return apply_filters( 'buttercream_get_layout_defaults', $defaults );
}

/**
 * Sanitize and validate form input. Accepts an array, return a sanitized array.
 *
 * @see buttercream_theme_options_init()
 * @todo set up Reset Options action
 *

 */
function buttercream_theme_options_validate( $input ) {
	$output = $defaults = buttercream_get_default_theme_options();

	// The sample Theme Styles value must be in our array of Theme Styles values
	if ( isset( $input['theme_style'] ) && array_key_exists( $input['theme_style'], buttercream_theme_style() ) )
		$output['theme_style'] = $input['theme_style'];

	// The Support field should either be on or off
	if ( ! isset( $input['support'] ) )
		$input['support'] = 'off';
	$output['support'] = ( $input['support'] == 'on' ? 'on' : 'off' );

	// The Custom CSS must be safe text with the allowed tags for posts
	if ( isset( $input['custom_css'] ) )
		$output['custom_css'] = wp_filter_nohtml_kses($input['custom_css'] );

	return apply_filters( 'buttercream_theme_options_validate', $output, $input, $defaults );
}

/**
 * Theme Options Admin Styles
*/

function buttercream_theme_options_admin_styles() {
	echo "<style type='text/css'>";
	echo ".layout .description { width: 300px; float: left; text-align: center; margin-bottom: 10px; padding: 10px; }";
	echo "</style>";
}

add_action( 'admin_enqueue_scripts', 'buttercream_theme_options_admin_styles' );

/**
 * Add a contextual help menu to the Theme Options panel
 */
function buttercream_contextual_help() {

	global $buttercream_options_hook;

	$screen = get_current_screen();

	if ( $screen->id == $buttercream_options_hook ) {

		//Store Theme Options tab in variable
		$theme_options_content = '<p><a href="http://wordpress.org/tags/buttercream?forum_id=5" target="_blank">' . __( 'For basic support, please post in the WordPress forums.', 'buttercream' ) . '</a></p>';
		$theme_options_content .= '<p><strong>' . __( 'Theme Style', 'buttercream' ) . '</strong> - ' . __( 'This is where you can choose the overall look and feel of your blog. Defaults to Confetti.', 'buttercream' ) . '</p>';
		$theme_options_content .= '<p><strong>' . __( 'Custom CSS', 'buttercream' ) . '</strong> - ' . __( 'You can override the theme\'s default CSS by putting your own code here.  It should be in the format:', 'buttercream' ) . '</p>';
		$theme_options_content .= '<blockquote><pre>.some-class { width: 100px; }</pre>';
		$theme_options_content .= '<pre>#some-id { background-color: #fff; }</pre></blockquote>';
		$theme_options_content .= '<p>' . __( 'Replacing any classes, ID\'s, etc. with the ones you want to override, and within them the attributes you want to change.', 'buttercream' ) . '</p>';
		$theme_options_content .= '<p><strong>' . __( 'Support Caroline Themes/Hide Donate Button', 'buttercream' ) . '</strong> - ' . __( 'If you like my themes and find them useful, please donate!  Checking the box will hide this information.', 'buttercream' ) . '</p>';
		$theme_options_content .= '<p><a href="http://www.carolinethemes.com" target="_blank">' . __( 'Visit Caroline Themes for more free WordPress themes!', 'buttercream' ) . '</a></p>';

		$screen->add_help_tab( array (
				'id' => 'buttercream-theme-options',
				'title' => __( 'Theme Options', 'buttercream' ),
				'content' => $theme_options_content
				)
		);

	}
}

?>