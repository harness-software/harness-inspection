<?php
/** 
 * Plugin Name: Harness Inspections
 * Description: Harness Inspection plugin.
 * Version:     0.1.0
 * Author:      Harness Software
 * Author URI:  https://harnessup.com/
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require(plugin_dir_path( __FILE__ ) . 'src/HarnessInspection.php');

/**
 * Define constants
 */
const WPGRAPHQL_REQUIRED_MIN_VERSION = '0.4.0';

add_action( 'plugins_loaded', function() {
  if ( false === can_load_plugin() ) {
    //requirements aren't met to use this plugin, return early
    add_action( 'admin_init', 'show_admin_notice');
		return;
	}else{
    add_action('init', 'init');
    add_action( 'acf/init', 'initACF');
  }
}, 0);

function init(){
  $HarnessInspection = new HarnessInspection();
  $HarnessInspection->register_custom_post_type();
}

function initACF(){
  $HarnessInspection = new HarnessInspection();
  $HarnessInspection->register_acf_field_group();
}

function can_load_plugin() {
	// Is ACF active?
	if ( ! class_exists( 'ACF' ) ) {
		return false;
	}

	// Is WPGraphQL active?
	if ( ! class_exists( 'WPGraphQL' ) ) {
		return false;
	}

	// Do we have a WPGraphQL version to check against?
	if ( empty( defined( 'WPGRAPHQL_VERSION' ) ) ) {
		return false;
	}

	// Have we met the minimum version requirement?
	if ( true === version_compare( WPGRAPHQL_VERSION, WPGRAPHQL_REQUIRED_MIN_VERSION, 'lt' ) ) {
		return false;
	}

	return true;
}

function show_admin_notice(){
  echo 'You are not meeting the requirements to use this plugin';
}