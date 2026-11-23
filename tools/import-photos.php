<?php
/**
 * Uvozi obrađene fotografije (već smanjene, bez EXIF podataka) u WordPress biblioteku medija
 * i postavlja fotografije za početnu stranicu. Može se pokretati više puta (ne pravi duplikate).
 *
 * Pokretanje: php -d extension=mysqli -d extension=gd -d mysqli.default_port=10035 tools/import-photos.php "<putanja do wp-load.php>" "<folder sa .jpg fajlovima>"
 */
$wpLoad = $argv[1] ?? '';
$dir = rtrim($argv[2] ?? '', "/\\");
if (!is_file($wpLoad) || !is_dir($dir)) { fwrite(STDERR, "Navedite putanju do wp-load.php i folder sa fotografijama\n"); exit(1); }
$_SERVER['HTTP_HOST'] = 'organski-med.local';
define('WP_USE_THEMES', false);
require $wpLoad;
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$photos = [
    'med-vrcanje'     => ['Med se cijedi iz vrcaljke', 'Med se cijedi iz vrcaljke u posudu'],
    'kosnice-brdo'    => ['Košnice na brdu', 'Redovi drvenih košnica u pčelinjaku na brdu'],
    'ram-1'           => ['Ram sa saćem 1', 'Ram sa poklopljenim saćem'],
    'ram-2'           => ['Ramovi sa saćem', 'Ramovi sa saćem pored drvene košnice'],
    'ram-3'           => ['Ram sa saćem 3', 'Ram sa poklopljenim saćem'],
    'ram-4'           => ['Ram sa saćem na livadi', 'Ram sa svijetlim saćem na livadi'],
    'ram-2026'        => ['Saće sa godinom 2026', 'Saće na kojem je ispisana godina 2026'],
    'ramovi-kosnica'  => ['Pčele na ramovima', 'Pčele na ramovima u otvorenoj košnici'],
    'tegle-pakovanje' => ['Tegle meda', 'Tegle meda složene u prostoriji za pakovanje'],
    'pcelar-kosnica'  => ['Pčelar pored košnice', 'Pčelar drži ram sa saćem pored košnice'],
    'pcelar-roj'      => ['Pčelar sa rojem', 'Pčelar sa rojem pčela na odijelu'],
    'stand-tegle'     => ['Štand sa teglama meda', 'Štand sa teglama meda'],
];

$ids = [];
foreach ($photos as $slug => [$title, $alt]) {
    $file = "$dir/$slug.jpg";
    if (!is_file($file)) { echo "nema $slug.jpg, preskačem\n"; continue; }
    $existing = get_posts(['post_type' => 'attachment', 'name' => $slug, 'post_status' => 'inherit', 'numberposts' => 1]);
    if ($existing) { $ids[$slug] = $existing[0]->ID; echo "postoji #{$existing[0]->ID} $slug\n"; continue; }
    $tmp = wp_tempnam($file);
    copy($file, $tmp);
    $id = media_handle_sideload(['name' => "$slug.jpg", 'tmp_name' => $tmp], 0, $title, ['post_name' => $slug]);
    if (is_wp_error($id)) { echo "greška $slug: " . $id->get_error_message() . "\n"; continue; }
    update_post_meta($id, '_wp_attachment_image_alt', $alt);
    $ids[$slug] = $id;
    echo "uvezeno #$id $slug\n";
}

/* Fotografije na početnoj (mogu se mijenjati u Prilagodi → Fotografije početne) */
if (isset($ids['med-vrcanje'])) set_theme_mod('om_hero_image', $ids['med-vrcanje']);
if (isset($ids['kosnice-brdo'])) set_theme_mod('om_about_image', $ids['kosnice-brdo']);
echo "Gotovo.\n";
