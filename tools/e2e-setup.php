<?php
/**
 * Priprema lokalni sajt za automatske testove (tests/e2e):
 *  - uključuje privatni link za "Coming soon" mod (test ulazi bez prijave),
 *  - pravi skriveni probni Proizvod koji nema na zalihi,
 *  - upisuje tests/e2e/.env (adresa sajta, Mailpit i ključ privatnog linka; fajl nije u gitu).
 *
 * Pokretanje: php -d extension=mysqli -d mysqli.default_port=10035 tools/e2e-setup.php "<putanja do wp-load.php>" [mailpit-url]
 */
$wpLoad = $argv[1] ?? '';
$mailpit = $argv[2] ?? 'http://localhost:10030';
if (!is_file($wpLoad)) { fwrite(STDERR, "Navedite putanju do wp-load.php\n"); exit(1); }
$_SERVER['HTTP_HOST'] = 'organski-med.local';
define('WP_USE_THEMES', false);
require $wpLoad;
if (!class_exists('WooCommerce')) { fwrite(STDERR, "WooCommerce nije aktivan\n"); exit(1); }

update_option('woocommerce_private_link', 'yes');
if (!get_option('woocommerce_share_key')) { update_option('woocommerce_share_key', wp_generate_password(32, false)); }

$sku = 'e2e-nema-na-zalihi';
$id = wc_get_product_id_by_sku($sku);
$p = $id ? wc_get_product($id) : new WC_Product_Simple();
$p->set_name('Probni proizvod (nema na zalihi)');
$p->set_slug($sku);
$p->set_sku($sku);
$p->set_status('publish');
$p->set_regular_price('10');
$p->set_short_description('Samo za automatske testove.');
$p->set_manage_stock(false);
$p->set_stock_status('outofstock');
$p->set_catalog_visibility('hidden');
$pid = $p->save();

$env = "OM_BASE_URL=http://organski-med.local\nOM_MAILPIT_URL=$mailpit\nOM_SHARE_KEY=" . get_option('woocommerce_share_key') . "\nOM_OUT_OF_STOCK_ID=$pid\n";
file_put_contents(dirname(__DIR__) . '/tests/e2e/.env', $env);
echo "Probni proizvod #$pid, tests/e2e/.env napisan.\n";
