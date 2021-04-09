# harness-inspection

**Contributors:** Harness Software
**Donate link:** https://harnessup.com
**Tags:**
**Requires at least:** 4.4
**Tested up to:** 4.8.1
**Stable tag:** 0.2.0
**License:** GPLv2
**License URI:** http://www.gnu.org/licenses/gpl-2.0.html

## Description

Plugin to register an Inspection Points Custom Post Type AND an Inspection Custom Post Type. Data can be queried for both CPTs through GraphQL and new Inspection CTPs can be created through a mutation.

## Installation

### Manual Installation

1. Upload the entire `/harness-inspection` directory to the `/wp-content/plugins/` directory.
2. Activate harness-inspection through the 'Plugins' menu in WordPress.

## Dependencies

1. [Advanced Custom Fields Pro](https://advancedcustomfields.com)
2. [WPGraphQL](https://github.com/wp-graphql/wp-graphql)
3. [WPGraphQL for Advanced Custom Fields](https://github.com/wp-graphql/wp-graphql-acf/)

## Example Query for Inspection Points CPT

```graphql
query {
  inspectionPoints {
    nodes {
      title
      harnessInspectionPoint {
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
          "title": "Stitching",
          "harnessInspectionPoint": {
            "locationId": 3,
            "description": "Gotta have some stitching."
          }
        },
        {
          "title": "Grommets",
          "harnessInspectionPoint": {
            "locationId": 2,
            "description": "here is an even better description"
          }
        },
        {
          "title": "Nylon Webbing",
          "harnessInspectionPoint": {
            "locationId": 1,
            "description": "Here is a neat description."
          }
        }
      ]
    }
  }
}
```

## Example Mutation to create an Inspection CPT

```graphql
mutation Make($input: MakeInspectionInput!) {
  makeInspection(input: $input) {
    success
    id
    error
  }
}
```

example variables:

```json
{
  "input": {
     "author_email": "email@author.com",
     "author_id": 1,
     "content": "Email content",
     "date_of_inspection": "today",
     "date_of_manufacture": "yesterday",
     "inspector": "Big Bob",
     "title": "serial# and date2",
     "serial_number": "#4325324523",
     "pass_fail": true,
     "share_email": "email@share.com"
    }
  }
```

successful post creation from the mutation would return:

```json
{
  "data": {
    "makeInspection": {
      "success": true,
      "id": "some id",
      "error": null
    }
  }
}
```

## Changelog

### 0.1.0

- First release

### 0.2.0

- Add share_email field
- Add descriptions to input fields on makeInspection mutation
- Update author_id to ID type

## Upgrade Notice

### 0.1.0

First Release
