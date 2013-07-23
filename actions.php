<?php namespace Rep\Actions;
/**
 * @package WordPress
 * @subpackage Rep_Template
 *
 * Actions
 *
 */

$hooks = array(

  'admin_enqueue_scripts' => array(
    'rep_enqueue_styles_admin'
  ),

  'wp_enqueue_scripts'    => array(
    'rep_enqueue_scripts',
    'rep_enqueue_styles'
  ),

  'init'                  => array(
    'rep_create_taxonomies',
    'rep_create_post_type',
    'setup_framework_options'
  ),

  'widgets_init'          => array(
    'rep_sidebars_init'
  ),

  'admin_enqueue_scripts' => array(
    'Rep\\Enqueue\\enqueue_styles_admin'
  )

);

if ( STYLE_KEY ) $hooks['wp_head'][] = 'add_styles';

if ( INCLUDE_JS ) $hooks['wp_enqueue_scripts'][] = 'Rep\\Enqueue\\enqueue_scripts';

if ( INCLUDE_CSS ) $hooks['wp_enqueue_scripts'][] = 'Rep\\Enqueue\\enqueue_styles';

\Rep\Core\add_actions( $hooks );

?>