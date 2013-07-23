<?php namespace Rep\Helpers;
/**
 * @package WordPress
 * @subpackage Template
 *
 * Helper functions
 *
 */


/**
 * excerpt
 *
 * Returns an excerpt at the speicfied length
 *
 * @param Int $limit
 * @return String
 **/
function excerpt( $length, $post_id = null ) {

	if ( !$post_id ) {
		global $post;
	} else {
		$post = get_post( intval( $post_id ) );
	}

	$excerpt = explode( ' ', $post->post_content, $length );

	array_pop( $excerpt );

	$excerpt = implode( ' ', $excerpt ) . '...';

	$excerpt = preg_replace( '`\[[^\]]*\]`', '', $excerpt );
	$excerpt = preg_replace('`/<[^>]*>/`', '', $excerpt);
	$excerpt = strip_tags( $excerpt );

	return $excerpt;

}


/**
 * check_dependencies
 *
 * Replaces a custom URL placeholder with the URL to the latest post
 *
 * @param Array $dependencies
 **/
function check_dependencies( $dependencies = null ) {

	if ( !$dependencies ) return false;

	foreach ( $dependencies as $name => $location ) {
		if ( !is_plugin_active($location) ) show_warning( $name );
	}

}

/**
 * show_warning
 *
 * Displayed a warning with the supplied text
 *
 * @param String $text
 * @return String
 **/
function show_warning( $text = null ) {

	printf( '<div class="container"><div class="row"><div class="span12"><div class="alert warning">You need to activate the %s plugin</div></div></div></div>', $text );

}

/**
 * is_plugin_active
 *
 * Checks whether a plugin is active or not
 *
 * @param String plugin
 * @return Bool
 **/
function is_plugin_active( $plugin = null ) {

	$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );

	if ( in_array( $plugin,  $active_plugins ) ) {
		return true;
	} else {
		return false;
	}

}

/**
 * get_related_categories
 *
 * Returns an array of siblings and any parents children of the supplied post
 *
 * @param Int $post_id
 * @return Array
 **/
function get_related_categories( $post_id = null ) {

	$member_categories = wp_get_post_categories( $post_id );

	$search_categories = $member_categories;

	foreach ( $member_categories as $category_id ) {

		$category = get_category( $category_id );

		if ( $category->parent != 0 && !in_array( $category->parent, $search_categories ) ) {

			array_push( $search_categories, $category->parent );

			$child_categories = get_categories( array( 'child_of' => $category->parent ) );

			foreach ( $child_categories as $category ) {

				if ( !in_array( $category->cat_ID, $search_categories ) ) {
					array_push( $search_categories, $category->cat_ID );
				}

			}

		}

	}

	return $search_categories;

}


/**
 * get_thumbnail
 *
 * Returns a posts thumbnail at the specified size, if a thumbnail is not
 * available, a placehold.it placeholder will be returned at the specified
 * size.
 *
 * @param Int $post_id
 * @param String $size
 * @return String
 **/
function get_thumbnail( $post_id = null, $size = 'full' ) {

	if ( !$post_id ) return false;

	$thumbnail = null;

	global $image_sizes;

	if ( has_post_thumbnail( $post_id ) ) {

		$thumbnail = get_the_post_thumbnail(
			$post_id,
			$size,
			array(
				'alt'   => get_the_title( $post_id ),
				'class' => 'feature-image ' . $size
			)
		);

	}

	if ( isset( $image_sizes ) && !$thumbnail  ) {
		if ( array_key_exists( $size, $image_sizes ) ) {

			$thumbnail = sprintf(
				'<img width="%1$d" height="%2$d" class="feature-image %4$s" src="http://placehold.it/%1$dx%2$d" alt="%3$s" />',
			  $image_sizes[$size]['width'],
			  $image_sizes[$size]['height'],
			  __( 'Placeholder for ' . get_the_title( $post_id ), 'rep' ),
			  $size
			);

		}
	}

	if ( !$thumbnail ) {
		$thumbnail = '<img width="100%" height="100%" class="feature-image"
			src="http://placehold.it/100x100" alt="placeholder" />';
	}

	return $thumbnail;

}

/**
 * set_image_sizes
 *
 * Takes an array of image sizes and registers them with WordPress
 *
 * @param Array $image_sizes
 * @return void
 **/
function set_image_sizes( $image_sizes = null ) {

	if ( !$image_sizes ) return false;

	foreach ( $image_sizes as $name => $size ) {

		$crop = isset( $size['crop'] ) ? $size['crop'] : false;

		add_image_size( $name, $size['width'], $size['height'], $crop );

	}

}

/**
 * post_type_labels
 *
 * Just generates an array for custom post type labels
 *
 * @param String $singular
 * @param String $plural
 * @param String $prepend
 * @param String $append
 * @return Array
 **/
function post_type_labels( $singular = null, $plural = null, $prepend = null, $append = null) {

	if ( $plural === null ) {
		$plural = $singular . 's';
	} else {
		$plural = $singular;
	}

	$plural = ucwords( $plural );
	$singular = ucwords( $singular );

  return array(
		'name'               => _x( $prepend . $plural . $append, 'post type general name', 'rep' ),
		'singular_name'      => _x( $singular, 'post type singular name', 'rep' ),
		'add_new'            => __( 'Add New', 'rep' ),
		'add_new_item'       => sprintf( __( 'Add New %s%s%s', 'rep' ), $prepend, $singular, $append ),
		'edit_item'          => sprintf( __( 'Edit %s%s%s', 'rep' ), $prepend, $singular, $append ),
		'new_item'           => sprintf( __( 'New %s%s%s', 'rep' ), $prepend, $singular, $append ),
		'view_item'          => sprintf( __( 'View %s%s%s', 'rep' ), $prepend, $singular, $append ),
		'search_items'       => sprintf( __( 'Search %s%s%s', 'rep' ), $prepend, $singular, $append ),
		'not_found'          => sprintf( __( 'No %s%s%s found', 'rep' ), $prepend, $plural, $append ),
		'not_found_in_trash' => sprintf( __( 'No %s%s%s found in Trash', 'rep' ), $prepend, $plural, $append ),
		'parent_item_colon'  => ''
  );
}

/**
 * is_blog
 *
 *
 * @return Bool
 **/
function is_blog() {

	global $post;

	$is_blog = (bool) false;

	if ( is_archive() || is_author() || is_category() || is_home() || is_single() || is_tag() ) {
		$posttype = get_post_type( $post );
		if ( $posttype == 'post' ) {
			$is_blog = true;
		}
	}

	return $is_blog;

}

/**
 * blog_page_url
 *
 * Returns the current blog page URL
 *
 * @return String
 **/
function blog_page_url() {

	if ( get_option( 'show_on_front' ) == 'page' ) {
		return get_permalink( get_option( 'page_for_posts' ) );
	} else {
		return bloginfo( 'url' );
	}

}

/**
 * get_thumbnail_url
 *
 *
 * @param WP_Post
 * @return Bool
 **/
function get_thumbnail_url( \WP_Post $post ) {

	if ( !$post ) global $post;

	return wp_get_attachment_url( get_post_thumbnail_id( $post->ID ) );

}

/**
 * get_post_array
 *
 * Returns an array of posts of the given type in the format
 * array(
 *   post_id => post_name
 * )
 *
 * @param String $post_type
 * @return Array
 **/
function get_post_array( $post_type = 'post' ) {

	if ( !$post_type ) return null;

	$return_array = array();

	$args = array(
		'post_type'      => $post_type,
		'posts_per_page' => -1
	);

	$posts = get_posts( $args );

	if ( !$posts ) return null;

	foreach ( $posts as $post ) {
		$return_array[$post->ID] = $post->post_title;
	}

	return $return_array;

}

/**
 * post_drop_down
 *
 * Returns a dropdown menu of posts for the given type
 *
 * @param String $post_type
 * @param Int $current_id
 * @param Boolean $placeholder
 * @return String
 **/
function post_drop_down( $post_type = 'post', $current_id = null, $placeholder = false ) {

	$posts = get_post_array( $post_type );

	$post_options = (string) '';


	if ( $placeholder ) {
		$post_options .= sprintf(
			'<option value="">- Select a %s -</option>',
			ucwords( $post_type )
		);
	}

	if ( $posts ) {
		foreach ( $posts as $post_id => $post ) {
			$selected = ( $post_id == $current_id ) ? ' selected' : '';
			$post_options .= sprintf(
				'<option value="%d"%s>%s</option>',
				$post_id,
				$selected,
				esc_html( $post )
			);
		}
	} else {
		$post_options = '<option value="">No Pages Found</option>';
	}

	return $post_options;

}

/**
 * sixteen_nine
 *
 * Returns 16:9 ratio depending on the parameter width
 *
 * @param Int $width
 * @return Array
 **/
function sixteen_nine($width = 0) {

	return array(
		'width'  => $width,
		'height' => round(($width / 16) * 9)
	);

}
/**
 * is_admin_user
 *
 * Check whether a user is admin or not
 *
 * @return Boolean
 **/
function is_admin_user() {

	return current_user_can( 'manage_options' );

}

/**
 * script_force_footer
 *
 * EXPTERIMENTAL
 *
 * @return Void
 **/
function scripts_force_footer( $scripts = array() ) {

	global $wp_scripts;

	$to_footer = array(
		'jquery-form',
		'jquery-color',
		'jquery-ui-core',
		'jquery-ui-widget',
		'jquery-ui-mouse',
		'jquery-ui-accordion',
		'jquery-ui-autocomplete',
		'jquery-ui-slider',
		'jquery-ui-tabs',
		'jquery-ui-sortable',
		'jquery-ui-draggable',
		'jquery-ui-droppable',
		'jquery-ui-selectable',
		'jquery-ui-position',
		'jquery-ui-datepicker ',
		'jquery-ui-resizable',
		'jquery-ui-dialog',
		'jquery-ui-button',
		'comment-reply'
	);

	$to_footer = array_merge( $scripts, $to_footer );

	foreach ( $wp_scripts->registered as $script ) {
		if ( in_array( $script->handle, $to_footer ) ) {
			$wp_scripts->add_data( $script->handle, 'group', 1 );
		}
	}

}

/**
 * in_past
 *
 * Returns whether the param date is in the past or not
 *
 * @param String date
 * @return Bool
 **/
function in_past( $dt = null ) {

	$dt = new \DateTime( $dt );

	$now = new \DateTime();

	return $dt < $now;

}

/**
 * is_today
 *
 * Returns whether the param date is today
 *
 * @param String date
 * @return Bool
 **/
function is_today( $dt = null ) {

	$dt = new \DateTime( $dt );

	$now = new \DateTime();

	return $dt->format( 'Y-m-d' ) == $now->format( 'Y-m-d' );

}

/**
 * in_future
 *
 * Returns whether the param date is in the future
 *
 * @param String date
 * @return Bool
 **/
function in_future( $dt = null ) {

	return !in_past( $dt );

}

/**
 * get_tweets
 *
 * Returns tweets
 *
 * @param Int count
 * @return Mixed
 **/
function get_tweets( $count = 1, $un = null ) {

	$tweets = null;

	if (!$un) return null;

	$url = sprintf( 'http://api.twitter.com/1/statuses/user_timeline/%s.json?&count=%d', $un,  intval( $count ) );

	$json = @file_get_contents( $url );

	if ($json !== false) {
		$tweets = json_decode( $json );
	} else {
		$tweets = 'Twitter feed fail';
	}

	return $tweets;

}

/**
 * get_mustache
 *
 * Returns an instance of Mustache_Engine with the frameworks default loader
 *
 * @return Mustache_Engine
 **/
function get_mustache() {

	global $RepMustacheEngine;

	if ( $RepMustacheEngine instanceof Mustache_Engine) return $RepMustacheEngine;

	return $RepMustacheEngine = new Mustache_Engine( array(
    'loader' => new Mustache_Loader_FilesystemLoader( dirname(__FILE__) . '/includes/views' )
	) );

}

?>