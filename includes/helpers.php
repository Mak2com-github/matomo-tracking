<?php
if (!defined('ABSPATH')) {
    exit;
}

function wc_matomo_send_request($params) {
    $matomo_url = get_option('wc_matomo_tracking_url') . 'matomo.php';
    $response = wp_remote_get(add_query_arg($params, $matomo_url));

    if (is_wp_error($response)) {
        error_log('Matomo request failed: ' . $response->get_error_message());
    } else {
        error_log('Matomo response: ' . print_r($response, true));
    }
}
