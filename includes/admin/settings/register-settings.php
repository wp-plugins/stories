<?php
/**
 * Register Settings
 *
 * @package     Stories
 * @subpackage  Admin/Settings
 * @copyright   Copyright (c) 2013, Bryan Monzon
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * Get an option
 *
 * Looks to see if the specified setting exists, returns default if not
 *
 * @since  0.1
 * @author Bryan Monzon
 * @return mixed
 */
function bm_stories_get_option( $key = '', $default = false ) {
    global $bm_stories_settings;
    return isset( $bm_stories_settings[ $key ] ) ? $bm_stories_settings[ $key ] : $default;
}

/**
 * Get Settings
 *
 * Retrieves all plugin settings
 *
 * @since  0.1
 * @author Bryan Monzon
 * @return array BM_STORIES settings
 */
function bm_stories_get_settings() {

    $settings = get_option( 'bm_stories_settings' );
    if( empty( $settings ) ) {

        // Update old settings with new single option

        $general_settings = is_array( get_option( 'bm_stories_settings_general' ) )    ? get_option( 'bm_stories_settings_general' )      : array();


        $settings = array_merge( $general_settings );

        update_option( 'bm_stories_settings', $settings );
    }
    return apply_filters( 'bm_stories_get_settings', $settings );
}

/**
 * Add all settings sections and fields
 *
 * @since  0.1
 * @author Bryan Monzon
 * @return void
*/
function bm_stories_register_settings() {

    if ( false == get_option( 'bm_stories_settings' ) ) {
        add_option( 'bm_stories_settings' );
    }

    foreach( bm_stories_get_registered_settings() as $tab => $settings ) {

        add_settings_section(
            'bm_stories_settings_' . $tab,
            __return_null(),
            '__return_false',
            'bm_stories_settings_' . $tab
        );

        foreach ( $settings as $option ) {
            add_settings_field(
                'bm_stories_settings[' . $option['id'] . ']',
                $option['name'],
                function_exists( 'bm_stories_' . $option['type'] . '_callback' ) ? 'bm_stories_' . $option['type'] . '_callback' : 'bm_stories_missing_callback',
                'bm_stories_settings_' . $tab,
                'bm_stories_settings_' . $tab,
                array(
                    'id'      => $option['id'],
                    'desc'    => ! empty( $option['desc'] ) ? $option['desc'] : '',
                    'name'    => $option['name'],
                    'section' => $tab,
                    'size'    => isset( $option['size'] ) ? $option['size'] : null,
                    'options' => isset( $option['options'] ) ? $option['options'] : '',
                    'std'     => isset( $option['std'] ) ? $option['std'] : ''
                )
            );
        }

    }

    // Creates our settings in the options table
    register_setting( 'bm_stories_settings', 'bm_stories_settings', 'bm_stories_settings_sanitize' );

}
add_action('admin_init', 'bm_stories_register_settings');

/**
 * Retrieve the array of plugin settings
 *
 * @since  0.1
 * @author Bryan Monzon
 * @return array
*/
function bm_stories_get_registered_settings() {

    $pages = get_pages();
    $pages_options = array( 0 => '' ); // Blank option
    if ( $pages ) {
        foreach ( $pages as $page ) {
            $pages_options[ $page->ID ] = $page->post_title;
        }
    }

    /**
     * 'Whitelisted' BM_STORIES settings, filters are provided for each settings
     * section to allow extensions and other plugins to add their own settings
     */
    $bm_stories_settings = array(
        /** General Settings */
        'general' => apply_filters( 'bm_stories_settings_general',
            array(
                'basic_settings' => array(
                    'id' => 'basic_settings',
                    'name' => '<strong>' . __( 'Basic Settings', 'bm_stories' ) . '</strong>',
                    'desc' => '',
                    'type' => 'header'
                ),
                'stories_slug' => array(
                    'id' => 'stories_slug',
                    'name' => __( bm_stories_get_label_plural() . ' URL Slug', 'bm_stories' ),
                    'desc' => __( 'Enter the slug you would like to use for your ' . strtolower( bm_stories_get_label_plural() ) . '. (<em>You will need to <a href="' . admin_url( 'options-permalink.php' ) . '">refresh permalinks</a>, after saving changes</em>).'  , 'bm_stories' ),
                    'type' => 'text',
                    'size' => 'medium',
                    'std' => strtolower( bm_stories_get_label_plural() )
                ),
                'stories_label_plural' => array(
                    'id' => 'stories_label_plural',
                    'name' => __( bm_stories_get_label_plural() . ' Label Plural', 'bm_stories' ),
                    'desc' => __( 'Enter the label you would like to use for your ' . strtolower( bm_stories_get_label_plural() ) . '.', 'bm_stories' ),
                    'type' => 'text',
                    'size' => 'medium',
                    'std' => bm_stories_get_label_plural()
                ),
                'stories_label_singular' => array(
                    'id' => 'stories_label_singular',
                    'name' => __( bm_stories_get_label_singular() . ' Label Singular', 'bm_stories' ),
                    'desc' => __( 'Enter the label you would like to use for your ' . strtolower( bm_stories_get_label_singular() ) . '.', 'bm_stories' ),
                    'type' => 'text',
                    'size' => 'medium',
                    'std' => bm_stories_get_label_singular()
                ),
                'disable_archive' => array(
                    'id' => 'disable_archive',
                    'name' => __( 'Disable Archives Page', 'bm_stories' ),
                    'desc' => __( 'Check to disable archives page. (<em>You might need to <a href="' . admin_url( 'options-permalink.php' ) . '">refresh permalinks</a>, after saving changes</em>).', 'bm_stories' ),
                    'type' => 'checkbox',
                    'std' => ''
                ),
                'exclude_from_search' => array(
                    'id' => 'exclude_from_search',
                    'name' => __( 'Exclude from Search', 'bm_stories' ),
                    'desc' => __( 'Check to exclude from search. (<em>You might need to <a href="' . admin_url( 'options-permalink.php' ) . '">refresh permalinks</a>, after saving changes</em>)', 'bm_stories' ),
                    'type' => 'checkbox',
                    'std' => ''
                ),
            )
        ),
        
    );

    return $bm_stories_settings;
}

/**
 * Header Callback
 *
 * Renders the header.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @return void
 */
function bm_stories_header_callback( $args ) {
    $html = '<label for="bm_stories_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';
    echo $html;
}

/**
 * Checkbox Callback
 *
 * Renders checkboxes.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $bm_stories_settings Array of all the BM_STORIES Options
 * @return void
 */
function bm_stories_checkbox_callback( $args ) {
    global $bm_stories_settings;

    $checked = isset($bm_stories_settings[$args['id']]) ? checked(1, $bm_stories_settings[$args['id']], false) : '';
    $html = '<input type="checkbox" id="bm_stories_settings_' . $args['section'] . '[' . $args['id'] . ']" name="bm_stories_settings_' . $args['section'] . '[' . $args['id'] . ']" value="1" ' . $checked . '/>';
    $html .= '<label for="bm_stories_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Multicheck Callback
 *
 * Renders multiple checkboxes.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $bm_stories_settings Array of all the BM_STORIES Options
 * @return void
 */
function bm_stories_multicheck_callback( $args ) {
    global $bm_stories_settings;

    foreach( $args['options'] as $key => $option ):
        if( isset( $bm_stories_settings[$args['id']][$key] ) ) { $enabled = $option; } else { $enabled = NULL; }
        echo '<input name="bm_stories_settings_' . $args['section'] . '[' . $args['id'] . '][' . $key . ']" id="bm_stories_settings_' . $args['section'] . '[' . $args['id'] . '][' . $key . ']" type="checkbox" value="' . $option . '" ' . checked($option, $enabled, false) . '/>&nbsp;';
        echo '<label for="bm_stories_settings_' . $args['section'] . '[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br/>';
    endforeach;
    echo '<p class="description">' . $args['desc'] . '</p>';
}

/**
 * Radio Callback
 *
 * Renders radio boxes.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $bm_stories_settings Array of all the BM_STORIES Options
 * @return void
 */
function bm_stories_radio_callback( $args ) {
    global $bm_stories_settings;

    foreach ( $args['options'] as $key => $option ) :
        $checked = false;

        if ( isset( $bm_stories_settings[ $args['id'] ] ) && $bm_stories_settings[ $args['id'] ] == $key )
            $checked = true;
        elseif( isset( $args['std'] ) && $args['std'] == $key && ! isset( $bm_stories_settings[ $args['id'] ] ) )
            $checked = true;

        echo '<input name="bm_stories_settings_' . $args['section'] . '[' . $args['id'] . ']"" id="bm_stories_settings_' . $args['section'] . '[' . $args['id'] . '][' . $key . ']" type="radio" value="' . $key . '" ' . checked(true, $checked, false) . '/>&nbsp;';
        echo '<label for="bm_stories_settings_' . $args['section'] . '[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br/>';
    endforeach;

    echo '<p class="description">' . $args['desc'] . '</p>';
}



/**
 * Text Callback
 *
 * Renders text fields.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $bm_stories_settings Array of all the BM_STORIES Options
 * @return void
 */
function bm_stories_text_callback( $args ) {
    global $bm_stories_settings;

    if ( isset( $bm_stories_settings[ $args['id'] ] ) )
        $value = $bm_stories_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<input type="text" class="' . $size . '-text" id="bm_stories_settings_' . $args['section'] . '[' . $args['id'] . ']" name="bm_stories_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
    $html .= '<label for="bm_stories_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}


/**
 * BM_STORIES Hidden Text Field Callback
 *
 * Renders text fields (Hidden, for necessary values in bm_stories_settings in the wp_options table)
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $bm_stories_settings Array of all the BM_STORIES Options
 * @return void
 * @todo refactor it is not needed entirely
 */
function bm_stories_hidden_callback( $args ) {
    global $bm_stories_settings;

    $hidden = isset($args['hidden']) ? $args['hidden'] : false;

    if ( isset( $bm_stories_settings[ $args['id'] ] ) )
        $value = $bm_stories_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<input type="hidden" class="' . $size . '-text" id="bm_stories_settings_' . $args['section'] . '[' . $args['id'] . ']" name="bm_stories_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
    $html .= '<label for="bm_stories_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['std'] . '</label>';

    echo $html;
}




/**
 * Textarea Callback
 *
 * Renders textarea fields.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $bm_stories_settings Array of all the BM_STORIES Options
 * @return void
 */
function bm_stories_textarea_callback( $args ) {
    global $bm_stories_settings;

    if ( isset( $bm_stories_settings[ $args['id'] ] ) )
        $value = $bm_stories_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<textarea class="large-text" cols="50" rows="5" id="bm_stories_settings_' . $args['section'] . '[' . $args['id'] . ']" name="bm_stories_settings_' . $args['section'] . '[' . $args['id'] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
    $html .= '<label for="bm_stories_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Password Callback
 *
 * Renders password fields.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $bm_stories_settings Array of all the BM_STORIES Options
 * @return void
 */
function bm_stories_password_callback( $args ) {
    global $bm_stories_settings;

    if ( isset( $bm_stories_settings[ $args['id'] ] ) )
        $value = $bm_stories_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<input type="password" class="' . $size . '-text" id="bm_stories_settings_' . $args['section'] . '[' . $args['id'] . ']" name="bm_stories_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( $value ) . '"/>';
    $html .= '<label for="bm_stories_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Missing Callback
 *
 * If a function is missing for settings callbacks alert the user.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @return void
 */
function bm_stories_missing_callback($args) {
    printf( __( 'The callback function used for the <strong>%s</strong> setting is missing.', 'bm_stories' ), $args['id'] );
}

/**
 * Select Callback
 *
 * Renders select fields.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $bm_stories_settings Array of all the BM_STORIES Options
 * @return void
 */
function bm_stories_select_callback($args) {
    global $bm_stories_settings;

    if ( isset( $bm_stories_settings[ $args['id'] ] ) )
        $value = $bm_stories_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $html = '<select id="bm_stories_settings_' . $args['section'] . '[' . $args['id'] . ']" name="bm_stories_settings_' . $args['section'] . '[' . $args['id'] . ']"/>';

    foreach ( $args['options'] as $option => $name ) :
        $selected = selected( $option, $value, false );
        $html .= '<option value="' . $option . '" ' . $selected . '>' . $name . '</option>';
    endforeach;

    $html .= '</select>';
    $html .= '<label for="bm_stories_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Color select Callback
 *
 * Renders color select fields.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $bm_stories_settings Array of all the BM_STORIES Options
 * @return void
 */
function bm_stories_color_select_callback( $args ) {
    global $bm_stories_settings;

    if ( isset( $bm_stories_settings[ $args['id'] ] ) )
        $value = $bm_stories_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $html = '<select id="bm_stories_settings_' . $args['section'] . '[' . $args['id'] . ']" name="bm_stories_settings_' . $args['section'] . '[' . $args['id'] . ']"/>';

    foreach ( $args['options'] as $option => $color ) :
        $selected = selected( $option, $value, false );
        $html .= '<option value="' . $option . '" ' . $selected . '>' . $color['label'] . '</option>';
    endforeach;

    $html .= '</select>';
    $html .= '<label for="bm_stories_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Rich Editor Callback
 *
 * Renders rich editor fields.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $bm_stories_settings Array of all the BM_STORIES Options
 * @global $wp_version WordPress Version
 */
function bm_stories_rich_editor_callback( $args ) {
    global $bm_stories_settings, $wp_version;

    if ( isset( $bm_stories_settings[ $args['id'] ] ) )
        $value = $bm_stories_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    if ( $wp_version >= 3.3 && function_exists( 'wp_editor' ) ) {
        $html = wp_editor( stripslashes( $value ), 'bm_stories_settings_' . $args['section'] . '[' . $args['id'] . ']', array( 'textarea_name' => 'bm_stories_settings_' . $args['section'] . '[' . $args['id'] . ']' ) );
    } else {
        $html = '<textarea class="large-text" rows="10" id="bm_stories_settings_' . $args['section'] . '[' . $args['id'] . ']" name="bm_stories_settings_' . $args['section'] . '[' . $args['id'] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
    }

    $html .= '<br/><label for="bm_stories_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Upload Callback
 *
 * Renders upload fields.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $bm_stories_settings Array of all the BM_STORIES Options
 * @return void
 */
function bm_stories_upload_callback( $args ) {
    global $bm_stories_settings;

    if ( isset( $bm_stories_settings[ $args['id'] ] ) )
        $value = $bm_stories_settings[$args['id']];
    else
        $value = isset($args['std']) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<input type="text" class="' . $size . '-text bm_stories_upload_field" id="bm_stories_settings_' . $args['section'] . '[' . $args['id'] . ']" name="bm_stories_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
    $html .= '<span>&nbsp;<input type="button" class="bm_stories_settings_upload_button button-secondary" value="' . __( 'Upload File', 'bm_stories' ) . '"/></span>';
    $html .= '<label for="bm_stories_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}


/**
 * Color picker Callback
 *
 * Renders color picker fields.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $bm_stories_settings Array of all the BM_STORIES Options
 * @return void
 */
function bm_stories_color_callback( $args ) {
    global $bm_stories_settings;

    if ( isset( $bm_stories_settings[ $args['id'] ] ) )
        $value = $bm_stories_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $default = isset( $args['std'] ) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<input type="text" class="bm_stories-color-picker" id="bm_stories_settings_' . $args['section'] . '[' . $args['id'] . ']" name="bm_stories_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( $value ) . '" data-default-color="' . esc_attr( $default ) . '" />';
    $html .= '<label for="bm_stories_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}



/**
 * Hook Callback
 *
 * Adds a do_action() hook in place of the field
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @return void
 */
function bm_stories_hook_callback( $args ) {
    do_action( 'bm_stories_' . $args['id'] );


    
}

/**
 * Settings Sanitization
 *
 * Adds a settings error (for the updated message)
 * At some point this will validate input
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $input The value inputted in the field
 * @return string $input Sanitizied value
 */
function bm_stories_settings_sanitize( $input = array() ) {

    global $bm_stories_settings;

    parse_str( $_POST['_wp_http_referer'], $referrer );

    $output    = array();
    $settings  = bm_stories_get_registered_settings();
    $tab       = isset( $referrer['tab'] ) ? $referrer['tab'] : 'general';
    $post_data = isset( $_POST[ 'bm_stories_settings_' . $tab ] ) ? $_POST[ 'bm_stories_settings_' . $tab ] : array();

    $input = apply_filters( 'bm_stories_settings_' . $tab . '_sanitize', $post_data );

    // Loop through each setting being saved and pass it through a sanitization filter
    foreach( $input as $key => $value ) {

        // Get the setting type (checkbox, select, etc)
        $type = isset( $settings[ $key ][ 'type' ] ) ? $settings[ $key ][ 'type' ] : false;

        if( $type ) {
            // Field type specific filter
            $output[ $key ] = apply_filters( 'bm_stories_settings_sanitize_' . $type, $value, $key );
        }

        // General filter
        $output[ $key ] = apply_filters( 'bm_stories_settings_sanitize', $value, $key );
    }


    // Loop through the whitelist and unset any that are empty for the tab being saved
    if( ! empty( $settings[ $tab ] ) ) {
        foreach( $settings[ $tab ] as $key => $value ) {

            // settings used to have numeric keys, now they have keys that match the option ID. This ensures both methods work
            if( is_numeric( $key ) ) {
                $key = $value['id'];
            }

            if( empty( $_POST[ 'bm_stories_settings_' . $tab ][ $key ] ) ) {
                unset( $bm_stories_settings[ $key ] );
            }

        }
    }

    // Merge our new settings with the existing
    $output = array_merge( $bm_stories_settings, $output );

    // @TODO: Get Notices Working in the backend.
    add_settings_error( 'bm_stories-notices', '', __( 'Settings Updated', 'bm_stories' ), 'updated' );

    return $output;

}

/**
 * Sanitize text fields
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $input The field value
 * @return string $input Sanitizied value
 */
function bm_stories_sanitize_text_field( $input ) {
    return trim( $input );
}
add_filter( 'bm_stories_settings_sanitize_text', 'bm_stories_sanitize_text_field' );

/**
 * Retrieve settings tabs
 * @since  0.1
 * @author Bryan Monzon
 * @param array $input The field value
 * @return string $input Sanitizied value
 */
function bm_stories_get_settings_tabs() {

    $settings = bm_stories_get_registered_settings();

    $tabs            = array();
    $tabs['general'] = __( 'General', 'bm_stories' );

    return apply_filters( 'bm_stories_settings_tabs', $tabs );
}
