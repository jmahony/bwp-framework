<?php namespace Rep\Bootstrap;
/**
 * @package WordPress
 * @subpackage Rep_Template
 *
 * Bootstrap
 *
 */

$admin_libraries = array(

  '/bwp-framework/lib/JMetaBox/build/init.php',
  '/includes/rep_meta_boxes.php',

);

$framework_libraries = array(

  '/bwp-framework/lib/NHP-Theme-Options-Framework/options/options.php',
  '/bwp-framework/defaults.php',
  '/bwp-framework/lib/mustache/src/Mustache/Autoloader.php',
  '/bwp-framework/lib/twitteroauth/twitteroauth/twitteroauth.php',
  '/bwp-framework/lib/helpers.php',
  '/bwp-framework/lib/enqueue.php',
  '/bwp-framework/lib/persistence.php'

);

$custom_libraries = array(

  '/includes/rep_theme_options.php',
  '/includes/rep_enqueue.php',
  '/includes/rep_custom_types.php',
  '/includes/rep_menus.php',
  '/includes/rep_shortcodes.php',
  '/includes/rep_sidebars.php',
  '/includes/widgets/rep_widgets.php',
  '/includes/rep_custom_styles.php'

);

/* Include stuff last */
$after = array(

  '/bwp-framework/actions.php'

);

\Rep\Core\load_admin_library( $admin_libraries );

\Rep\Core\load_library( array_merge( $framework_libraries, $custom_libraries, $after ) );

?>