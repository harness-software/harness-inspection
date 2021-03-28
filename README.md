# harness-inspection #
**Contributors:**      Harness Software  
**Donate link:**       https://harnessup.com  
**Tags:**  
**Requires at least:** 4.4  
**Tested up to:**      4.8.1 
**Stable tag:**        0.1.0  
**License:**           GPLv2  
**License URI:**       http://www.gnu.org/licenses/gpl-2.0.html  

## Description ##

Plugin to register an Inspection Points Custom Post Type AND an Inspection Custom Post Type. Data can be queried for both CPTs through GraphQL and new Inspection CTPs can be created through a mutation.

## Installation ##

### Manual Installation ###

1. Upload the entire `/harness-inspection` directory to the `/wp-content/plugins/` directory.
2. Activate harness-inspection through the 'Plugins' menu in WordPress.

## Dependencies ##

1. [Advanced Custom Fields Pro](https://advancedcustomfields.com)
2. [WPGraphQL](https://github.com/wp-graphql/wp-graphql)
3. [WPGraphQL for Advanced Custom Fields](https://github.com/wp-graphql/wp-graphql-acf/)

## Example Query for Inspection Points CPT ##
```graphql
query{
  inspectionPoints{
    nodes{
      harnessInspectionPoint{
         locationId
        description  
      }
    }
  }
}
```
and the results of the query would be:
```json
{
  "data": {
    "inspectionPoints": {
      "nodes": [
        {
          "harnessInspectionPoint": {
            "locationId": 3,
            "description": "Gotta have some stitching."
          }
        },
        {
          "harnessInspectionPoint": {
            "locationId": 2,
            "description": "here is an even better description"
          }
        },
        {
          "harnessInspectionPoint": {
            "locationId": 1,
            "description": "Here is a neat description."
          }
        }
      ]
    }
  },
}
```
## Example Mutation to create an Inspection CPT ##
```graphql
mutation {
  makeInspection(input: {
    title: "Date + Serial Number", 
    author_id: 1, 
    author_email: "email@test.com", 
    serial_number: "1234-5678-910112", 
    inspector: "Gadget",
    date_of_inspection: "24/03/2021", 
    date_of_manufacture: "24/03/2006", 
    pass_fail: false, 
    fail_point: "Dee Ring",
    number_of_points_before_failure: 10
  }) {
    success
    error
  }
}
```
successful post creation from the mutation would return:
```json
{
  "data": {
    "makeInspection": {
      "success": true,
      "error": null
    }
  },
}
```

## Changelog ##

### 0.1.0 ###
* First release

## Upgrade Notice ##

### 0.1.0 ###
First Release
