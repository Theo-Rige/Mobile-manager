<?php
/*
Plugin Name: KC - Mobile Manager
Description: Gestion du mobile
Version: 2.0
author: Kelcible
author uri: https://www.kelcible.fr/
*/

add_action('admin_menu', 'kc_plugin_menu');
add_action('wp_enqueue_scripts', 'kc_mobile_manager_script');

function kc_mobile_manager_script()
{
    wp_enqueue_style("kc-mobile-manager-style", plugins_url("kc-mobile-manager/style.css"));
    wp_enqueue_script("kc-mobile-manager-script", plugins_url("kc-mobile-manager/script.js"));
}

// Ajout de l'entrée de la page de configuration dans le back-office
function kc_plugin_menu()
{
    add_menu_page('Mobile Manager', 'Mobile Manager', 'manage_options', 'mobile-manager', 'kc_plugin_options', '', 4);
}

// Gestion de l'affiche de la page de configuration dans le back-office
function kc_display_manage_page()
{
    if (isset($_GET['page']) && $_GET['page'] == "mobile-manager") {
        wp_enqueue_style("kc-mobile-manager-admin-style", plugins_url("kc-mobile-manager/admin/style.css"));
        wp_enqueue_script("kc-mobile-manager-admin-script", plugins_url("kc-mobile-manager/admin/script.js"));
        include "admin/admin.php";
    }
}

add_action('admin_enqueue_scripts', 'codemirror_enqueue_scripts');

function codemirror_enqueue_scripts($hook)
{
    if (isset($_GET['page']) && $_GET['page'] == "mobile-manager") {
        $cm_settings['codeEditor'] = wp_enqueue_code_editor([
            'type' => 'image/svg+xml',
            'codemirror' => [
                // 'mode'              => 'svg',
                'lineNumbers'       => false,
                'styleActiveLine'   => false,
            ],
            'htmlhint'   => [
                // 'tag-pair'          => false
            ]
        ]);
        wp_localize_script('jquery', 'cm_settings', $cm_settings);
        wp_enqueue_script('wp-theme-plugin-editor');
        wp_enqueue_style('wp-codemirror');
    }
}

// Gestion de l'affiche de la page de configuration dans le back-office en fonction du rôle de l'utilisateur
function kc_plugin_options()
{
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    kc_display_manage_page();
}

// Si inexistante, on créée la table SQL "mobile_manager" après l'activation du thème
global $wpdb;
$charset_collate = $wpdb->get_charset_collate();

$mobile_manager_table_name = $wpdb->prefix . 'mobile_manager';

$mobile_manager_sql = "CREATE TABLE IF NOT EXISTS $mobile_manager_table_name (
	id mediumint(9) NOT NULL AUTO_INCREMENT,
    active boolean DEFAULT false,
    selector varchar(255) DEFAULT NULL,
	selector_type varchar(45) DEFAULT 'id',
	breakpoint mediumint(9) DEFAULT NULL,
	burger_position varchar(45) DEFAULT 'top_left',
    burger_color varchar(7) DEFAULT '#000000',
    search boolean DEFAULT false,
    search_icon tinyint DEFAULT 1,
    search_custom_icon varchar(1000) DEFAULT NULL,
    search_color varchar(7) DEFAULT '#000000',
    search_position varchar(45) DEFAULT 'right',
    logo varchar(255) DEFAULT NULL,
    logo_position varchar(25) DEFAULT NULL,
    back_color varchar(7) DEFAULT '#FFFFFF',
    back_opacity float DEFAULT 0.5,
    back_img varchar(255) DEFAULT NULL,
    content_before varchar(1000) DEFAULT NULL,
    content_after varchar(1000) DEFAULT NULL,
	PRIMARY KEY  (id)
) $charset_collate;";

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($mobile_manager_sql);

// Création de l'emplacement du menu
$menu_location = 'kc_mobile';
register_nav_menu($menu_location, 'KC - Mobile Manager');

// Ajax pour récuperer le menu en fonction du contexte de langue
function kc_mobile_manager_get_menu()
{
    $locale = substr(get_locale(), 0, 2);


    // @SEE https://wordpress.org/support/topic/get-nav-menu-id-in-a-specific-languange/
    // @TODO : gérer le cas la traduction n'est pas gérée par Polylang
    if (is_plugin_active('polylang/polylang.php')) {
        $options = get_option('polylang');
        $theme = get_option('stylesheet');
        $menu_id = $options['nav_menus'][$theme][$menu_location][$locale];
    } else if ($locale === 'fr') {
        $menus = get_nav_menu_locations();
        $menu_id = $menus[$menu_location];
        if (empty($menu_id || $menu_id <= 0)) {
            echo json_encode(['error' => 'Aucun menu trouvé']);
            exit;
        }
    } else {
        echo json_encode(['error' => 'Seulement Polylang est supporté comme plugin multilingue pour le moment']);
        exit;
    }

    ob_start();
    wp_nav_menu($menu_id);
    $menu = ob_get_clean();

    echo json_encode(['menu' => $menu]);
    exit;
}
add_action('wp_ajax_kc_mobile_manager_get_menu', 'kc_mobile_manager_get_menu');
add_action('wp_ajax_nopriv_kc_mobile_manager_get_menu', 'kc_mobile_manager_get_menu');

// Shortcode pour afficher le menu burger
function kc_mobile_manager_shortcode()
{
    global $wpdb;
    $position = $wpdb->get_row("SELECT position FROM " . $wpdb->prefix . 'mobile_manager' . " WHERE id = 1", ARRAY_A);

    if ($position['position'] == 'shortcode') {
        return '<div class="kc_mobile-manager-burger"><div class="burger"></div></div>';
    } else {
        return '';
    }
}
add_shortcode('kc_mobile_manager', 'kc_mobile_manager_shortcode');
