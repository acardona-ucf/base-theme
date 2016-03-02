<?php
/**
 * @see js/shortcodebase.js
 * @see includes/shortcodebase-interface.php
 * @see includes/theme-help.php (Has section with documentation shortcodes.)
 */

define('SHORTCODE_JS_URI', get_bloginfo('template_url').'/js/shortcodebase.js' );
define('SHORTCODE_INTERFACE_PATH', get_stylesheet_directory().'/includes/shortcodebase-interface.php' );

/**
 * Register the javascript and PHP components required for ShortcodeBase to work.
 */
class ShortcodeBase_Loader {
	private static $isLoaded = false;
	public static $enable_fontawesome = false;

	public static function Load() {
		if ( !self::$isLoaded ) {
			add_action( 'admin_enqueue_scripts', 'ShortcodeBase_Loader::enqueue_shortcode_script' );
			add_action( 'media_buttons', 'ShortcodeBase_Loader::add_shortcode_interface' );
			add_action( 'admin_footer', 'ShortcodeBase_Loader::add_shortcode_interface_modal' );
			self::$isLoaded = true;
		}
	}

	public static function enqueue_shortcode_script() {
		wp_enqueue_script( 'shortcode-script', SHORTCODE_JS_URI );
	}

	// @see https://github.com/UCF/Students-Theme/blob/d56183079c70836adfcfaa2ac7b02cb4c935237d/functions/admin.php#L3-L11
	public static function add_shortcode_interface() {
		$js_id = "select-shortcode-form";
		$icon_classes = (self::$enable_fontawesome) ? "fa fa-code" : "dashicons dashicons-editor-code";
		$icon_styles  = (self::$enable_fontawesome) ? "" : "margin-top: 3px;";
		ob_start();
	  ?>
		<a href="#TB_inline?width=600&height=700&inlineId=<?= $js_id ?>" class="thickbox button" id="add-shortcode" title="Add Shortcode">
			<span class="<?= $icon_classes ?>" style=" <?= $icon_styles ?>"></span> Add Shortcode
		</a>
	  <?php
		echo ob_get_clean();
	}

	// @see https://github.com/UCF/Students-Theme/blob/d56183079c70836adfcfaa2ac7b02cb4c935237d/functions/admin.php#L13-L20
	public static function add_shortcode_interface_modal() {
		$page = basename( $_SERVER['PHP_SELF'] );
		if ( in_array( $page, array( 'post.php', 'page.php', 'page-new.php', 'post-new.php' ) ) ) {
			include_once( SHORTCODE_INTERFACE_PATH );
		}
	}
}
ShortcodeBase_Loader::Load();


 /**
 * Base Shortcode class.
 * @see https://github.com/UCF/Students-Theme/blob/d56183079c70836adfcfaa2ac7b02cb4c935237d/shortcodes.php#L2-L111
 **/
abstract class ShortcodeBase {
	public static $installed_custom_post_types = null;
	public static $installed_shortcodes = array();
	public
		$name        = 'Shortcode', // The name of the shortcode.
		$command     = 'shortcode', // The command used to call the shortcode.
		$description = 'This is the description of the shortcode.', // The description of the shortcode.
		$params      = array(), // The parameters used by the shortcode.
		$callback    = 'callback',
		$wysiwyg     = True; // Whether to add it to the shortcode Wysiwyg modal.

	/*
	 * Register the shortcode.
	 * @since v0.0.1
	 * @author Jim Barnes
	 * @return void
	 */
	public function register_shortcode() {
		add_shortcode( $this->command, array( $this, $this->callback ) );
	}

	/*
	 * Returns the html option markup.
	 * @since v0.0.1
	 * @author Jim Barnes
	 * @return string
	 */
	public function get_option_markup() {
		return sprintf('<option value="%s">%s</option>', $this->command, $this->name);
	}

	/*
	 * Returns the description html markup.
	 * @since v0.0.1
	 * @author Jim Barnes
	 * @return string
	 */
	public function get_description_markup() {
		return sprintf('<li class="shortcode-%s">%s</li>', $this->command, $this->description);
	}

	/*
	 * Returns the form html markup.
	 * @since v0.0.1
	 * @author Jim Barnes
	 * @return string
	 */
	public function get_form_markup() {
		ob_start();
	  ?>
		<li class="shortcode-<?php echo $this->command; ?>">
			<h3><?php echo $this->name; ?> Options</h3>
			<?php
				foreach($this->params as $param) {
					echo $this->get_field_input( $param, $this->command );
				}
			?>
		</li>
	  <?php
		return ob_get_clean();
	}

	/*
	 * Returns the appropriate markup for the field.
	 * @since v0.0.1
	 * @author Jim Barnes
	 * return string
	 */
	private function get_field_input( $field, $command ) {
		$name      = isset( $field['name'] ) ? $field['name'] : '';
		$id        = isset( $field['id'] ) ? $field['id'] : '';
		$help_text = isset( $field['help_text'] ) ? $field['help_text'] : '';
		$type      = isset( $field['type'] ) ? $field['type'] : 'text';
		$default   = isset( $field['default'] ) ? $field['default'] : '';
		$template  = isset( $field['template'] ) ? $tempalte['template'] : '';

		$retval = '<h4>' . $name . '</h4>';
		if ( $help_text ) {
			$retval .= '<p class="help">' . $help_text . '</p>';
		}
		switch( $type ) {
			case 'text':
			case 'date':
			case 'email':
			case 'url':
			case 'number':
			case 'color':
				$retval .= '<input type="' . $type . '" name="' . $command . '-' . $id . '" value="'.$default.'" default-value="' . $default . '" data-parameter="' . $id . '">';
				break;
			case 'dropdown':
				$choices = is_array( $field['choices'] ) ? $field['choices'] : array();
				$retval .= '<select type="text" name="' . $command . '-' . $id . '" value="" default-value="' . $default . '" data-parameter="' . $id . '">';
				foreach ( $choices as $choice ) {
					$retval .= '<option value="' . $choice['value'] . '">' . $choice['name'] . '</option>';
				}
				$retval .= '</select>';
				break;
			case 'checkbox':
				$retval = '<input id="'.$command.'-'.$id.'" type="checkbox" name="' . $command . '-' . $id . '" data-parameter="' . $id . '"><label for="'.$command.'-'.$id.'">'.$name.'</label>';
				break;
		}

		return $retval;
	}
}
