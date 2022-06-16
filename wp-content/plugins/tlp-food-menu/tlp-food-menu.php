<?php
/**
 * Plugin Name: Food Menu - Restaurant Menu & Online Ordering for WooCommerce
 * Plugin URI: http://demo.radiustheme.com/wordpress/plugins/food-menu/
 * Description: A Simple Food & Restaurant Menu Display Plugin for Restaurant, Cafes, Fast Food, Coffee House with WooCommerce Online Ordering.
 * Author: RadiusTheme
 * Version: 3.0.11
 * Text Domain: tlp-food-menu
 * Domain Path: /languages
 * Author URI: https://radiustheme.com/
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

define('TLP_FOOD_MENU_VERSION', '3.0.11' );
define('TLP_FOOD_MENU_AUTHOR', 'RadiusTheme' );
define('TLP_FOOD_MENU_PLUGIN_PATH', dirname(__FILE__));
define('TLP_FOOD_MENU_PLUGIN_ACTIVE_FILE_NAME', plugin_basename( __FILE__ ));
define('TLP_FOOD_MENU_PLUGIN_URL', plugins_url('', __FILE__));
define('TLP_FOOD_MENU_LANGUAGE_PATH', dirname( plugin_basename( __FILE__ ) ) . '/languages');

require( 'lib/init.php' );
