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
