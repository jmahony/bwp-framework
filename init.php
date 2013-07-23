<?php
/**
 * @package WordPress
 * @subpackage Rep_Template
 *
 * Republique Framework Initilisation
 *
 */

// Get the root directory of the theme
if ( !defined( 'THEME_DIR' ) ) define( 'THEME_DIR', get_stylesheet_directory() );

// Get the current theme URL
if ( !defined( 'CURRENT_THEME_URL' ) ) {

  if ( is_child_theme() ) {

    define( 'CURRENT_THEME_URL', dirname( get_stylesheet_uri() ) );

  } else {

    define( 'CURRENT_THEME_URL', get_template_directory_uri() );

  }

}

if ( !defined('FRAMEWORK_URL') )  define('FRAMEWORK_URL', CURRENT_THEME_URL . '/framework');

if ( !defined('LIBRARY_URL') )  define('LIBRARY_URL', FRAMEWORK_URL . '/lib');

define( 'ASSETS_URL', CURRENT_THEME_URL . '/assets' );

// Load core functions
require_once( 'core.php' );

\Rep\Core\load_library( array( '/framework/bootstrap.php' ) );

// Register Mustache
Mustache_Autoloader::register();

?>