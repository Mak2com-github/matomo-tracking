<?php
/*
Plugin Name: WooCommerce Matomo Tracking
Description: Plugin pour intégrer le suivi e-commerce WooCommerce dans Matomo.
Version: 1.0
Author: Alexandre Celier
*/

// Sécurité : empêcher l'accès direct au fichier
if (!defined('ABSPATH')) {
    exit;
}

// Définir les constantes du plugin
define('WC_MATOMO_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('WC_MATOMO_PLUGIN_URL', plugin_dir_url(__FILE__));

// Charger les fichiers nécessaires
require_once WC_MATOMO_PLUGIN_PATH . 'includes/settings.php';
require_once WC_MATOMO_PLUGIN_PATH . 'includes/tracking.php';
require_once WC_MATOMO_PLUGIN_PATH . 'includes/helpers.php';

// Initialisation
function wc_matomo_tracking_init() {
    // Initialisation des options ou autre logique globale
}
add_action('init', 'wc_matomo_tracking_init');

// Activer le plugin
function wc_matomo_tracking_activate() {
    // Code d'activation, comme la création d'options
}
register_activation_hook(__FILE__, 'wc_matomo_tracking_activate');

// Désactiver le plugin
function wc_matomo_tracking_deactivate() {
    // Code de désactivation
}
register_deactivation_hook(__FILE__, 'wc_matomo_tracking_deactivate');
