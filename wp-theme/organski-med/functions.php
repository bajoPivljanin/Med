<?php
/** Organski med: postavke teme. */

add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('woocommerce');
    register_nav_menus(['glavni' => 'Glavni meni']);
});

add_action('wp_enqueue_scripts', function () {
    $dir = get_template_directory();
    $uri = get_template_directory_uri();

    wp_enqueue_style('organski-med-fonts', 'https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,600;0,700;0,800;1,600&family=Nunito+Sans:wght@400;600;800&display=swap', [], null);
    wp_enqueue_style('organski-med', $uri . '/assets/styles.css', ['organski-med-fonts'], filemtime($dir . '/assets/styles.css'));

    wp_enqueue_script('lenis', 'https://cdn.jsdelivr.net/npm/lenis@1.1.13/dist/lenis.min.js', [], '1.1.13', true);
    wp_enqueue_script('organski-med', $uri . '/assets/script.js', ['lenis'], filemtime($dir . '/assets/script.js'), true);
});

/** Broj stavki u korpi za ikonicu u zaglavlju. */
function om_cart_count(): int {
    return function_exists('WC') && WC()->cart ? (int) WC()->cart->get_cart_contents_count() : 0;
}

function om_cart_url(): string {
    return function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/korpa/');
}

/** Cijene bez nepotrebnih nula: "20 €", a ne "20,00 €". */
add_filter('woocommerce_price_trim_zeros', '__return_true');

/** Ilustracija tegle u boji Proizvoda (kad Proizvod nema fotografiju). */
function om_jar(string $color): string {
    $c = esc_attr($color);
    return '<svg viewBox="0 0 100 130" fill="none" aria-hidden="true"><rect x="22" y="4" width="56" height="16" rx="4" fill="#3B2412"/><path d="M18 24h64v90a12 12 0 0 1-12 12H30a12 12 0 0 1-12-12z" fill="#fff" fill-opacity=".4" stroke="#3B2412" stroke-width="3"/><path d="M22 50h56v64a8 8 0 0 1-8 8H30a8 8 0 0 1-8-8z" fill="' . $c . '"/><rect x="30" y="68" width="40" height="32" rx="6" fill="#FFF6DC"/></svg>';
}

/** Slika Proizvoda: fotografija ako postoji, inače tegla. */
function om_product_visual(WC_Product $p): string {
    if ($p->get_image_id()) {
        return wp_get_attachment_image($p->get_image_id(), 'woocommerce_single', false, ['alt' => $p->get_name()]);
    }
    return om_jar($p->get_meta('_om_color') ?: '#E0A526');
}

/** Naziv kategorije Proizvoda. */
function om_product_cat(WC_Product $p): array {
    $t = get_the_terms($p->get_id(), 'product_cat');
    return $t && !is_wp_error($t) ? [$t[0]->slug, $t[0]->name] : ['', ''];
}

/** Težina/zapremina za prikaz uz cijenu (prvi atribut: Težina, Zapremina ili Sadržaj). */
function om_product_size(WC_Product $p): string {
    foreach (['Težina', 'Zapremina', 'Sadržaj'] as $k) {
        $v = $p->get_attribute($k);
        if ($v) return $v;
    }
    return '';
}

add_action('wp_enqueue_scripts', function () {
    $dir = get_template_directory();
    wp_enqueue_script('organski-med-shop', get_template_directory_uri() . '/assets/shop.js', [], filemtime($dir . '/assets/shop.js'), true);
}, 20);

require_once get_template_directory() . '/inc/shop.php';

/** Datum na crnogorskom: "29. oktobar 2026." */
function om_fmt_date($post = null): string {
    $m = ['januar', 'februar', 'mart', 'april', 'maj', 'jun', 'jul', 'avgust', 'septembar', 'oktobar', 'novembar', 'decembar'];
    $t = strtotime(get_post_field('post_date', $post));
    return date('j', $t) . '. ' . $m[(int) date('n', $t) - 1] . ' ' . date('Y', $t) . '.';
}

function om_read_time($post = null): string {
    $words = str_word_count(wp_strip_all_tags(get_post_field('post_content', $post)));
    return max(1, (int) round($words / 180)) . ' min čitanja';
}

function om_post_cat($post = null): string {
    $c = get_the_category(get_post_field('ID', $post));
    return $c ? $c[0]->name : '';
}

/** Naslovna ilustracija članka (tegla u boji članka). */
function om_post_cover($post = null, string $extra = ''): string {
    $id = get_post_field('ID', $post);
    $g = get_post_meta($id, '_om_gradient', true) ?: 'g1';
    $c = get_post_meta($id, '_om_color', true) ?: '#E0A526';
    $inner = has_post_thumbnail($id) ? get_the_post_thumbnail($id, 'large') : om_jar($c);
    return '<div class="post-cover ' . esc_attr($g . ' ' . $extra) . '">' . $inner . '</div>';
}
