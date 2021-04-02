<?php

/**
 * Main plugin class.
 */
class HarnessInspection
{

  //this will eventually reflect total # of fields but leaving at 9 for testing
  const TOTAL_FIELDS = 9;

  private $inspectionDetailFields = [
    '0' => 'Serial Number',
    '1' => 'Inspector',
    '2' => 'Author Email',
    '3' => 'Author ID',
    '4' => 'Date of Manufacture',
    '5' => 'Date of Inspection',
    '6' => 'Fail Point',
    '7' => 'Number of Points before Failure'
  ];

  private $inspectionPointFields = [
    '8' => 'Location Id',
    '9' => 'Description'
  ];

  public function clean_name($string, $int)
  {
    $search = array("(", ")", " ", "-", "/");
    $replace = array("_", "", "_", "", "_");
    return str_replace($search, $replace, strtolower($string)) . '_' . $int;
  }

  public function register_custom_post_types()
  {

    register_post_type(
      'inspection-points',
      array(
        'labels'      => array(
          'name'          => __('inspection points', 'textdomain'),
          'singular_name' => __('inspection points', 'textdomain'),
        ),
        'public'      => true,
        'has_archive' => true,
        'menu_icon'   => 'dashicons-list-view',
        'show_in_graphql' => true,
        'graphql_single_name' => 'inspectionPoint',
        'graphql_plural_name' => 'inspectionPoints',
        'hierarchical' => true,
        'publicly_queryable'  => true,
      )
    );

    register_post_type(
      'inspection',
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
        'supports' => ['title', 'editor', 'author', 'excerpt']
      )
    );
  }

  public function acfInit()
  {
    $this->register_inspection_details_acf_field_group();
    $this->register_inspection_points_acf_field_group();
    $this->build_inspection_detail_fields($this->inspectionDetailFields);
    $this->build_inspection_point_fields($this->inspectionPointFields);
    $this->build_pass_fail_field();
  }

  public function init_graphql_register()
  {
    $this->register_graphql_type_and_mutation();
  }

  private function register_inspection_details_acf_field_group()
  {
    if (!function_exists('acf_add_local_field_group') || !function_exists('acf_add_local_field')) {
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
      'location' => array(
        array(
          array(
            'param' => 'post_type',
            'operator' => '==',
            'value' => 'inspection',
          ),
        ),
      ),
    ]);
  }

  private function register_inspection_points_acf_field_group()
  {
    if (!function_exists('acf_add_local_field_group') || !function_exists('acf_add_local_field')) {
      return;
    }

    acf_add_local_field_group([
      'key' => 'group_inspection_points',
      'title' => __('Harness Inspection Points', 'txtdomain'),
      'label_placement' => 'top',
      'menu_order' => 0,
      'style' => 'default',
      'position' => 'normal',
      'show_in_graphql'       => 1,
      'graphql_field_name'    => 'harnessInspectionPoint',
      'location' => array(
        array(
          array(
            'param' => 'post_type',
            'operator' => '==',
            'value' => 'inspection-points',
          ),
        ),
      ),
    ]);
  }

  private function build_inspection_detail_fields($array)
  {
    foreach ($array as $key => $value) {
      $name = str_replace(' ', '_', strtolower($value));

      $numbersArray = [
        'Number of Points before Failure',
      ];

      $type = in_array($value, $numbersArray) ? 'number' : 'text';

      $temp = [
        'key' => 'field_text_' . $key,
        'label' => $value,
        'name' =>  $name,
        'parent' => 'group_inspection_details',
        'type' => $type,
        'prefix' => '',
        'instructions' => '',
        'show_in_graphql'   => 1,
      ];

      acf_add_local_field($temp);
    }
  }

  private function build_inspection_point_fields($array)
  {
    foreach ($array as $key => $value) {
      $name = str_replace(' ', '_', strtolower($value));

      $type = $value === 'Location Id' ? 'number' : 'text';

      $temp = [
        'key' => 'field_text_' . $key,
        'label' => $value,
        'name' =>  $name,
        'parent' => 'group_inspection_points',
        'type' => $type,
        'prefix' => '',
        'instructions' => '',
        'show_in_graphql'   => 1,
      ];

      acf_add_local_field($temp);
    }
  }

  private function build_pass_fail_field()
  {

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

  private function register_graphql_type_and_mutation()
  {
    //not sure we need 'harness_point_id' or 'description'
    register_graphql_input_type('HarnessInspectionType',  [
      'fields'      => [
        'condition' => ['type' => 'Boolean', 'description' => 'condition of component'],
      ]
    ]);

    register_graphql_mutation('makeInspection', [
      'inputFields' => [
        'serial_number' => [
          'type' => ['non_null' => 'String'],
          'description' => 'Serial number of the harness being inspected'
        ],
        'date_of_manufacture' => [
          'type' => ['non_null' => 'String'],
          'description' => 'Date the harness being inspected way manufactured'
        ],
        'author_id' => [
          'type' => ['non_null' => 'ID'],
          'description' => 'Must be the user databaseId'
        ],
        'author_email' => [
          'type' => ['non_null' => 'String'],
          'description' => 'Current user email'
        ],
        'date_of_inspection' => [
          'type' => ['non_null' => 'String'],
          'description' => 'Date of harness inspection'
        ],
        'inspector' => [
          'type' => ['non_null' => 'String'],
          'description' => 'Full name for the user completing the inspection'
        ],
        'pass_fail' => [
          'type' => ['non_null' => 'Boolean']
        ],
        'title' => [
          'type' => ['non_null' => 'String'],
          'description' => 'Is a combination of the Serial Number and Date completed: #-Date'
        ],
        'fail_point' => [
          'type' => 'String',
          'description' => 'When the harness fails inspection, supply the Label of the fail point'
        ],
        'number_of_points_before_failure' => [
          'type' => 'Number'
        ],
        'content' => [
          'type' => 'String',
          'description' => 'Used as the email body when sending the Inspection to required stack holders'
        ],
        'share_email' => [
          'type' => 'String',
          'description' => 'Can be supplied as an extra email to share the inspection with over email. This is saved in the Inspection excerpt field of the CPT',
        ],
      ],
      'outputFields' => [
        'success' => [
          'type' => ['non_null' => 'Boolean'],
        ],
        'id' => [
          'type' => 'ID',
          'description' => 'Id of the newly created Inspection post type'
        ],
        'error' => [
          'type' => 'String'
        ]
      ],
      'mutateAndGetPayload' => function ($input, $context, $info) {
        //UNCOMMENT AND USE BELOW TO DUMP $input TO A FILE FOR DEBUGGING

        //$fp = fopen(plugin_dir_path( __FILE__ ) . 'results.json', 'w');
        //fwrite($fp, json_encode($input));
        //fclose($fp);

        try {

          $inspect_post = array(
            'post_title'    => $input['title'],
            'post_status'   => 'publish',
            'post_type'     => 'inspection',
            'post_author'   => $input['author_id'],
            'post_content'  => $input['content'],
            'post_excerpt'  => $input['share_email'],
          );

          $post_id = wp_insert_post($inspect_post, $wp_error);
          foreach ($input as $key => $value) {

            if ($key === "pass_fail") {
              $updateValue = $value ? 'pass' :  'fail';
            } else {
              $updateValue = $value;
            }

            update_field($key, $updateValue, $post_id);
          }
          return [
            'success' => true,
            'id'      => $post_id,
          ];
        } catch (Exception $e) {
          return [
            'success' => false,
            'error' => 'Sorry, inspection document creation failed, try again.'
          ];
        }
      }
    ]);
  }
}
