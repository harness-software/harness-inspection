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
      //register our custom post type
      add_action('init', 'register_custom_post_type');
      //add && associate a field group with our custom post type
      add_action( 'acf/init', 'register_acf_field_group' );
  }
}, 0);

function register_custom_post_type() {
  register_post_type('inspection',
      array(
          'labels'      => array(
              'name'          => __('inspection', 'textdomain'),
              'singular_name' => __('inspection', 'textdomain'),
          ),
          'public'      => true,
          'has_archive' => true,
          'menu_icon'   => 'dashicons-welcome-write-blog',
          'show_in_graphql' => true,
          'graphql_single_name' => 'inspection',
          'graphql_plural_name' => 'inspections',
          'hierarchical' => true,
          'publicly_queryable'  => true,
      )
  );
}

function register_acf_field_group(){
  if( !function_exists('acf_add_local_field_group') || !function_exists('acf_add_local_field')){
    return;
  }

  $harnessComponents = [
    'Dee Ring',
    'Dee Pad',
    'Nylon Webbing Top Right',
    'Spring Loaded Friction Buckles Right',
    'Elastic Keepers(2) Right',
    'Nylon Webbing Top Left',
    'Stitching',
    'Tongue Buckle'
  ];
  
    acf_add_local_field_group([
		'key' => 'group_my_fields',
		'title' => __('My fields', 'txtdomain'),
		'label_placement' => 'top',
		'menu_order' => 0,
		'style' => 'default',
		'position' => 'normal',
    'show_in_graphql'       => 1,
		'graphql_field_name'    => 'fields',
		'location' => array (
      array (
        array (
          'param' => 'post_type',
          'operator' => '==',
          'value' => 'inspection',
        ),
      ),
    ),
	]);

  build_radio_fields($harnessComponents);
}

function build_radio_fields($array){
  foreach($array as $key=>$value) {
    $name = str_replace(' ', '_', strtolower($value));

    $temp = [
      'key' => 'field_' . $key,
      'label' => $value,
      'name' =>  $name,
      'parent' => 'group_my_fields',
      'type' => 'radio',
      'choices' => [
        'yes' => __('Yes', 'txtdomain'),
        'no' => __('No', 'txtdomain'),
        'not_applicable' => __('Not Applicable', 'txtdomain'),
      ],
      'prefix' => '',
      'instructions' => '',
      'required' => 0,
      'conditional_logic' => 0,
      'default_value' => '',
      'placeholder' => '',
      'prepend' => '',
      'append' => '',
      'maxlength' => '',
      'readonly' => 0,
      'disabled' => 0,
      'show_in_graphql'   => 1,
    ];

    acf_add_local_field($temp);
  }
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