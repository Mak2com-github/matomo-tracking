<?php
if (!defined('ABSPATH')) {
    exit;
}

// Ajouter une page de réglages
function wc_matomo_tracking_add_settings_page() {
    add_options_page(
        'WooCommerce Matomo Tracking',
        'Matomo Tracking',
        'manage_options',
        'wc-matomo-tracking',
        'wc_matomo_tracking_settings_page'
    );
}
add_action('admin_menu', 'wc_matomo_tracking_add_settings_page');

// Contenu de la page de réglages
function wc_matomo_tracking_settings_page() {
    ?>
    <div class="wrap">
        <h1>WooCommerce Matomo Tracking</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('wc_matomo_tracking_options');
            do_settings_sections('wc-matomo-tracking');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Enregistrement des options
function wc_matomo_tracking_register_settings() {
    register_setting('wc_matomo_tracking_options', 'wc_matomo_tracking_url');
    register_setting('wc_matomo_tracking_options', 'wc_matomo_tracking_site_id');

    add_settings_section(
        'wc_matomo_tracking_section',
        'Paramètres de Matomo',
        null,
        'wc-matomo-tracking'
    );

    add_settings_field(
        'wc_matomo_tracking_url',
        'URL de Matomo',
        'wc_matomo_tracking_url_callback',
        'wc-matomo-tracking',
        'wc_matomo_tracking_section'
    );

    add_settings_field(
        'wc_matomo_tracking_site_id',
        'Site ID Matomo',
        'wc_matomo_tracking_site_id_callback',
        'wc-matomo-tracking',
        'wc_matomo_tracking_section'
    );
}
add_action('admin_init', 'wc_matomo_tracking_register_settings');

function wc_matomo_tracking_url_callback() {
    $url = get_option('wc_matomo_tracking_url', '');
    echo '<input type="text" name="wc_matomo_tracking_url" value="' . esc_attr($url) . '" class="regular-text">';
}

function wc_matomo_tracking_site_id_callback() {
    $site_id = get_option('wc_matomo_tracking_site_id', '');
    echo '<input type="number" name="wc_matomo_tracking_site_id" value="' . esc_attr($site_id) . '" class="regular-text">';
}
