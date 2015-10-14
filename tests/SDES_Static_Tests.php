<?php

// require_once 'vendor/autoload.php';
require_once '\..\functions\Class_SDES_Static.php';


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
}