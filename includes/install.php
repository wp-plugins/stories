<?php
/**
 * Install Function
 *
 * @package     BM_STORIES
 * @subpackage  Functions/Install
 * @copyright   Copyright (c) 2014, Bryan Monzon
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Install
 *
 * Runs on plugin install by setting up the post types, custom taxonomies,
 * flushing rewrite rules to initiate the new 'boiler' slug and also
 * creates the plugin and populates the settings fields for those plugin
 * pages. After successful install, the user is redirected to the BM_STORIES Welcome
 * screen.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @global $wpdb
 * @global $bm_stories_settings
 * @global $wp_version
 * @return void
 */
function bm_stories_install() {
    global $wpdb, $bm_stories_settings, $wp_version;

    // Setup the Downloads Custom Post Type
    setup_bm_stories_post_types();

    // Setup the Download Taxonomies
    bm_stories_setup_taxonomies();

    // Clear the permalinks
    flush_rewrite_rules();

    // Add Upgraded From Option
    $current_version = get_option( 'bm_stories_version' );
    if ( $current_version ) {
        update_option( 'bm_stories_version_upgraded_from', $current_version );
    }

    // Bail if activating from network, or bulk
    if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
        return;
    }

    // Add the transient to redirect
    set_transient( '_bm_stories_activation_redirect', true, 30 );
}
register_activation_hook( BM_STORIES_PLUGIN_FILE, 'bm_stories_install' );

/**
 * Post-installation
 *
 * Runs just after plugin installation and exposes the
 * bm_stories_after_install hook.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @return void
 */
function bm_stories_after_install() {

    if ( ! is_admin() ) {
        return;
    }

    $activation_pages = get_transient( '_bm_stories_activation_pages' );

    // Exit if not in admin or the transient doesn't exist
    if ( false === $activation_pages ) {
        return;
    }

    // Delete the transient
    delete_transient( '_bm_stories_activation_pages' );

    do_action( 'bm_stories_after_install', $activation_pages );
}
add_action( 'admin_init', 'bm_stories_after_install' );