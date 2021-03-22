<?php
/**
 * Main plugin class.
 */
class HarnessInspection {
	/**
	 * Class instances.
	 */

  private $harnessComponents = [
    '1' => 'Dee Ring',
    '2' => 'Dee Pad',
    '3' => 'Nylon Webbing',
    '4' => 'Spring Loaded Friction Buckles',
    '5' => 'Elastic Keepers(2)',
    '6' => 'Nylon Webbing',
    '7' => 'Spring Loaded Friction Buckles',
    '8' => 'Elastic Keepers(2)',
    '9' => 'Nylon Webbing',
    '10' => 'Stitching',
    '11' => 'Stitching',
    '12' => 'Tongue Buckle',
    '13' => 'Elastic Keeper(1)',
    '14' => 'Nylon Webbing',
    '15' => 'Stitching',
    '16' => 'Stitching',
    '17' => 'Tongue Buckle',
    '18' => 'Elastic Keeper(1)',
    '19' => 'Stitching',
    '20' => 'Nylon Webbing',
    '21' => 'Stitching',
    '22' => 'Stitching',
    '23' => 'Nylon Webbing',
    '24' => 'Grommets',
    '25' => 'Stitching',
    '26' => 'Nylon Webbing',
    '27' => 'Stitching',
    '28' => 'Stitching',
    '29' => 'Nylon Webbing',
    '30' => 'Grommets',
    '31' => 'Sub-Pelvic Strap',
    '31' => 'Back Strap',
    '33' => 'Stitching - Back Strap',
    '34' => 'Stitching - Back Strap',
    '35' => 'Chest Strap Pad',
    '36' => 'Nylon Webbing',
    '37' => 'Stitching',
    '38' => 'Mating Link',
    '39' => 'Chest Strap Pad',
    '40' => 'Nylon Webbing',
    '41' => 'Stitching',
    '42' => '3 Bar Mating Buckle',
    '43' => 'Elastic Keeper(1)',
    '44' => 'Tagging/Label System'
  ];

  private $textFields = [
    '45' => 'Serial Number',
    '46' => 'Inspector'
  ];

  private $dateFields = [
    '47' => 'Date of Manufacture',
    '48' => 'Date of Inspection'
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

  public function acfInit(){
    $this->register_harness_component_acf_field_group();
    $this->build_radio_fields($this->harnessComponents);
    $this->register_inspection_details_acf_field_group();
    $this->build_text_fields($this->textFields);
    $this->build_date_fields($this->dateFields);
    $this->build_pass_fail_field();
  }

  private function register_harness_component_acf_field_group() {
    if( !function_exists('acf_add_local_field_group') || !function_exists('acf_add_local_field')){
      return;
    }

      acf_add_local_field_group([
      'key' => 'group_harness_components',
      'title' => __('Harness Components', 'txtdomain'),
      'label_placement' => 'top',
      'menu_order' => 0,
      'style' => 'default',
      'position' => 'normal',
      'show_in_graphql'       => 1,
      'graphql_field_name'    => 'harnessComponents',
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
  }

  private function build_radio_fields($array){
  
    foreach($array as $key=>$value) {
      //if this ACF field is a duplicate we need to append id/key
      $name = str_replace(' ', '_', strtolower($value)) . '_' . $key;

      $temp = [
        'key' => 'field_' . $key,
        'label' => $value,
        'name' =>  $name,
        'parent' => 'group_harness_components',
        'type' => 'radio',
        'choices' => [
          'yes' => __('Yes', 'txtdomain'),
          'no' => __('No', 'txtdomain'),
          'not_applicable' => __('Not Applicable', 'txtdomain'),
        ],
        'prefix' => '',
        'instructions' => 'Pass Fail Criteria for this harness component',
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

  private function register_inspection_details_acf_field_group(){
    if( !function_exists('acf_add_local_field_group') || !function_exists('acf_add_local_field')){
      return;
    }

      acf_add_local_field_group([
      'key' => 'group_inspection_details',
      'title' => __('Harness Inspection Details', 'txtdomain'),
      'label_placement' => 'top',
      'menu_order' => 0,
      'style' => 'default',
      'position' => 'normal',
      'show_in_graphql'       => 1,
      'graphql_field_name'    => 'harnessInspectionDetails',
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
  }

  private function build_text_fields($array){
    foreach($array as $key=>$value) {
      $name = str_replace(' ', '_', strtolower($value));
      
      $temp = [
        'key' => 'field_' . $key,
        'label' => $value,
        'name' =>  $name,
        'parent' => 'group_inspection_details',
        'type' => 'text',
        'prefix' => '',
        'instructions' => '',
        'show_in_graphql'   => 1,
      ];
  
      acf_add_local_field($temp);
    }
  }

  private function build_date_fields($array){

    foreach($array as $key=>$value) {
      $name = str_replace(' ', '_', strtolower($value));
      
      $temp = [
        'key' => 'field_' . $key,
        'label' => $value,
        'name' =>  $name,
        'parent' => 'group_inspection_details',
        'type' => 'date_picker',
        'display_format' => 'd/m/Y',
			  'return_format' => 'd/m/Y',
        'prefix' => '',
        'instructions' => '',
        'show_in_graphql'   => 1,
      ];
  
      acf_add_local_field($temp);
    }
  }

  private function build_pass_fail_field(){
    //hardcoded key since this is the last field
    $key = 49;

    $field = [
      'key' => 'field_' . $key,
      'label' => 'Pass or Fail',
      'name' =>  'pass_fail_' . $key,
      'parent' => 'group_inspection_details',
      'type' => 'radio',
      'choices' => [
        'pass' => __('Pass', 'txtdomain'),
        'fail' => __('Fail', 'txtdomain'),
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

    acf_add_local_field($field);
  }
}