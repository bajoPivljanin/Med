<?php
/**
 * Prebacuje članke iz prototipa u WordPress objave (tools/blog-posts.json), pravi stranicu Blog
 * i podešava je kao stranicu za objave. Može se pokretati više puta (ne pravi duplikate).
 *
 * Pokretanje: php -d extension=mysqli -d mysqli.default_port=10035 tools/seed-blog.php "<putanja do wp-load.php>" [draft|publish]
 * Bez drugog argumenta članci se objavljuju (status "publish").
 */
$wpLoad = $argv[1] ?? '';
$status = $argv[2] ?? 'publish';
if (!is_file($wpLoad)) { fwrite(STDERR, "Navedite putanju do wp-load.php\n"); exit(1); }
$_SERVER['HTTP_HOST'] = 'organski-med.local';
define('WP_USE_THEMES', false);
require $wpLoad;

$posts = json_decode(file_get_contents(__DIR__ . '/blog-posts.json'), true);

foreach ($posts as $p) {
    $html = '';
    foreach ($p['body'] as [$type, $text]) {
        $html .= $type === 'h' ? '<h2>' . esc_html($text) . "</h2>\n" : '<p>' . esc_html($text) . "</p>\n";
    }
    $existing = get_page_by_path($p['slug'], OBJECT, 'post');
    $args = [
        'post_type'    => 'post',
        'post_title'   => $p['title'],
        'post_name'    => $p['slug'],
        'post_excerpt' => $p['excerpt'],
        'post_content' => $html,
        'post_status'  => $status,
        'post_date'    => $p['date'] . ' 09:00:00',
    ];
    if ($existing) { $args['ID'] = $existing->ID; }
    $id = wp_insert_post($args, true);
    if (is_wp_error($id)) { fwrite(STDERR, $id->get_error_message() . "\n"); continue; }
    $term = term_exists($p['cat'], 'category') ?: wp_insert_term($p['cat'], 'category');
    wp_set_post_terms($id, [(int) (is_array($term) ? $term['term_id'] : $term)], 'category');
    update_post_meta($id, '_om_gradient', $p['g']);
    update_post_meta($id, '_om_color', $p['col']);
    echo ($existing ? 'azurirano' : 'napravljeno') . " #$id {$p['slug']} ($status)\n";
}

/* Uklanja probnu objavu "Hello world!" koju WordPress pravi sam (premješta je u smeće) */
$hello = get_page_by_path('hello-world', OBJECT, 'post');
if ($hello && $hello->post_status === 'publish') { wp_trash_post($hello->ID); echo "Probna objava premještena u smeće.
"; }

/* Stranica Blog kao stranica za objave */
$blog = get_page_by_path('blog');
$blogId = $blog ? $blog->ID : wp_insert_post(['post_type' => 'page', 'post_title' => 'Blog', 'post_name' => 'blog', 'post_status' => 'publish']);
$home = get_page_by_path('pocetna');
$homeId = $home ? $home->ID : wp_insert_post(['post_type' => 'page', 'post_title' => 'Početna', 'post_name' => 'pocetna', 'post_status' => 'publish']);
update_option('show_on_front', 'page');
update_option('page_on_front', $homeId);
update_option('page_for_posts', $blogId);
flush_rewrite_rules();
echo "Gotovo.\n";
