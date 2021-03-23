<?php
/**
 * Main plugin class.
 */
class HarnessInspection {

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
    $this->register_harness_component_acf_field_group($this->harnessComponents);
    $this->register_inspection_details_acf_field_group();
    $this->build_text_fields($this->textFields);
    $this->build_date_fields($this->dateFields);
    $this->build_pass_fail_field();
  }

  private function register_harness_component_acf_field_group($array) {
    if( !function_exists('acf_add_local_field_group') || !function_exists('acf_add_local_field')){
      return;
    }

    $fields = [];

    foreach($array as $key=>$value) {
      //if this ACF field is a duplicate we need to append id/key
      $name = str_replace(' ', '_', strtolower($value)) . '_' . $key;

      $field = [
        [
          'key' => 'field_group_'  . $key,
          'label' => $value,
          'name' => $name,  
          'type' => 'group',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'show_in_graphql' => 1,
          'layout' => 'block',
          'sub_fields' => [
            [
              'key' => 'field_description_' . $key,
              'label' => 'Description',
              'name' => 'description',
              'type' => 'text',
              'instructions' => 'Enter component description.',
              'required' => 0,
              'conditional_logic' => 0,
              'show_in_graphql' => 1,
              'default_value' => '',
              'placeholder' => '',
              'prepend' => '',
              'append' => '',
              'maxlength' => '',
            ],
            [ 
              'key' => 'field_condition_' . $key,
              'label' => 'Condition',
              'name' => 'condition',
              'type' => 'radio',
              'instructions' => '',
              'required' => 0,
              'conditional_logic' => 0,
              'show_in_graphql' => 1,
              'choices' => array(
                'pass' => 'Pass',
                'fail' => 'Fail',
                'not_applicable' => 'Not Applicable'
              ),
              'allow_null' => 0,
              'other_choice' => 0,
              'default_value' => '',
              'layout' => 'vertical',
              'return_format' => 'value',
              'save_other_choice' => 0,
            ],
            [
              'key' => 'field_id_' . $key,
					    'label' => 'Harness Point ID',
					    'name' => 'harness_point_id',
					    'type' => 'number',
              'default_value' => $key,
					    'instructions' => '',
					    'required' => 0,
					    'conditional_logic' => 0,
					    'show_in_graphql' => 1,
					    'placeholder' => '',
					    'prepend' => '',
					    'append' => '',
					    'min' => '',
					    'max' => '',
					    'step' => '',
            ]
          ]
        ]
      ];

      array_push($fields, $field);
    }
   
    acf_add_local_field_group(array(
      'key' => 'group_harness_components',
      'title' => __('Harness Components', 'txtdomain'),
      'fields' => array_reduce($fields, 'array_merge', array()),
      'location' => array(
        array(
          array(
            'param' => 'post_type',
            'operator' => '==',
            'value' => 'inspection',
          ),
        ),
      ),
      'menu_order' => 0,
      'position' => 'normal',
      'style' => 'default',
      'label_placement' => 'top',
      'instruction_placement' => 'label',
      'hide_on_screen' => '',
      'active' => true,
      'description' => '',
      'show_in_graphql' => 1,
      'graphql_field_name' => 'harnessComponents',
    ));
  
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
        'key' => 'field_text_' . $key,
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
        'key' => 'field_date_' . $key,
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
      'key' => 'field_pass_fail',
      'label' => 'Pass or Fail',
      'name' =>  'pass_fail',
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