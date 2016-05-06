<?php 

/**
 * @see https://github.com/UCF/Students-Theme/blob/87dca3074cb48bef5d811789cf9a07c9eac55cd1/functions/admin.php#L74-L101
 */
/**
 * Modifies the default stylesheets associated with the TinyMCE editor.
 *
 * @return string
 * @author Jared Lang
 * */
function editor_styles( $css ) {
	$css   = array_map( 'trim', explode( ',', $css ) );
	$css   = implode( ',', $css );
	return $css;
}
add_filter( 'mce_css', 'editor_styles' );
/**
 * Edits second row of buttons in tinyMCE editor. Removing/adding actions
 *
 * @return array
 * @author Jared Lang
 * */
function editor_format_options( $row ) {
	$found = array_search( 'underline', $row );
	if ( False !== $found ) {
		unset( $row[$found] );
	}
	return $row;
}
add_filter( 'mce_buttons_2', 'editor_format_options' );




/**
 * @see https://github.com/UCF/Students-Theme/blob/87dca3074cb48bef5d811789cf9a07c9eac55cd1/functions/admin.php#L130-L268
 */
function add_advanced_styles_button( $buttons ) {
	array_unshift( $buttons, 'styleselect' );
	return $buttons;
}
add_filter( 'mce_buttons_2', 'add_advanced_styles_button' );

function add_editor_styles( $init_array ) {
	$style_formats = array(
		array(
			'title' => 'Text Transforms',
			'items' => array(
				array(
					'title'    => 'Uppercase Text',
					'selector' => 'h1,h2,h3,h4,h5,p',
					'classes'  => 'text-uppercase',
				),
				array(
					'title'    => 'Lowercase Text',
					'selector' => 'h1,h2,h3,h4,h5,p',
					'classes'  => 'text-lowercase'
				),
				array(
					'title'    => 'Capitalize Text',
					'selector' => 'h1,h2,h3,h4,h5,p',
					'classes'  => 'text-capitalize'
				),
			)
		),
		array(
			'title' => 'List Styles',
			'items' => array(
				array(
					'title'    => 'Unstyled List',
					'selector' => 'ul,ol',
					'classes'  => 'list-unstyled'
				),
				array(
					'title'    => 'Horizontal List',
					'selector' => 'ul,ol',
					'classes'  => 'list-inline'
				),
				array(
					'title'    => 'Bullets',
					'selector' => 'ul,ol',
					'classes'  => 'bullets'
				),
			),
		),
		array(
			'title' => 'Buttons',
			'items' => array(
				array(
					'title' => 'Button Sizes',
					'items' => array(
						array(
							'title'    => "Large Button",
							'selector' => 'a,button',
							'classes'  => 'btn btn-lg'
						),
						array(
							'title'    => 'Default Button',
							'selector' => 'a,button',
							'classes'  => 'btn'
						),
						array(
							'title'    => 'Small Button',
							'selector' => 'a,button',
							'classes'  => 'btn btn-sm'
						),
						array(
							'title'    => 'Extra Small Button',
							'selector' => 'a,button',
							'classes'  => 'btn btn-xs'
						),
					),
				),
				array(
					'title' => 'Button Styles',
					'items' => array(
						array(
							'title'    => 'Default',
							'selector' => 'a.btn,button.btn',
							'classes'  => 'btn-default'
						),
						array(
							'title'    => 'UCF Gold',
							'selector' => 'a.btn,button.btn',
							'classes'  => 'btn-ucf'
						),
						array(
							'title'    => 'Primary',
							'selector' => 'a.btn,button.btn',
							'classes'  => 'btn-primary'
						),
						array(
							'title'    => 'Success',
							'selector' => 'a.btn,button.btn',
							'classes'  => 'btn-success'
						),
						array(
							'title'    => 'Info',
							'selector' => 'a.btn,button.btn',
							'classes'  => 'btn-info'
						),
						array(
							'title'    => 'Warning',
							'selector' => 'a.btn,button.btn',
							'classes'  => 'btn-warning'
						),
						array(
							'title'    => 'Danger',
							'selector' => 'a.btn,button.btn',
							'classes'  => 'btn-danger'
						),
					),
				)
			),
		),
		array(
			'title'    => 'Lead',
			'selector' => 'p',
			'classes'  => 'lead'
		),
	);
	$init_array['style_formats'] = json_encode( $style_formats );
	return $init_array;
}
add_filter( 'tiny_mce_before_init', 'add_editor_styles' );

// function add_mce_stylesheet( $url ) {
// 	if ( ! empty( $url ) ) {
// 		$url .= ',';
// 	}
// 	$url .= THEME_CSS_URL . '/style.min.css';
// 	return $url;
// }
// add_filter( 'mce_css', 'add_mce_stylesheet' );

?>
