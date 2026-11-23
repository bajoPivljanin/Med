<?php
/**
 * Podešava prodavnicu: samo Crna Gora, plaćanje pouzećem, dostava 3 € / besplatna od 50 €,
 * kupovina bez naloga, stranice Korpa i Narudžbina. Može se pokretati više puta.
 *
 * Pokretanje: php -d extension=mysqli -d mysqli.default_port=10035 tools/configure-shop.php "<putanja do wp-load.php>"
 */
$wpLoad = $argv[1] ?? '';
if (!is_file($wpLoad)) { fwrite(STDERR, "Navedite putanju do wp-load.php\n"); exit(1); }
$_SERVER['HTTP_HOST'] = 'organski-med.local';
define('WP_USE_THEMES', false);
require $wpLoad;
if (!class_exists('WooCommerce')) { fwrite(STDERR, "WooCommerce nije aktivan\n"); exit(1); }

const OM_FREE_FROM = '50';
const OM_FLAT_FEE = '3';

/* Samo Crna Gora */
update_option('woocommerce_default_country', 'ME');
update_option('woocommerce_allowed_countries', 'specific');
update_option('woocommerce_specific_allowed_countries', ['ME']);
update_option('woocommerce_ship_to_countries', '');
update_option('woocommerce_calc_taxes', 'no');
update_option('woocommerce_enable_coupons', 'no');
update_option('woocommerce_checkout_phone_field', 'required');

/* Kupovina bez naloga */
update_option('woocommerce_enable_guest_checkout', 'yes');
update_option('woocommerce_enable_checkout_login_reminder', 'no');
update_option('woocommerce_enable_signup_and_login_from_checkout', 'no');
update_option('woocommerce_enable_myaccount_registration', 'no');

/* Plaćanje: samo pouzećem */
update_option('woocommerce_cod_settings', [
    'enabled' => 'yes',
    'title' => 'Plaćanje pouzećem',
    'description' => 'Plaćate gotovinom kuriru prilikom preuzimanja paketa.',
    'instructions' => 'Platite gotovinom kuriru prilikom preuzimanja paketa.',
    'enable_for_methods' => [],
    'enable_for_virtual' => 'yes',
]);
foreach (['bacs', 'cheque'] as $gw) {
    $s = get_option("woocommerce_{$gw}_settings", []);
    $s['enabled'] = 'no';
    update_option("woocommerce_{$gw}_settings", $s);
}

/* Dostava: zona Crna Gora, fiksno 3 €, besplatno od 50 € */
$zone = null;
foreach (WC_Shipping_Zones::get_zones() as $z) {
    if ($z['zone_name'] === 'Crna Gora') { $zone = new WC_Shipping_Zone($z['id']); }
}
if (!$zone) {
    $zone = new WC_Shipping_Zone();
    $zone->set_zone_name('Crna Gora');
    $zone->add_location('ME', 'country');
    $zone->save();
}
$have = [];
foreach ($zone->get_shipping_methods() as $m) { $have[$m->id] = $m; }
if (!isset($have['flat_rate']))     { $zone->add_shipping_method('flat_rate');     }
if (!isset($have['free_shipping'])) { $zone->add_shipping_method('free_shipping'); }
foreach ($zone->get_shipping_methods() as $m) {
    if ($m->id === 'flat_rate') {
        update_option('woocommerce_flat_rate_' . $m->instance_id . '_settings', ['title' => 'Dostava', 'tax_status' => 'none', 'cost' => OM_FLAT_FEE]);
    }
    if ($m->id === 'free_shipping') {
        update_option('woocommerce_free_shipping_' . $m->instance_id . '_settings', ['title' => 'Besplatna dostava', 'requires' => 'min_amount', 'min_amount' => OM_FREE_FROM, 'ignore_discounts' => 'no']);
    }
}

/* E-mail poruke: potvrda kupcu i obavještenje vlasniku */
$cust = get_option('woocommerce_customer_processing_order_settings', []);
$cust = array_merge($cust, [
    'enabled' => 'yes',
    'subject' => 'Vaša narudžbina #{order_number} je primljena',
    'heading' => 'Hvala na narudžbini',
    'additional_content' => 'Plaćate gotovinom kuriru prilikom preuzimanja. Dostava traje 1 do 3 radna dana.',
]);
update_option('woocommerce_customer_processing_order_settings', $cust);
$adm = get_option('woocommerce_new_order_settings', []);
$adm = array_merge($adm, [
    'enabled' => 'yes',
    'subject' => '[{site_title}]: Nova narudžbina #{order_number}',
    'heading' => 'Nova narudžbina',
    'additional_content' => '',
]);
update_option('woocommerce_new_order_settings', $adm);

/* Naziv sajta i vidljivost za pretragu (do puštanja sajt ostaje u WooCommerce "Coming soon" modu) */
update_option('blogname', 'Organski med');
update_option('blogdescription', '');
update_option('blog_public', '1');
$sample = get_page_by_path('sample-page');
if ($sample && $sample->post_status === 'publish') { wp_trash_post($sample->ID); }

/* Stranice Korpa i Narudžbina */
$pages = [
    'woocommerce_cart_page_id'     => ['Korpa', 'korpa', '[woocommerce_cart]'],
    'woocommerce_checkout_page_id' => ['Narudžbina', 'narudzbina', '[woocommerce_checkout]'],
];
foreach ($pages as $opt => [$title, $slug, $content]) {
    $id = (int) get_option($opt);
    if ($id) {
        wp_update_post(['ID' => $id, 'post_title' => $title, 'post_name' => $slug, 'post_content' => $content]);
    }
}
flush_rewrite_rules();
echo "Gotovo.\n";
