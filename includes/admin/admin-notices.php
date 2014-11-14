<?php
/**
 * Admin Notices
 *
 * @package     Stories
 * @subpackage  Admin/Notices
 * @copyright   Copyright (c) 2013, Bryan Monzon
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Admin Messages
 *
 * @since  0.1
 * @author Bryan Monzon
 * @global $bm_stories_settings Array of all the BM_STORIES Options
 * @return void
 */
function bm_stories_admin_messages() {
    global $bm_stories_settings;

    settings_errors( 'bm_stories-notices' );
}
add_action( 'admin_notices', 'bm_stories_admin_messages' );


/**
 * Dismisses admin notices when Dismiss links are clicked
 *
 * @since 1.8
 * @return void
*/
function bm_stories_dismiss_notices() {

    $notice = isset( $_GET['bm_stories_notice'] ) ? $_GET['bm_stories_notice'] : false;

    if( ! $notice )
        return; // No notice, so get out of here

    update_user_meta( get_current_user_id(), '_bm_stories_' . $notice . '_dismissed', 1 );

    wp_redirect( remove_query_arg( array( 'bm_stories_action', 'bm_stories_notice' ) ) ); exit;

}
add_action( 'bm_stories_dismiss_notices', 'bm_stories_dismiss_notices' );
