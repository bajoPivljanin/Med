<?php
/**
 * Premješta u smeće probne narudžbine koje su napravili testovi (e-mail e2e@test.local).
 * Pokretanje: php -d extension=mysqli -d mysqli.default_port=10035 tools/e2e-cleanup.php "<putanja do wp-load.php>"
 */
$wpLoad = $argv[1] ?? '';
if (!is_file($wpLoad)) { fwrite(STDERR, "Navedite putanju do wp-load.php\n"); exit(1); }
$_SERVER['HTTP_HOST'] = 'organski-med.local';
define('WP_USE_THEMES', false);
require $wpLoad;
$orders = wc_get_orders(['billing_email' => 'e2e@test.local', 'limit' => -1, 'status' => array_keys(wc_get_order_statuses())]);
foreach ($orders as $o) { $o->delete(false); }
echo count($orders) . " probnih narudžbina premješteno u smeće.\n";
