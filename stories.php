<?php 
/**
 * Plugin Name: Stories
 * Plugin URI: http://wordpress.org/plugins/stories
 * Description: Builds a story post type
 * Version: 1.1
 * Author: Bryan Monzon
 * Author URI: https://profiles.wordpress.org/bryanmonzon/
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'BM_STORIES' ) ) :


/**
 * Main BM_STORIES Class
 *
 * @since 1.0 */
final class BM_STORIES {

  /**
   * @var BM_STORIES Instance
   * @since 1.0
   */
  private static $instance;


  /**
   * BM_STORIES Instance / Constructor
   *
   * Insures only one instance of BM_STORIES exists in memory at any one
   * time & prevents needing to define globals all over the place. 
   * Inspired by and credit to BM_STORIES.
   *
   * @since 1.0
   * @static
   * @uses BM_STORIES::setup_globals() Setup the globals needed
   * @uses BM_STORIES::includes() Include the required files
   * @uses BM_STORIES::setup_actions() Setup the hooks and actions
   * @see BM_STORIES()
   * @return void
   */
  public static function instance() {
    if ( ! isset( self::$instance ) && ! ( self::$instance instanceof BM_STORIES ) ) {
      self::$instance = new BM_STORIES;
      self::$instance->setup_constants();
      self::$instance->includes();
      // self::$instance->load_textdomain();
      // use @examples from public vars defined above upon implementation
    }
    return self::$instance;
  }



  /**
   * Setup plugin constants
   * @access private
   * @since 1.0 
   * @return void
   */
  private function setup_constants() {
    // Plugin version
    if ( ! defined( 'BM_STORIES_VERSION' ) )
      define( 'BM_STORIES_VERSION', '1.1' );

    // Plugin Folder Path
    if ( ! defined( 'BM_STORIES_PLUGIN_DIR' ) )
      define( 'BM_STORIES_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

    // Plugin Folder URL
    if ( ! defined( 'BM_STORIES_PLUGIN_URL' ) )
      define( 'BM_STORIES_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

    // Plugin Root File
    if ( ! defined( 'BM_STORIES_PLUGIN_FILE' ) )
      define( 'BM_STORIES_PLUGIN_FILE', __FILE__ );

    if ( ! defined( 'BM_STORIES_DEBUG' ) )
      define ( 'BM_STORIES_DEBUG', true );
  }



  /**
   * Include required files
   * @access private
   * @since 1.0
   * @return void
   */
  private function includes() {
    global $bm_stories_settings, $wp_version;

    require_once BM_STORIES_PLUGIN_DIR . '/includes/admin/settings/register-settings.php';
    $bm_stories_settings = bm_stories_get_settings();

    // Required Plugin Files
    require_once BM_STORIES_PLUGIN_DIR . '/includes/functions.php';
    require_once BM_STORIES_PLUGIN_DIR . '/includes/posttypes.php';
    require_once BM_STORIES_PLUGIN_DIR . '/includes/scripts.php';
    require_once BM_STORIES_PLUGIN_DIR . '/includes/shortcodes.php';

    if( is_admin() ){
        //Admin Required Plugin Files
        require_once BM_STORIES_PLUGIN_DIR . '/includes/admin/admin-pages.php';
        require_once BM_STORIES_PLUGIN_DIR . '/includes/admin/admin-notices.php';
        require_once BM_STORIES_PLUGIN_DIR . '/includes/admin/settings/display-settings.php';

    }

    require_once BM_STORIES_PLUGIN_DIR . '/includes/install.php';


  }

} /* end BM_STORIES class */
endif; // End if class_exists check


/**
 * Main function for returning BM_STORIES Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $sqcash = BM_STORIES(); ?>
 *
 * @since 1.0
 * @return object The one true BM_STORIES Instance
 */
function BM_STORIES() {
  return BM_STORIES::instance();
}


/**
 * Initiate
 * Run the BM_STORIES() function, which runs the instance of the BM_STORIES class.
 */
BM_STORIES();



/**
 * Debugging
 * @since 1.0
 */
if ( BM_STORIES_DEBUG ) {
  ini_set('display_errors','On');
  error_reporting(E_ALL);
}


