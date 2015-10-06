(function( $ ) {
	$(function() {
		
		// Template Function
		// wp.customize( 'theme_customizer_setting_name', function( value ) {
		// 	value.bind( function( to ) {
		// 		// Bind dynamically using javascript when using Them Customizer inferace.
		//		// This will match anywhere that you use get_theme_mod() for this setting..
		// 	});
		// });

		wp.customize( 'tctheme_link_color', function( value ) {
			value.bind( function( to ) {
				$( 'div.site-title a' ).css( 'color', to );
			});
		});
		

		wp.customize( 'sdes_rev_2015-hours', function( value ) {
			value.bind( function( to ) {
				$( '#departmentInfo td:eq(0)').html( to );
			});
		});

		wp.customize( 'sdes_rev_2015-phone', function( value ) {
			value.bind( function( to ) {
				$( '#departmentInfo td:eq(1)').html( to );
			});
		});

		wp.customize( 'sdes_rev_2015-fax', function( value ) {
			value.bind( function( to ) {
				$( '#departmentInfo td:eq(2)').html( to );
			});
		});

		wp.customize( 'sdes_rev_2015-email', function( value ) {
			value.bind( function( to ) {
				$( '#departmentInfo td:eq(3)').html( to );
			});
		});

		wp.customize( 'sdes_rev_2015-taglineURL', function( value ) {
			value.bind( function( to ) {
				$( 'div.site-subtitle a').attr('href', to );
			});
		});
	});
}( jQuery ));
