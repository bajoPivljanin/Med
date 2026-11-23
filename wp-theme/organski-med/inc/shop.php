<?php
/** Pravila prodavnice: dostava, polja narudžbine, obavještenja. */

/** Iznos od kojeg je dostava besplatna (čita se iz podešavanja WooCommerce-a). */
function om_free_shipping_from(): ?float {
    if (!class_exists('WC_Shipping_Zones')) return null;
    foreach (WC_Shipping_Zones::get_zones() as $z) {
        foreach ($z['shipping_methods'] as $m) {
            if ($m->id === 'free_shipping' && $m->is_enabled() && $m->get_option('requires') === 'min_amount') {
                return (float) $m->get_option('min_amount');
            }
        }
    }
    return null;
}

/** Kad je dostava besplatna, ne nudi plaćenu. */
add_filter('woocommerce_package_rates', function ($rates) {
    $free = array_filter($rates, fn($r) => $r->method_id === 'free_shipping');
    return $free ? $free : $rates;
}, 100);

/** Obavještenje: koliko fali do besplatne dostave. */
function om_free_shipping_note(): void {
    $from = om_free_shipping_from();
    if (!$from || !WC()->cart || WC()->cart->is_empty()) return;
    $sub = (float) WC()->cart->get_displayed_subtotal();
    echo '<p class="ship-note">';
    if ($sub >= $from) {
        echo 'Imate besplatnu dostavu.';
    } else {
        echo 'Dodajte još ' . wp_kses_post(wc_price($from - $sub)) . ' do besplatne dostave.';
    }
    echo '</p>';
}
add_action('woocommerce_before_cart', 'om_free_shipping_note');
add_action('woocommerce_before_checkout_form', 'om_free_shipping_note', 5);

/** Polja narudžbine: samo ono što je potrebno za dostavu. */
add_filter('woocommerce_checkout_fields', function ($fields) {
    unset($fields['billing']['billing_company'], $fields['billing']['billing_address_2'], $fields['billing']['billing_state']);
    unset($fields['shipping']['shipping_company'], $fields['shipping']['shipping_address_2'], $fields['shipping']['shipping_state']);
    $fields['billing']['billing_phone']['required'] = true;
    $fields['billing']['billing_email']['required'] = true;
    return $fields;
});
add_filter('woocommerce_ship_to_different_address_checked', '__return_false');
add_filter('woocommerce_enable_order_notes_field', '__return_true');

/** Obavještenja (dodato u korpu, greške) na stranicama koje ih ne prikazuju same. */
add_action('om_before_main', function () {
    if (function_exists('wc_print_notices') && !is_cart() && !is_checkout()) {
        echo '<div class="container om-notices">';
        wc_print_notices();
        echo '</div>';
    }
});

/** Prevod osnovnih WooCommerce poruka na crnogorski (sajt je na engleskom jeziku). */
add_filter('gettext', function ($translated, $text, $domain) {
    static $map = null;
    if ($domain !== 'woocommerce') return $translated;
    if ($map === null) $map = require __DIR__ . '/strings-sr.php';
    return $map[$text] ?? $translated;
}, 10, 3);
add_filter('ngettext', function ($translated, $single, $plural, $number, $domain) {
    static $map = null;
    if ($domain !== 'woocommerce') return $translated;
    if ($map === null) $map = require __DIR__ . '/strings-sr.php';
    return $map[$number == 1 ? $single : $plural] ?? $translated;
}, 10, 5);

/** Prevod poruka koje imaju kontekst (_x). */
add_filter('gettext_with_context', function ($translated, $text, $context, $domain) {
    static $map = null;
    if ($domain !== 'woocommerce') return $translated;
    if ($map === null) $map = require __DIR__ . '/strings-sr.php';
    return $map[$text] ?? $translated;
}, 10, 4);

/** Tegla umjesto prazne slike u korpi. */
add_filter('woocommerce_cart_item_thumbnail', function ($thumb, $item) {
    $p = $item['data'] ?? null;
    if ($p instanceof WC_Product && !$p->get_image_id()) {
        return '<span class="cart-jar">' . om_jar($p->get_meta('_om_color') ?: '#E0A526') . '</span>';
    }
    return $thumb;
}, 10, 2);
