<?php
/**
 * Who Are The Parents?
 *
 * An ExpressionEngine plugin to fetch the parents of a given category
 *
 * @author Moz Morris <moz@earthview.co.uk>
 * @link httphttps://github.com/MozMorris
 * @copyright (c) 2012 Moz Morris
 * @license MIT License - http://www.opensource.org/licenses/mit-license.php
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
  'pi_name' => 'Who are the Parents?',
  'pi_version' =>'0.1',
  'pi_author' =>'Moz Morris',
  'pi_author_url' => 'https://github.com/MozMorris',
  'pi_description' => 'A better way of getting parent category information',
  'pi_usage' => Who_are_the_parents::usage()
);

class Who_are_the_parents {

  /**
   * constructor
   */
  function __construct()
  {
    $this->EE =& get_instance();
  }

  /**
   * parent tag
   */
  function parent()
  {
    // expressionengine vars
    $TMPL  = $this->EE->TMPL;
    $DB    = $this->EE->db;
    $PREFS = $this->EE->config;
    $FNS   = $this->EE->functions;

    // configure the find conditions
    if (is_numeric($TMPL->fetch_param('category')))
    {
      // find by cat_id
      $conditions = array(
        'field'    => 'cat_id',
        'category' => $TMPL->fetch_param('category') ? $TMPL->fetch_param('category') : '0'
      );

      if ($conditions['category'] <= 0)
      {
        return $TMPL->no_results();
      }
    }
    else if (is_string($TMPL->fetch_param('cat_url_title')))
    {
      // find by category_url_title
      $conditions = array(
        'field'    => 'cat_url_title',
        'category' => $TMPL->fetch_param('cat_url_title')
      );
    }
    else
    {
      // default settings
      $conditions = array(
        'field'    => 'cat_id',
        'category' => 0
      );
    }

    // get the tag data used
    $tagdata = $TMPL->tagdata;

    // perform tree traverse
    $data = $this->_parent($conditions);

    // return empty set
    if (empty($data))
    {
      return $TMPL->no_results();
    }

    // populate tags
    foreach ($data[0] as $key=>$val)
    {
      $tagdata = $TMPL->swap_var_single($key, $val, $tagdata);
    }

    // return data
    $tagdata = $FNS->prep_conditionals($tagdata, array());
    $this->return_data = $tagdata;
    return $this->return_data;
  }

  /**
   * traverses the category tree
   */
  private function _parent($conditions)
  {
    // get db conection
    $DB = $this->EE->db;

    // cat_id OR url_title find
    if (is_array($conditions))
    {
      $q = $DB->query("SELECT * FROM exp_categories WHERE {$conditions['field']} = '{$conditions['category']}'");
    } else {
      $q = $DB->query("SELECT * FROM exp_categories WHERE cat_id='$conditions'");
    }

    if ($q->num_rows == 0)
    {
      return false;
    }

    if (($parent_id = $q->row('parent_id')) == 0)
    {
      return $q->result_array();
    }

    return $this->_parent($parent_id);
  }

  /**
   * usage instuctions
   */
  function usage()
  {
    ob_start();
    ?>
    This plugin lets you fetch info about parents of a given category
    Parameters:
    cat_id - category id to fetch parent
    cat_url_title - cat_url_title to fetch parent

    {exp:who_are_the_parents:parent cat_url_title="example-url-title"}
    {cat_id}
    {cat_name}
    {cat_url_title}
    {cat_description}
    {cat_image}
    {exp:who_are_the_parents:parent}
    <?php
    $buffer = ob_get_contents();
    ob_end_clean();
    return $buffer;
  }
}
?>