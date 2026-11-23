<?php
/** Osnovni SEO bez dodatka: naslovi, opisi, opisi slika, kanonski linkovi, sitemap. */

const OM_SITE = 'Organski med';
const OM_HOME_TITLE = 'Organski med Crna Gora | Domaći i prirodni med direktno od pčelara';
const OM_HOME_DESC = 'Organski, domaći i prirodni med iz Crne Gore. Bagrem, livada, kesten i žalfija po cijeni od 20 €. Dostava širom Crne Gore, plaćanje pouzećem.';

/** Naslov stranice: "Naziv | Organski med", a početna ima svoj naslov. */
add_filter('document_title_separator', fn() => '|');
add_filter('document_title_parts', function ($parts) {
    if (is_front_page()) {
        return ['title' => OM_HOME_TITLE];
    }
    $parts['site'] = OM_SITE;
    unset($parts['tagline']);
    return $parts;
});

/** Opis stranice (meta description). */
function om_meta_description(): string {
    if (is_front_page()) return OM_HOME_DESC;

    if (function_exists('is_product') && is_product()) {
        $p = wc_get_product(get_queried_object_id());
        if ($p) {
            $short = rtrim(wp_strip_all_tags($p->get_short_description()), '. ');
            $size = om_product_size($p);
            $price = html_entity_decode(wp_strip_all_tags(wc_price((float) $p->get_price())), ENT_QUOTES, 'UTF-8');
            return $p->get_name() . ': ' . $short . '. ' . $price . ($size ? ', ' . $size : '') . '. Dostava širom Crne Gore, plaćanje pouzećem.';
        }
    }
    if (is_singular('post')) {
        $e = trim(wp_strip_all_tags(get_the_excerpt()));
        if ($e) return $e;
    }
    if (is_home()) return 'Savjeti, vodiči i priče iz pčelinjaka: kako izabrati, čuvati i koristiti domaći med.';
    if (function_exists('is_product_category') && is_product_category()) {
        $d = trim(wp_strip_all_tags(term_description()));
        return $d ?: single_term_title('', false) . ': domaći proizvodi iz Crne Gore, direktno od pčelara. Dostava širom Crne Gore, plaćanje pouzećem.';
    }
    if (is_page()) return get_the_title() . '. Organski med, domaći i prirodni med iz Crne Gore, direktno od pčelara.';
    return OM_HOME_DESC;
}

add_action('wp_head', function () {
    if (is_404() || is_search()) return;
    $desc = esc_attr(om_meta_description());
    $title = esc_attr(wp_get_document_title());
    $url = esc_url(is_front_page() ? home_url('/') : (get_permalink() ?: home_url('/')));
    $type = is_singular('post') ? 'article' : (function_exists('is_product') && is_product() ? 'product' : 'website');
    echo "<meta name=\"description\" content=\"$desc\">\n";
    echo "<meta property=\"og:type\" content=\"$type\">\n<meta property=\"og:locale\" content=\"sr_ME\">\n";
    echo "<meta property=\"og:site_name\" content=\"" . esc_attr(OM_SITE) . "\">\n";
    echo "<meta property=\"og:title\" content=\"$title\">\n<meta property=\"og:description\" content=\"$desc\">\n<meta property=\"og:url\" content=\"$url\">\n";
    echo "<meta name=\"geo.region\" content=\"ME\">\n";
    if (is_front_page()) {
        $graph = [
            '@context' => 'https://schema.org',
            '@graph' => [
                ['@type' => 'Store', 'name' => OM_SITE, 'description' => 'Organski, domaći i prirodni med iz Crne Gore, direktno od pčelara.', 'url' => home_url('/'), 'areaServed' => ['@type' => 'Country', 'name' => 'Crna Gora']],
                ['@type' => 'FAQPage', 'mainEntity' => [
                    ['@type' => 'Question', 'name' => 'Koliko traje dostava meda u Crnoj Gori?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Dostava širom Crne Gore traje obično 1 do 3 radna dana.']],
                    ['@type' => 'Question', 'name' => 'Kako mogu platiti organski med?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Plaćanje je pouzećem, prilikom preuzimanja paketa.']],
                    ['@type' => 'Question', 'name' => 'Zašto se prirodni med kristalizuje?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Kristalizacija je prirodan proces i znak pravog meda. Teglu zagrijte u mlakoj vodi i med će ponovo postati tečan.']],
                ]],
            ],
        ];
        echo '<script type="application/ld+json">' . wp_json_encode($graph, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "</script>\n";
    }
}, 1);

/** Slike bez opisa dobijaju naziv Proizvoda ili članka kao opis. */
add_filter('wp_get_attachment_image_attributes', function ($attr, $attachment) {
    if (empty($attr['alt'])) {
        $parent = $attachment->post_parent ? get_the_title($attachment->post_parent) : '';
        $attr['alt'] = $parent ?: get_the_title($attachment);
    }
    return $attr;
}, 10, 2);

/** Korpa i narudžbina ne idu na pretragu. */
add_filter('wp_robots', function ($robots) {
    if (function_exists('is_cart') && (is_cart() || is_checkout())) {
        $robots['noindex'] = true;
        $robots['follow'] = true;
    }
    return $robots;
});

/** Sitemap (ugrađen u WordPress, /wp-sitemap.xml): bez korpe i narudžbine. */
add_filter('wp_sitemaps_posts_query_args', function ($args, $post_type) {
    if ($post_type === 'page' && function_exists('wc_get_page_id')) {
        $args['post__not_in'] = array_filter([wc_get_page_id('cart'), wc_get_page_id('checkout'), wc_get_page_id('myaccount')]);
    }
    return $args;
}, 10, 2);
add_filter('wp_sitemaps_add_provider', function ($provider, $name) {
    return $name === 'users' ? false : $provider;
}, 10, 2);

/** Strukturirani podaci Proizvoda (cijena i dostupnost za pretragu). */
add_action('wp_head', function () {
    if (!function_exists('is_product') || !is_product()) return;
    $p = wc_get_product(get_queried_object_id());
    if (!$p) return;
    $data = [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $p->get_name(),
        'description' => om_meta_description(),
        'sku' => $p->get_sku(),
        'url' => get_permalink($p->get_id()),
        'offers' => [
            '@type' => 'Offer',
            'price' => (string) $p->get_price(),
            'priceCurrency' => 'EUR',
            'availability' => $p->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'url' => get_permalink($p->get_id()),
        ],
    ];
    if ($p->get_image_id()) $data['image'] = wp_get_attachment_url($p->get_image_id());
    echo '<script type="application/ld+json">' . wp_json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "</script>\n";
}, 2);

/** Stranica Shop nema svoj izgled: proizvodi su na početnoj, pa je preusmjeri tamo (i ne stavljaj u sitemap). */
add_action('template_redirect', function () {
    $isArchive = function_exists('is_product_category') && (is_product_category() || is_product_tag());
    if ($isArchive || (function_exists('is_shop') && is_shop())) {
        wp_safe_redirect(home_url('/#proizvodi'), 301);
        exit;
    }
});
add_filter('wp_sitemaps_posts_query_args', function ($args, $post_type) {
    if ($post_type === 'page' && function_exists('wc_get_page_id')) {
        $args['post__not_in'] = array_merge((array) ($args['post__not_in'] ?? []), array_filter([wc_get_page_id('shop')]));
    }
    return $args;
}, 20, 2);

/** Kategorije i oznake Proizvoda nemaju svoj izgled (preusmjeravaju se na početnu), pa nisu ni u sitemap-u. */
add_filter('wp_sitemaps_taxonomies', function ($taxonomies) {
    unset($taxonomies['product_cat'], $taxonomies['product_tag']);
    return $taxonomies;
});
