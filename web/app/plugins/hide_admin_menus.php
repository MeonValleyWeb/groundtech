<?php
/**
 * Plugin Name: Hide Admin Menus for Non-Admins
 * Description: Hides specific admin menu items for all users except administrators.
 * Author: Meon Valley Web
 * Version: 1.1
 */

add_action('admin_menu', 'mvw_hide_admin_menus', 999);

function mvw_hide_admin_menus() {
    if (!current_user_can('administrator')) {

        // Core menus to hide
         // remove_menu_page('edit.php');                // Posts
         // remove_menu_page('upload.php');              // Media
         // remove_menu_page('edit.php?post_type=page'); // Pages
        remove_menu_page('tools.php');               // Tools
        remove_menu_page('plugins.php');             // Plugins
        remove_menu_page('users.php');               // Users
        remove_menu_page('options-general.php');     // Settings
         // remove_menu_page('themes.php');              // Appearance

        // Submenus under Appearance
        remove_submenu_page('themes.php', 'themes.php');              // Themes
        remove_submenu_page('themes.php', 'nav-menus.php');           // Menus
        remove_submenu_page('themes.php', 'widgets.php');             // Widgets
        remove_submenu_page('themes.php', 'customize.php');           // Customizer
        remove_submenu_page('themes.php', 'theme-editor.php');        // Theme Editor

        // Custom post types
        remove_menu_page('edit.php?post_type=your_cpt_slug');

        // Plugin-specific menus
        remove_menu_page('breakdance'); // Breakdance Builder (main menu)
        remove_menu_page('acf-options'); // ACF Options (if used)

        // Optional: remove Breakdance submenus if needed
        remove_submenu_page('breakdance', 'breakdance-settings');     // Breakdance Settings
        remove_submenu_page('breakdance', 'breakdance-theme-builder'); // Theme Builder
    }
}