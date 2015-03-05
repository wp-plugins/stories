<?php
/**
 * Post Type Functions
 *
 * @package     BM_STORIES
 * @subpackage  Functions
 * @copyright   Copyright (c) 2013, Bryan Monzon
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Registers and sets up the Downloads custom post type
 *
 * @since  0.1
 * @author Bryan Monzon
 * @return void
 */
function setup_bm_stories_post_types() {
	global $bm_stories_settings;

	//Check to see if anything is set in the settings area.
	if( !empty( $bm_stories_settings['stories_slug'] ) ) {
	    $slug = defined( 'BM_STORIES_SLUG' ) ? BM_STORIES_SLUG : $bm_stories_settings['stories_slug'];
	} else {
	    $slug = defined( 'BM_STORIES_SLUG' ) ? BM_STORIES_SLUG : 'stories';
	}

	if( !isset( $bm_stories_settings['disable_archive'] ) ) {
	    $archives = true;
	}else{
	    $archives = false;
	}

	$exclude_from_search = isset( $bm_stories_settings['exclude_from_search'] ) ? true : false;
	
	$rewrite  = defined( 'BM_STORIES_DISABLE_REWRITE' ) && BM_STORIES_DISABLE_REWRITE ? false : array('slug' => $slug, 'with_front' => false);

	$boiler_labels =  apply_filters( 'bm_stories_boiler_labels', array(
		'name' 				=> '%2$s',
		'singular_name' 	=> '%1$s',
		'add_new' 			=> __( 'Add New', 'bm_stories' ),
		'add_new_item' 		=> __( 'Add New %1$s', 'bm_stories' ),
		'edit_item' 		=> __( 'Edit %1$s', 'bm_stories' ),
		'new_item' 			=> __( 'New %1$s', 'bm_stories' ),
		'all_items' 		=> __( 'All %2$s', 'bm_stories' ),
		'view_item' 		=> __( 'View %1$s', 'bm_stories' ),
		'search_items' 		=> __( 'Search %2$s', 'bm_stories' ),
		'not_found' 		=> __( 'No %2$s found', 'bm_stories' ),
		'not_found_in_trash'=> __( 'No %2$s found in Trash', 'bm_stories' ),
		'parent_item_colon' => '',
		'menu_name' 		=> __( '%2$s', 'bm_stories' )
	) );

	foreach ( $boiler_labels as $key => $value ) {
	   $boiler_labels[ $key ] = sprintf( $value, bm_stories_get_label_singular(), bm_stories_get_label_plural() );
	}

	$boiler_args = array(
		'labels'              => $boiler_labels,
		'public'              => true,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_icon'           => 'dashicons-book-alt',
		'query_var'           => true,
		'exclude_from_search' => $exclude_from_search,
		'rewrite'             => $rewrite,
		'map_meta_cap'        => true,
		'has_archive'         => $archives,
		'show_in_nav_menus'   => true,
		'hierarchical'        => false,
		'supports'            => apply_filters( 'bm_stories_supports', array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments', 'revisions', 'author' ) ),
	);
	register_post_type( 'stories', apply_filters( 'bm_stories_post_type_args', $boiler_args ) );
	
}
add_action( 'init', 'setup_bm_stories_post_types', 1 );

/**
 * Get Default Labels
 *
 * @since  0.1
 * @author Bryan Monzon
 * @return array $defaults Default labels
 */
function bm_stories_get_default_labels() {
	global $bm_stories_settings;

	if( !empty( $bm_stories_settings['stories_label_plural'] ) || !empty( $bm_stories_settings['stories_label_singular'] ) ) {
	    $defaults = array(
	       'singular' => $bm_stories_settings['stories_label_singular'],
	       'plural' => $bm_stories_settings['stories_label_plural']
	    );
	 } else {
		$defaults = array(
		   'singular' => __( 'Story', 'bm_stories' ),
		   'plural' => __( 'Stories', 'bm_stories')
		);
	}
	
	return apply_filters( 'bm_stories_default_name', $defaults );

}

/**
 * Get Singular Label
 *
 * @since  0.1
 * @author Bryan Monzon
 * @return string $defaults['singular'] Singular label
 */
function bm_stories_get_label_singular( $lowercase = false ) {
	$defaults = bm_stories_get_default_labels();
	return ($lowercase) ? strtolower( $defaults['singular'] ) : $defaults['singular'];
}

/**
 * Get Plural Label
 *
 * @since  0.1
 * @author Bryan Monzon
 * @return string $defaults['plural'] Plural label
 */
function bm_stories_get_label_plural( $lowercase = false ) {
	$defaults = bm_stories_get_default_labels();
	return ( $lowercase ) ? strtolower( $defaults['plural'] ) : $defaults['plural'];
}

/**
 * Change default "Enter title here" input
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param string $title Default title placeholder text
 * @return string $title New placeholder text
 */
function bm_stories_change_default_title( $title ) {
     $screen = get_current_screen();

     if  ( 'bm_stories' == $screen->post_type ) {
     	$label = bm_stories_get_label_singular();
        $title = sprintf( __( 'Enter %s title here', 'bm_stories' ), $label );
     }

     return $title;
}
add_filter( 'enter_title_here', 'bm_stories_change_default_title' );

/**
 * Registers the custom taxonomies for the downloads custom post type
 *
 * @since  0.1
 * @author Bryan Monzon
 * @return void
*/
function bm_stories_setup_taxonomies() {

	$slug     = defined( 'BM_STORIES_SLUG' ) ? BM_STORIES_SLUG : 'boiler';

	/** Categories */
	$category_labels = array(
		'name' 				=> sprintf( _x( '%s Categories', 'taxonomy general name', 'bm_stories' ), bm_stories_get_label_singular() ),
		'singular_name' 	=> _x( 'Category', 'taxonomy singular name', 'bm_stories' ),
		'search_items' 		=> __( 'Search Categories', 'bm_stories'  ),
		'all_items' 		=> __( 'All Categories', 'bm_stories'  ),
		'parent_item' 		=> __( 'Parent Category', 'bm_stories'  ),
		'parent_item_colon' => __( 'Parent Category:', 'bm_stories'  ),
		'edit_item' 		=> __( 'Edit Category', 'bm_stories'  ),
		'update_item' 		=> __( 'Update Category', 'bm_stories'  ),
		'add_new_item' 		=> __( 'Add New Category', 'bm_stories'  ),
		'new_item_name' 	=> __( 'New Category Name', 'bm_stories'  ),
		'menu_name' 		=> __( 'Categories', 'bm_stories'  ),
	);

	$category_args = apply_filters( 'bm_stories_category_args', array(
			'hierarchical' 		=> true,
			'labels' 			=> apply_filters('bm_stories_category_labels', $category_labels),
			'show_ui' 			=> true,
			'query_var' 		=> 'stories_category',
			'rewrite' 			=> array('slug' => $slug . '/category', 'with_front' => false, 'hierarchical' => true ),
			'capabilities'  	=> array( 'manage_terms','edit_terms', 'assign_terms', 'delete_terms' ),
			'show_admin_column'	=> true
		)
	);
	register_taxonomy( 'stories_category', array('stories'), $category_args );
	register_taxonomy_for_object_type( 'stories_category', 'stories' );

}
add_action( 'init', 'bm_stories_setup_taxonomies', 0 );



/**
 * Updated Messages
 *
 * Returns an array of with all updated messages.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $messages Post updated message
 * @return array $messages New post updated messages
 */
function bm_stories_updated_messages( $messages ) {
	global $post, $post_ID;

	$url1 = '<a href="' . get_permalink( $post_ID ) . '">';
	$url2 = bm_stories_get_label_singular();
	$url3 = '</a>';

	$messages['bm_stories'] = array(
		1 => sprintf( __( '%2$s updated. %1$sView %2$s%3$s.', 'bm_stories' ), $url1, $url2, $url3 ),
		4 => sprintf( __( '%2$s updated. %1$sView %2$s%3$s.', 'bm_stories' ), $url1, $url2, $url3 ),
		6 => sprintf( __( '%2$s published. %1$sView %2$s%3$s.', 'bm_stories' ), $url1, $url2, $url3 ),
		7 => sprintf( __( '%2$s saved. %1$sView %2$s%3$s.', 'bm_stories' ), $url1, $url2, $url3 ),
		8 => sprintf( __( '%2$s submitted. %1$sView %2$s%3$s.', 'bm_stories' ), $url1, $url2, $url3 )
	);

	return $messages;
}
add_filter( 'post_updated_messages', 'bm_stories_updated_messages' );
