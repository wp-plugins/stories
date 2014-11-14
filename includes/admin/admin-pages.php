<?php
/**
 * Admin Pages
 *
 * @package     Stories
 * @subpackage  Admin/Pages
 * @copyright   Copyright (c) 2013, Bryan Monzon
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;




/**
 * Creates the admin menu pages under Donately and assigns them their global variables
 *
 * @since  0.1
 * @author Bryan Monzon
 * @global  $bm_stories_settings_page
 * @return void
 */
function bm_stories_add_menu_page() {
    global $bm_stories_settings_page;

    $bm_stories_settings_page = add_submenu_page( 'edit.php?post_type=stories', __( 'Settings', 'bm_stories' ), __( 'Settings', 'bm_stories'), 'edit_pages', 'stories-settings', 'bm_stories_settings_page' );
    
}
add_action( 'admin_menu', 'bm_stories_add_menu_page', 11 );
