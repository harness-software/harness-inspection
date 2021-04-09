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
if (!defined('ABSPATH')) {
  exit;
}

require(plugin_dir_path(__FILE__) . 'src/HarnessInspection.php');
$HarnessInspection = new HarnessInspection();

/**
 * Define constants
 */
const WPGRAPHQL_REQUIRED_MIN_VERSION = '0.4.0';

add_action('plugins_loaded', function () {
  if (false === can_load_plugin()) {
    //requirements aren't met to use this plugin, return early
    add_action('admin_init', 'show_admin_notice');
    return;
  } else {
    add_action('init', 'init');
    add_action('acf/init', 'initACF');
    add_action('graphql_register_types', 'initGraphQLRegister');
    add_role('free_user', 'Free User', array('read' => true, 'level_0' => true));

    add_filter('retrieve_password_message', function ($message, $key, $user_login, $user_data) {
       
       $options = get_option( 'harness_inspections_plugin_options' );

       $client_url = (!empty($options['client_url'])) ? $options['client_url'] : getenv('CLIENT_URL');
       $reset_path =  (!empty($options['password_reset'])) ? $options['password_reset'] : '/harness-inspection/password-reset/';

      $user_email = $user_data->user_email;
      $site_name = 'Harness Software Inspection App';
      $message   = __('Someone has requested a password reset for the following account:') . "\r\n\r\n";
      /* translators: %s: Site name. */
      $message .= sprintf(__('Site Name: %s', 'Harness Software Inspection App'), $site_name) . "\r\n\r\n";
      /* translators: %s: User login. */
      $message .= sprintf(__('Username: %s', 'Harness Software Inspection App'), $user_login) . "\r\n\r\n";
      $message .= sprintf(__('Email Address: %s', 'Harness Software Inspection App'), $user_email) . "\r\n\r\n";
      $message .= __('If this was a mistake, ignore this email and nothing will happen.') . "\r\n\r\n";
      $message .= __('To reset your password, visit the following address:') . "\r\n\r\n";
      $message .= $client_url . $reset_path . $key . "?email=" . rawurlencode($user_email) . "&username=" . rawurlencode($user_login) . "\r\n\r\n";
      $requester_ip = $_SERVER['REMOTE_ADDR'];
      if ($requester_ip) {
        $message .= sprintf(
          /* translators: %s: IP address of password reset requester. */
          __('This password reset request originated from the IP address %s.'),
          $requester_ip
        ) . "\r\n";
      }
      return $message;
    }, 10, 4);

    add_filter( 'manage_inspection-points_posts_columns', 'inspections_filter_posts_columns' );

    function inspections_filter_posts_columns( $columns ) {
      $columns['location'] = __( 'Location' );
      return $columns;
    }

    add_action( 'manage_inspection-points_posts_custom_column', 'inspections_location_column', 10, 2);
    
      function inspections_location_column( $column, $post_id ) {
      // Image column
      if ( 'location' === $column ) {
        echo get_field( "location_id", $post_id );
      }
    } 

    add_filter( 'manage_edit-inspection-points_sortable_columns', 'inspections_location_sortable_columns');
    function inspections_location_sortable_columns( $columns ) {
      $columns['location'] = 'location_id';
      return $columns;
    }
  }

  //SETTINGS PAGE
  add_action( 'admin_menu', 'inspection_add_settings_page' );  

  function inspection_add_settings_page() {
    add_options_page( 'Harness Inspections Plugin Options', 'Harness Inspections', 'manage_options', 'harness-inspections', 'inspection_render_plugin_settings_page' );
  }

  function inspection_render_plugin_settings_page(){
    if ( !current_user_can( 'manage_options' ) )  {
      wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    ?>
      <div class="wrap">
        <h2>Harness Inspection Plugin Settings</h2>
        <form action="options.php" method="post">
          <?php 
            settings_fields( 'harness_inspections_plugin_options' );
            do_settings_sections( 'harness_inspections_plugin' ); 
          ?>
            <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
          </div>
    </form>
      </div>
    <?php
  }

  function harness_inspection_register_settings() {
    register_setting( 'harness_inspections_plugin_options', 'harness_inspections_plugin_options', 'harness_inspections_plugin_options_validate' );
    
    add_settings_section( 'harness_inspections_settings', 'Harness Inspection Settings', 'harness_inspections_section_text', 'harness_inspections_plugin' );

    add_settings_field( 'harness_inspections_plugin_setting_client_url', 'Client URL', 'harness_inspections_plugin_setting_client_url', 'harness_inspections_plugin', 'harness_inspections_settings' );

    add_settings_field( 'harness_inspections_plugin_setting_password_reset', 'Password Reset Path', 'harness_inspections_plugin_setting_password_reset', 'harness_inspections_plugin', 'harness_inspections_settings' );

}

add_action( 'admin_init', 'harness_inspection_register_settings' );

function harness_inspections_section_text() {
  echo '<p>Here you can set all the options for the Harness Inspections plugin</p>';
}

function harness_inspections_plugin_setting_client_url() {
  $options = get_option( 'harness_inspections_plugin_options' );
  echo "<input id='harness_inspections_plugin_setting_client_url' name='harness_inspections_plugin_options[client_url]' type='text' value='" . esc_url( $options['client_url'] ) . "' />";
}

function harness_inspections_plugin_setting_password_reset() {
  $options = get_option( 'harness_inspections_plugin_options' );
  echo "<input id='harness_inspections_plugin_setting_password_reset' name='harness_inspections_plugin_options[password_reset]' type='text' value='" . esc_attr( $options['password_reset'] ) . "' />";
}

function harness_inspections_plugin_options_validate($input) {
  $default_values = array (
    'client_url' => '',
    'password_reset'  => '',
  );

  if ( ! is_array( $input ) ){
    return $default_values;
  }

  $validated_output = array ();

  foreach ( $default_values as $key => $value ){
    if ( empty ( $input[ $key ] ) ){
      $validated_output[ $key ] = $value;
    }else{
      
      if('client_url' === $key){
        //sanitize URL
        $validated_output[$key] = filter_var( $input[$key], FILTER_VALIDATE_URL );
      }

      if('password_reset' === $key){
        //sanitize path
        //see: https://www.texelate.co.uk/blog/validate-a-url-path-with-php
        if(filter_var('http://www.example.com' . $input[$key], FILTER_VALIDATE_URL )){
          $validated_output[$key] = $input[$key];
        }
      }
    }
  }

  return $validated_output;
}

}, 0);

function init()
{
  global $HarnessInspection;
  $HarnessInspection->register_custom_post_types();
}

function initACF()
{
  global $HarnessInspection;
  $HarnessInspection->acfInit();
}

function initGraphQLRegister()
{
  global $HarnessInspection;
  $HarnessInspection->init_graphql_register();
}

function can_load_plugin()
{
  // Is ACF active?
  if (!class_exists('ACF')) {
    return false;
  }

  // Is WPGraphQL active?
  if (!class_exists('WPGraphQL')) {
    return false;
  }

  // Do we have a WPGraphQL version to check against?
  if (empty(defined('WPGRAPHQL_VERSION'))) {
    return false;
  }

  // Have we met the minimum version requirement?
  if (true === version_compare(WPGRAPHQL_VERSION, WPGRAPHQL_REQUIRED_MIN_VERSION, 'lt')) {
    return false;
  }

  return true;
}

function show_admin_notice()
{
  echo 'You are not meeting the requirements to use this plugin';
}
