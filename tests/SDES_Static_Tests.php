<?php

// require_once 'vendor/autoload.php';
require_once '\..\functions\Class_SDES_Static.php';


class SDES_Static_Tests extends PHPUnit_Framework_TestCase
{
    public function test_phpUnitInstalled__RunPHPUnit__TestPasses()
    {
        $this->AssertTrue(true);
    }


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
}