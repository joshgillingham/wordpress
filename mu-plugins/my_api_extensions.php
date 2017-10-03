<?php
/*
Plugin Name:  Api Extensions
Plugin URI:   
Description:  Provides api endpoints for various devops needs
Installation: Stick the files in the mu-plugins directory
Version:      1.0
Author:       Josh Gillingham
Author URI:   http://joshgillingham.com
*/
defined('ABSPATH') or die("No script kiddies please!");

new Api_Extensions();

class Api_Extensions {

    public static $namespace = 'my-api/v1';
    private static $root_path = "/my_api_extensions/";
    private static $includes = array(
        'health-check.php',
        'nav.php'
    );

    function __construct() {
        
        foreach(self::$includes as $include) { 
            
            try {
                if (file_exists(__DIR__ . self::$root_path . $include)) {
                    include_once(__DIR__ . self::$root_path . $include);
                }
            }
            catch(Exception $e){
                error_log($e, 0);
            }
        }
    }
}
?>