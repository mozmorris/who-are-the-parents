Who Are The Parents?
====================

An ExpressionEngine plugin to fetch the parents of a given category

Installation
------------

Copy the `who_are_the_parents` folder to `expressionengine/third_party`

Usage
-----

**Parameters**

`cat_id` - int, category id to fetch parent  
`cat_url_title` - string, cat_url_title to fetch parent  

**Tag Example**

    {exp:who_are_the_parents:parent cat_url_title="example-url-title"}
      {cat_id}
      {cat_name}
      {cat_url_title}
      {cat_description}
      {cat_image}
    {exp:who_are_the_parents:parent}
