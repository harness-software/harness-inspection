<?php
/**
 * Main plugin class.
 */
class HarnessInspection {
	/**
	 * Class instances.
	 */

  public $harnessComponents = [
    'Dee Ring',
    'Dee Pad',
    'Nylon Webbing Top Right',
    'Spring Loaded Friction Buckles Right',
    'Elastic Keepers(2) Right',
    'Nylon Webbing Top Left',
    'Stitching',
    'Tongue Buckle'
  ];

	public function register_custom_post_type() {
	
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

  public function register_acf_field_group() {
    if( !function_exists('acf_add_local_field_group') || !function_exists('acf_add_local_field')){
      return;
    }

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
  
    $this->build_radio_fields($this->harnessComponents);
  }

  public function build_radio_fields($array){
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
}