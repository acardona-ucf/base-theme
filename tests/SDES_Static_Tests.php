<?php

// require_once 'vendor/autoload.php';
require_once '\..\functions\class-sdes-static.php';


class SDES_Static_Tests extends PHPUnit_Framework_TestCase
{
    public function test_phpUnitInstalled__RunPHPUnit__TestPasses()
    {
        $this->AssertTrue(true);
    }


    ///////////  SDES_Static::set_default_keyValue()  ////////////////////
    public function test_set_default_keyValue__NoKey__ReturnsDefaultValue()
    {
        // Arrange
        $key = 'transport';
        $defaultValue = 'postMessage';
        $args = Array();

        // Act
        SDES_Static::set_default_keyValue($args, $key, $defaultValue);

        // Assert
        $this->assertEquals($defaultValue, $args[$key]);
    }


    public function test_set_default_keyValue__KeySet__ReturnsSetValue()
    {
        // Arrange
        $key = 'transport';
        $defaultValue = 'postMessage';
        $args = Array();

        $setValue = 'refresh'; 
        $args[$key] = $setValue;

        // Act
        SDES_Static::set_default_keyValue($args, $key, $defaultValue);

        // Assert
        $this->assertEquals($setValue, $args[$key]);
    }



    ///////////  SDES_Static::sanitize_telephone_407()  ////////////////////
    public function test_sanitize_telephone_407__MissingAreaCode__ReturnsWithAreacode()
    {
        // Arrange
        $toSanitize = '823-5896';
        $expected = '407-823-5896';

        // Act
        $result = SDES_Static::sanitize_telephone_407($toSanitize);

        // Assert
        $this->assertEquals($expected, $result);
    }

    public function test_sanitize_telephone_407__MixedInput__ReturnsOnlyNumbersAndDashes()
    {
        // Arrange
        $toSanitize = 'ABCDEFG!@# 407-123-4567';
        $expected = '407-123-4567';

        // Act
        $result = SDES_Static::sanitize_telephone_407($toSanitize);

        // Assert
        $this->assertEquals($expected, $result);
    }

    public function test_sanitize_telephone_407__MissingtDashes__ReturnsWithDashes()
    {
        // Arrange
        $toSanitize = '4071234567';
        $expected = '407-123-4567';

        // Act
        $result = SDES_Static::sanitize_telephone_407($toSanitize);

        // Assert
        $this->assertEquals($expected, $result);
    }

    public function test_sanitize_telephone_407__AtLeast8Numeric__NoThrowsException()
    {
        // Arrange
        $toSanitize = 'ABCDEF123-';
        $expected = '407-123-';

        // Act
        $result = SDES_Static::sanitize_telephone_407($toSanitize);

        $this->assertEquals($expected, $result);
    }

    // TODO: handle short strings more gracefully, as $value[$lastDash] currently throws exceptions
    /**
     * @expectedException PHPUnit_Framework_Error
     * @expectedExceptionMessage Uninitialized string offset: 7
     */
    public function test_sanitize_telephone_407__Under8Numeric__ThrowsException()
    {
        // Arrange
        $toSanitize = 'ABCDEF123';
        $expected = '407-123';

        // Act
        $result = SDES_Static::sanitize_telephone_407($toSanitize);
    }



    ///////////  SDES_Static::get_theme_mod_defaultIfEmpty()  ////////////////////
    public function test_get_theme_mod_defaultIfEmpty__LookupYieldsNull__ReturnsDefault()
    {
        // Arrange
        $mockNull_get_theme_mod = function ($value, $default_to) { return null; };
        $expected = "expected default";
        $defaultValue = $expected;

        // Act
        $result = SDES_Static::get_theme_mod_defaultIfEmpty("foo", $defaultValue, $mockNull_get_theme_mod);

        // Assert
        $this->assertEquals($expected, $result);
    }

    public function test_get_theme_mod_defaultIfEmpty__LookupYieldssWhitespace__ReturnsDefault()
    {
        // Arrange
        $mockWhitespace_get_theme_mod = function ($value, $default_to) { return '       '; };
        $expected = "expected default";
        $defaultValue = $expected;

        // Act
        $result = SDES_Static::get_theme_mod_defaultIfEmpty("foo", $defaultValue, $mockWhitespace_get_theme_mod);

        // Assert
        $this->assertEquals($expected, $result);
    }

    public function test_get_theme_mod_defaultIfEmpty__LookupYieldsDefault__ReturnsDefault()
    {
        // Arrange
        $mockDefault_get_theme_mod = function ($value, $default_to) { return $default_to; };
        $expected = "expected default";
        $defaultValue = $expected;

        // Act
        $result = SDES_Static::get_theme_mod_defaultIfEmpty("foo", $defaultValue, $mockDefault_get_theme_mod);

        // Assert
        $this->assertEquals($expected, $result);
    }

    public function test_get_theme_mod_defaultIfEmpty__LookupYieldsValue__ReturnsValue()
    {
        // Arrange
        $mockDefault_get_theme_mod = function ($value, $default_to) { return 'bar'; };
        $expected = 'bar';
        $defaultValue = 'unexpected default';

        // Act
        $result = SDES_Static::get_theme_mod_defaultIfEmpty("foo", $defaultValue, $mockDefault_get_theme_mod);

        // Assert
        $this->assertEquals($expected, $result);
    }




    // wp_nav_menu: defaults set internally
    public static $WP_NAV_MENU_DEFAULTS = array( 'menu' => '', 'container' => 'div', 'container_class' => '', 'container_id' => '', 'menu_class' => 'menu', 'menu_id' => '',
        'echo' => true, 'fallback_cb' => 'wp_page_menu', 'before' => '', 'after' => '', 'link_before' => '', 'link_after' => '', 'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
        'depth' => 0, 'walker' => '', 'theme_location' => '' );

    ///////////  SDES_Static::fallback_navpills_warning()  ////////////////////
    public function test_fallback_navpills_warning__AdminNoWarn__ReturnsEmptyUl()
    {
        // Arrange
        $args = array('warn' => false, 'depth' => 1);
        $shouldWarn = true;
        $esc_attr = function($a){ return $a; }; // Bypass sanitize function for testing
        $get_query_var_preview = function(){ return false; };
        $expected = '<ul id="" class="menu"></ul>';

        // Act
        $args = array_merge(self::$WP_NAV_MENU_DEFAULTS, $args); // This is performed by wp_nav_menu.
        $result = SDES_Static::fallback_navpills_warning($args, $shouldWarn, 
            $get_query_var_preview, $esc_attr);

        // Assert
        $this->assertEquals($expected, $result);        
    }

    public function test_fallback_navpills_warning__AdminYesWarn_ReturnsUlWithWarningLi()
    {
        // Arrange
        $args = array('warn' => true, 'theme_location' =>'pg-Home', 'depth' => 1);
        $shouldWarn = true;
        $esc_attr = function($a){ return $a; }; // Bypass sanitize function for testing
        $get_query_var_preview = function(){ return false; };
        $expected = 
        '<ul id="" class="menu"><li><a class="text-warning adminmsg" style="color: #8a6d3b !important;" href="/wp-admin/nav-menus.php?action=locations#locations-pg-Home">Admin Warning: No menu set for "pg-Home" menu location.</a></li></ul>';

        // Act
        $args = array_merge(self::$WP_NAV_MENU_DEFAULTS, $args); // This is performed by wp_nav_menu.
        $result = SDES_Static::fallback_navpills_warning($args, $shouldWarn,
            $get_query_var_preview, $esc_attr);

        // Assert
        $this->assertEquals($expected, $result);        
    }

    public function test_fallback_navpills_warning__AdminNoWarnEcho__OutputsEmptyUl()
    {
        // Arrange
        $args = array('warn' => false, 'echo' => true, 'depth' => 1);
        $shouldWarn = true;
        $esc_attr = function($a){ return $a; }; // Bypass sanitize function for testing
        $get_query_var_preview = function(){ return false; };
        $expected = '<ul id="" class="menu"></ul>';

        // Act
        $args = array_merge(self::$WP_NAV_MENU_DEFAULTS, $args); // This is performed by wp_nav_menu.
        SDES_Static::fallback_navpills_warning($args, $shouldWarn, 
            $get_query_var_preview, $esc_attr);

        // Assert
        $this->expectOutputString($expected);
    }

    /** @dataProvider provide_test_fallback_navpills_warning__DepthNot1__ThrowsNotice */
    public function test_fallback_navpills_warning__DepthNot1__ThrowsNotice($depth)
    {
        // Assert
        $this->setExpectedException('PHPUnit_Framework_Error_Notice');

        // Arrange
        $args = array('warn' => false, 'echo' => true, 'depth' => $depth);
        $shouldWarn = true;
        $esc_attr = function($a){ return $a; }; // Bypass sanitize function for testing
        $get_query_var_preview = function(){ return false; };
        $expected = '<ul id="" class="menu"></ul>';

        // Act
        $args = array_merge(self::$WP_NAV_MENU_DEFAULTS, $args); // This is performed by wp_nav_menu.
        SDES_Static::fallback_navpills_warning($args, $shouldWarn, 
            $get_query_var_preview, $esc_attr);
    }
    public function provide_test_fallback_navpills_warning__DepthNot1__ThrowsNotice(){ return [ [-1], [0], [2], [3], [4] ];}


    ///////////  SDES_Static::fallback_navbar_list_pages()  ////////////////////
    public function test_fallback_navbar_list_pages__NoWarn1Page__ReturnsUlWithLi()
    {
        // Arrange
        $args = array('warn' => false, 'depth' => 1);
        $shouldWarn = true;
        $wp_list_pages = function(){ return '<li><a href="Home">Home</a></li>'; };
        $esc_attr = function($a){ return $a; }; // Bypass sanitize function for testing
        $get_query_var_preview = function(){ return false; };
        $expected = '<ul id="" class="menu"><li><a href="Home">Home</a></li></ul>';

        // Act
        $args = array_merge(self::$WP_NAV_MENU_DEFAULTS, $args); // This is performed by wp_nav_menu.
        $result = SDES_Static::fallback_navbar_list_pages($args, $shouldWarn, 
            $get_query_var_preview, $wp_list_pages, $esc_attr);

        // Assert
        $this->assertEquals($expected, $result);        
    }

    public function test_fallback_navbar_list_pages__PublicUser1Page__ReturnsUlWithLi()
    {
        // Arrange
        $args = array('warn' => true, 'depth' => 1);
        $shouldWarn = false;
        $wp_list_pages = function(){ return '<li><a href="Home">Home</a></li>'; };
        $esc_attr = function($a){ return $a; }; // Bypass sanitize function for testing
        $get_query_var_preview = function(){ return false; };
        $expected = '<ul id="" class="menu"><li><a href="Home">Home</a></li></ul>';

        // Act
        $args = array_merge(self::$WP_NAV_MENU_DEFAULTS, $args); // This is performed by wp_nav_menu.
        $result = SDES_Static::fallback_navbar_list_pages($args, $shouldWarn,
            $get_query_var_preview, $wp_list_pages, $esc_attr);

        // Assert
        $this->assertEquals($expected, $result);        
    }

    public function test_fallback_navbar_list_pages__AdminUser1Page_ReturnsUlWithLiAndWarningLi()
    {
        // Arrange
        $args = array('warn' => true, 'theme_location' =>'main-menu', 'depth' => 1);
        $shouldWarn = true;
        $wp_list_pages = function(){ return '<li><a href="Home">Home</a></li>'; };
        $esc_attr = function($a){ return $a; }; // Bypass sanitize function for testing
        $get_query_var_preview = function(){ return false; };
        $expected = 
        '<ul id="" class="menu"><li><a href="Home">Home</a></li><li><a class="text-danger adminmsg" style="color: red !important;" href="/wp-admin/nav-menus.php?action=locations#locations-main-menu">Admin Alert: Missing "main-menu" menu location.</a></li></ul>';

        // Act
        $args = array_merge(self::$WP_NAV_MENU_DEFAULTS, $args); // This is performed by wp_nav_menu.
        $result = SDES_Static::fallback_navbar_list_pages($args, $shouldWarn,
            $get_query_var_preview, $wp_list_pages, $esc_attr);

        // Assert
        $this->assertEquals($expected, $result);        
    }

    public function test_fallback_navbar_list_pages__NoWarn1PageEcho__OutputsUlWithLi()
    {
        // Arrange
        $args = array('warn' => false, 'echo' => true, 'depth' => 1);
        $shouldWarn = true;
        $wp_list_pages = function(){ return '<li><a href="Home">Home</a></li>'; };
        $esc_attr = function($a){ return $a; }; // Bypass sanitize function for testing
        $get_query_var_preview = function(){ return false; };
        $expected = '<ul id="" class="menu"><li><a href="Home">Home</a></li></ul>';

        // Act
        $args = array_merge(self::$WP_NAV_MENU_DEFAULTS, $args); // This is performed by wp_nav_menu.
        $result = SDES_Static::fallback_navbar_list_pages($args, $shouldWarn, 
            $get_query_var_preview, $wp_list_pages, $esc_attr);

        // Assert
        $this->assertEquals($expected, $result);
    }

    /** @dataProvider provide_test_fallback_navbar_list_pages__DepthNot1__ThrowsNotice */
    public function test_fallback_navbar_list_pages__DepthNot1__ThrowsNotice($depth)
    {
        // Assert
        $this->setExpectedException('PHPUnit_Framework_Error_Notice');

        // Arrange
        $args = array('warn' => false, 'echo' => true, 'depth' => $depth);
        $shouldWarn = true;
        $wp_list_pages = function(){ return '<li><a href="Home">Home</a></li>'; };
        $esc_attr = function($a){ return $a; }; // Bypass sanitize function for testing
        $get_query_var_preview = function(){ return false; };
        $expected = '<ul id="" class="menu"><li><a href="Home">Home</a></li></ul>';

        // Act
        $args = array_merge(self::$WP_NAV_MENU_DEFAULTS, $args); // This is performed by wp_nav_menu.
        $result = SDES_Static::fallback_navbar_list_pages($args, $shouldWarn, 
            $get_query_var_preview, $wp_list_pages, $esc_attr);
    }
    public function provide_test_fallback_navbar_list_pages__DepthNot1__ThrowsNotice(){ return [ [-1], [0], [2], [3], [4] ];}
    
}