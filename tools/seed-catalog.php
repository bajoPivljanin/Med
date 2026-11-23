<?php
/**
 * Puni WooCommerce katalogom iz prototipa: kategorije, 8 Proizvoda, valuta.
 * Može se pokretati više puta (ne pravi duplikate).
 *
 * Pokretanje (Local, PHP 8.2): php -d extension=mysqli -d mysqli.default_port=10035 tools/seed-catalog.php "<putanja do wp-load.php>"
 */
$wpLoad = $argv[1] ?? '';
if (!is_file($wpLoad)) { fwrite(STDERR, "Navedite putanju do wp-load.php\n"); exit(1); }
$_SERVER['HTTP_HOST'] = 'organski-med.local';
define('WP_USE_THEMES', false);
require $wpLoad;
if (!class_exists('WooCommerce')) { fwrite(STDERR, "WooCommerce nije aktivan\n"); exit(1); }

/* Valuta: euro, "20 €" */
update_option('woocommerce_currency', 'EUR');
update_option('woocommerce_currency_pos', 'right_space');
update_option('woocommerce_price_decimal_sep', ',');
update_option('woocommerce_price_thousand_sep', '.');

/* Kategorije */
$cats = ['med' => 'Med', 'pcelinji-proizvodi' => 'Pčelinji proizvodi', 'poklon' => 'Poklon'];
$catIds = [];
foreach ($cats as $slug => $name) {
    $t = term_exists($slug, 'product_cat');
    if (!$t) { $t = wp_insert_term($name, 'product_cat', ['slug' => $slug]); }
    $catIds[$slug] = is_array($t) ? (int) $t['term_id'] : (int) $t;
}

$cu = 'Zatvoreno, na sobnoj temperaturi, dalje od sunca';
$products = [
 ['bagrem', 'Bagremov med', 'med', 'Svijetao, nježan i dugo tečan.', 20, 'g1', '#E8B93C', 'Hit',
  ['Bagremov med je najsvjetiji i najblaži u ponudi. Okus je nježan i ne preglasi čaj, jogurt ni palačinke, pa ga rado biraju i oni koji inače ne vole jaku aromu.', 'Dugo ostaje tečan, a kad kristališe, to je znak da je pravi. Teglu samo zagrijte u mlakoj vodi.'],
  [['Težina', '900 g'], ['Berba', 'Maj'], ['Konzistencija', 'Tečan, kristališe sporo'], ['Čuvanje', $cu]]],
 ['livada', 'Livadski med', 'med', 'Bogata aroma ljekovitog bilja.', 20, 'g2', '#C98A22', '',
  ['Livadski med nastaje od cvijeća sa različitih livada, pa se okus malo razlikuje od berbe do berbe. Aroma je bogatija i zlatnija nego kod bagrema.', 'Dobar je za svaki dan, uz hljeb i maslac ili u čaju od bilja.'],
  [['Težina', '900 g'], ['Berba', 'Jun i jul'], ['Konzistencija', 'Tečan, tokom zime može kristalisati'], ['Čuvanje', $cu]]],
 ['kesten', 'Kesten', 'med', 'Taman i snažan, blago gorak.', 20, 'g2', '#8B5A1E', '',
  ['Kestenov med je taman, gust i snažnog okusa sa blagom gorčinom na kraju. Nije za svakoga, ali ko ga zavoli, rijetko mijenja.', 'Odlično ide uz sireve i orahe, a u maloj količini daje karakter i toplim napicima.'],
  [['Težina', '900 g'], ['Berba', 'Jun i jul'], ['Konzistencija', 'Tečan, kristališe sporo'], ['Čuvanje', $cu]]],
 ['zalfija', 'Med od žalfije', 'med', 'Mediteranski med sa ljekovitim svojstvima.', 20, 'g1', '#D9A92E', '',
  ['Med od žalfije dolazi sa sunčanih, kamenitih predjela. Svijetao je, blago začinski i prijatan uz topli čaj kad vas hvata prehlada.', 'Okus je čist i lagano biljan, bez oštrine.'],
  [['Težina', '900 g'], ['Berba', 'Maj i jun'], ['Konzistencija', 'Tečan, vremenom može kristalisati'], ['Čuvanje', $cu]]],
 ['sace', 'Saće', 'med', 'Med u svom najčistijem obliku.', 25, 'g1', '#E0A526', 'Novo',
  ['Med u saću, onakav kakav je u košnici. Saće se jede cijelo, zajedno sa voštanim zidovima ćelija.', 'Lijepo izgleda na tanjiru sa sirevima, a može i kašikom direktno iz tegle.'],
  [['Težina', '500 g'], ['Berba', 'Prema sezoni'], ['Oblik', 'Cijelo saće'], ['Čuvanje', 'Zatvoreno, na sobnoj temperaturi']]],
 ['polen', 'Polen', 'pcelinji-proizvodi', 'Prirodni cvjetni prah.', 7, 'g3', '#D4A62A', '',
  ['Cvjetni prah koji pčele skupljaju na nogicama i unose u košnicu. Prodaje se u obliku sitnih zrnaca.', 'Dodajte ga u jogurt, kašu ili smuti, ili ga jedite kašikicom.'],
  [['Težina', '100 g'], ['Berba', 'Maj do jul'], ['Oblik', 'Suha zrnca'], ['Čuvanje', 'Zatvoreno, u frižideru']]],
 ['propolis', 'Propolis', 'pcelinji-proizvodi', 'Prirodni štit za imunitet.', 6, 'g4', '#9A5B1B', '',
  ['Propolis pčele prave od smola sa pupoljaka i koriste ga da zaštite košnicu. Ovdje je u obliku tečnog ekstrakta u maloj bočici.', 'Koristi se u kapima, prema uputstvu na pakovanju.'],
  [['Zapremina', '20 ml'], ['Oblik', 'Tečni ekstrakt'], ['Čuvanje', 'Zatvoreno, na tamnom i hladnom mjestu']]],
 ['poklon', 'Poklon paket', 'poklon', 'Tri meda u lijepoj kutiji.', 50, 'g4', '#B67F0E', '',
  ['Tri manje tegle meda u poklon kutiji. Dobar izbor za rođendan, praznike ili kad nekome želite da pošaljete nešto domaće.', 'Sorte u paketu zavise od zaliha u trenutku slanja.'],
  [['Sadržaj', '3 tegle po 250 g'], ['Pakovanje', 'Poklon kutija'], ['Čuvanje', $cu]]],
];

foreach ($products as $i => [$sku, $name, $cat, $short, $price, $grad, $color, $tag, $long, $facts]) {
    $id = wc_get_product_id_by_sku($sku);
    $p = $id ? wc_get_product($id) : new WC_Product_Simple();
    $p->set_name($name);
    $p->set_slug($sku);
    $p->set_sku($sku);
    $p->set_status('publish');
    $p->set_regular_price((string) $price);
    $p->set_short_description($short);
    $p->set_description('<p>' . implode("</p>\n<p>", $long) . '</p>');
    $p->set_category_ids([$catIds[$cat]]);
    $p->set_menu_order($i);
    $p->set_manage_stock(false);
    $p->set_stock_status('instock');
    $attrs = [];
    foreach ($facts as $pos => [$k, $v]) {
        $a = new WC_Product_Attribute();
        $a->set_name($k);
        $a->set_options([$v]);
        $a->set_position($pos);
        $a->set_visible(true);
        $a->set_variation(false);
        $attrs[] = $a;
    }
    $p->set_attributes($attrs);
    $p->update_meta_data('_om_gradient', $grad);
    $p->update_meta_data('_om_color', $color);
    $p->update_meta_data('_om_tag', $tag);
    $newId = $p->save();
    echo ($id ? 'azurirano' : 'napravljeno') . " #$newId $sku\n";
}
echo "Gotovo.\n";
