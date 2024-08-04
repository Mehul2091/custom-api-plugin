<?php
/**
 * Plugin Name: Custom Api Plugin
 * Description: This plugin will allow you to set up a custom namespace for REST APIs.
 * Version: 1.0
 * Author: Mehul Patel
 * License: GPLv2
 * Text Domain: custom-api-plugin
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}


// Activation hook to create table
register_activation_hook(__FILE__, 'custom_plugin_create_table');
/**
 * Function to create table
 */
function custom_plugin_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_data'; // Define your table name

    $charset_collate = $wpdb->get_charset_collate();
    
    // SQL to create the table
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id int(11) NOT NULL AUTO_INCREMENT,
        first_name VARCHAR(100) NOT NULL,
        last_name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        phone VARCHAR(100) NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    // Include the upgrade file
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

require plugin_dir_path(__FILE__) . 'includes/class-custom-rest.php';

add_filter(
    'jwt_auth_expire',
    function ( $expire, $issued_at ) {
        // Modify the "expire" here.
        return time() + (DAY_IN_SECONDS * 30);
    },
    10,
    2
);

$plugin = new CustomRestApi();


// Function to check the dependency
function check_jwt_auth_dependency() {
    if (is_plugin_active('jwt-auth/jwt-auth.php')) {
        // JWT Auth plugin is active
        return true;
    } else {
        // JWT Auth plugin is not active
        return false;
    }
}
function my_custom_plugin_activation() {
    if (!check_jwt_auth_dependency()) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die('The "JWT Auth – WordPress JSON Web Token Authentication" plugin must be active for this plugin to work.');
    }
}
register_activation_hook(__FILE__, 'my_custom_plugin_activation');
function my_custom_plugin_admin_notice() {
    if (!check_jwt_auth_dependency()) {
        ?>
        <div class="notice notice-error">
            <p><?php _e('The "JWT Auth – WordPress JSON Web Token Authentication" plugin must be active to work `Custom Api Plugin` plugin. Install from link : <a href="'.get_site_url().'/wp-admin/plugin-install.php?s=JWT%2520Auth&tab=search&type=term">https://wordpress.org/plugins/jwt-auth/</a>', 'my-custom-plugin'); ?></p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'my_custom_plugin_admin_notice');
