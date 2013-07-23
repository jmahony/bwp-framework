<?php namespace Rep\Core;
/**
 * @package WordPress
 * @subpackage Rep_Template
 *
 * Republique Framework Core Functions
 *
 */

function load_library( $a = array() ) {

  foreach ( $a as $v ) {
    if ( file_exists( THEME_DIR . $v ) ) {
      require_once( THEME_DIR . $v );
    }
  }

}

function load_admin_library( $a = array() ) {

  if ( is_admin() ) load_library( $a );

}

function add_actions( $hooks = array() ) {

  foreach ( $hooks as $hook => $actions ) {
    foreach ( $actions as $action ) {
      if ( function_exists( $action ) ) {
        add_action( $hook, $action );
      }
    }
  }

}

?>