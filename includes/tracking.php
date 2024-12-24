<?php
if (!defined('ABSPATH')) {
    exit;
}

// Tracker les vues de produits
function wc_matomo_track_product_view() {
    if (is_product()) {
        global $post;
        $product = wc_get_product($post->ID);

        $matomo_url = get_option('wc_matomo_tracking_url');
        $site_id = get_option('wc_matomo_tracking_site_id');

        if ($matomo_url && $site_id) {
            echo "
            <script>
                var _paq = _paq || [];
                _paq.push(['trackPageView']);
                _paq.push(['setEcommerceView',
                    '{$product->get_id()}',
                    '" . esc_js($product->get_name()) . "',
                    '" . esc_js(implode(',', $product->get_category_ids())) . "',
                    {$product->get_price()}
                ]);
                (function() {
                    var u='{$matomo_url}';
                    _paq.push(['setTrackerUrl', u+'matomo.php']);
                    _paq.push(['setSiteId', '{$site_id}']);
                    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
                    g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
                })();
            </script>
            ";
        }
    }
}
add_action('wp_head', 'wc_matomo_track_product_view');

// Tracker l'ajout au panier
function wc_matomo_track_add_to_cart($cart_item_key, $product_id, $quantity) {
    $product = wc_get_product($product_id);

    $matomo_url = get_option('wc_matomo_tracking_url');
    $site_id = get_option('wc_matomo_tracking_site_id');

    if ($matomo_url && $site_id) {
        $params = [
            'idsite' => $site_id,
            'rec' => 1,
            'ec_id' => 'ADD_TO_CART',
            'ec_items' => json_encode([[
                $product->get_id(),
                $product->get_name(),
                implode(',', $product->get_category_ids()),
                $product->get_price(),
                $quantity,
            ]]),
        ];

        $response = wc_matomo_send_request($params);

        // Ajout de logs pour débogage
        if (is_wp_error($response)) {
            error_log('Erreur Matomo (ADD_TO_CART) : ' . $response->get_error_message());
        } else {
            error_log('Réponse Matomo (ADD_TO_CART) : ' . print_r($response, true));
        }
    }
}
add_action('woocommerce_add_to_cart', 'wc_matomo_track_add_to_cart', 10, 3);


// Tracker la commande
function wc_matomo_track_order_complete($order_id) {
    $order = wc_get_order($order_id);

    // Récupérer les options Matomo
    $matomo_url = get_option('wc_matomo_tracking_url');
    $site_id = get_option('wc_matomo_tracking_site_id');

    if ($matomo_url && $site_id) {
        $order_items = [];
        foreach ($order->get_items() as $item_id => $item) {
            $product = $item->get_product();
            $order_items[] = [
                $product->get_id(),
                $product->get_name(),
                implode(',', $product->get_category_ids()),
                $product->get_price(),
                $item->get_quantity(),
            ];
        }

        $params = [
            'idsite' => $site_id,
            'rec' => 1,
            'ec_id' => $order->get_order_number(),
            'revenue' => $order->get_total(),
            'ec_items' => json_encode($order_items),
        ];

        wc_matomo_send_request($params);
    }
}
add_action('woocommerce_thankyou', 'wc_matomo_track_order_complete');

// Tracker les suppressions du panier
function wc_matomo_track_remove_from_cart($cart_item_key, $cart) {
    $cart_item = $cart->removed_cart_contents[$cart_item_key];
    $product = wc_get_product($cart_item['product_id']);

    // Récupérer les options Matomo
    $matomo_url = get_option('wc_matomo_tracking_url');
    $site_id = get_option('wc_matomo_tracking_site_id');

    if ($matomo_url && $site_id) {
        $params = [
            'idsite' => $site_id,
            'rec' => 1,
            'ec_id' => 'REMOVE_FROM_CART',
            'ec_items' => json_encode([[
                $product->get_id(),
                $product->get_name(),
                implode(',', $product->get_category_ids()),
                $product->get_price(),
                $cart_item['quantity'],
            ]]),
        ];

        wc_matomo_send_request($params);
    }
}
add_action('woocommerce_cart_item_removed', 'wc_matomo_track_remove_from_cart', 10, 2);

// Tracker les abandons de panier via JavaScript
function wc_matomo_track_cart_abandonment() {
    if (is_cart() || is_checkout()) {
        $matomo_url = get_option('wc_matomo_tracking_url');
        $site_id = get_option('wc_matomo_tracking_site_id');

        if ($matomo_url && $site_id) {
            ?>
            <script>
              var cartAbandonmentTracked = false;

              window.addEventListener('beforeunload', function () {
                if (!cartAbandonmentTracked) {
                  var cart = <?php echo json_encode(WC()->cart->get_cart()); ?>;
                  var items = [];

                  for (var key in cart) {
                    if (cart.hasOwnProperty(key)) {
                      var item = cart[key];
                      items.push([
                        item.product_id,
                        item.data.product_name,
                        item.data.categories.join(','),
                        item.data.price,
                        item.quantity
                      ]);
                    }
                  }

                  if (items.length > 0) {
                    navigator.sendBeacon('<?php echo esc_url($matomo_url . 'matomo.php'); ?>', JSON.stringify({
                      idsite: <?php echo intval($site_id); ?>,
                      rec: 1,
                      ec_id: 'CART_ABANDONMENT',
                      ec_items: items
                    }));

                    cartAbandonmentTracked = true;
                  }
                }
              });
            </script>
            <?php
        }
    }
}
add_action('wp_footer', 'wc_matomo_track_cart_abandonment');