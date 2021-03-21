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

Plugin to register an Inspection Custom Post Type and associate it with some ACF fields, all of which can be queried with GraphQL.

## Installation ##

### Manual Installation ###

1. Upload the entire `/harness-inspection` directory to the `/wp-content/plugins/` directory.
2. Activate harness-inspection through the 'Plugins' menu in WordPress.

## Dependencies ##

1. [Advanced Custom Fields Pro](https://advancedcustomfields.com)
2. [WPGraphQL](https://github.com/wp-graphql/wp-graphql)
3. [WPGraphQL for Advanced Custom Fields](https://github.com/wp-graphql/wp-graphql-acf/)

## Example Query ##

```graphql
{
  query MyQuery {
  inspections {
    nodes {
      title
      id
      fields {
        deePad
        deeRing
        stitching
        tongueBuckle
      }
    }
  }
}
```

and the results of the query would be:

```json
{
  "data": {
    "inspections": {
      "nodes": [
        {
          "title": "Test",
          "id": "cG9zdDo3",
          "fields": {
            "deePad": "yes",
            "deeRing": "not_applicable",
            "stitching": "no",
            "tongueBuckle": "not_applicable"
          }
        }
      ]
    }
  }
}
```

## Changelog ##

### 0.1.0 ###
* First release

## Upgrade Notice ##

### 0.1.0 ###
First Release
