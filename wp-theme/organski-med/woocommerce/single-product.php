<?php
/** Stranica Proizvoda (izgled iz prototipa). */
get_header();
while (have_posts()) : the_post();
    $p = wc_get_product(get_the_ID());
    [$catSlug, $catName] = om_product_cat($p);
    $tag = $p->get_meta('_om_tag');
    $grad = $p->get_meta('_om_gradient') ?: 'g1';
    $size = om_product_size($p);
    $desc = array_values(array_filter(array_map('trim', preg_split('#</p>\s*#', $p->get_description()))));
    $lead = isset($desc[0]) ? wp_strip_all_tags($desc[0]) : '';
    $rest = isset($desc[1]) ? wp_strip_all_tags($desc[1]) : '';
    $related = wc_get_products(['status' => 'publish', 'visibility' => 'catalog', 'limit' => 4, 'exclude' => [$p->get_id()], 'orderby' => 'menu_order', 'order' => 'ASC', 'category' => [$catSlug]]);
    if (count($related) < 4) {
        $more = wc_get_products(['status' => 'publish', 'visibility' => 'catalog', 'limit' => 4 - count($related), 'orderby' => 'menu_order', 'order' => 'ASC',
            'exclude' => array_merge([$p->get_id()], array_map(fn($r) => $r->get_id(), $related))]);
        $related = array_merge($related, $more);
    }
    ?>
<div class="container">
  <nav class="crumbs" aria-label="Putanja"><a href="<?php echo esc_url(home_url('/')); ?>">Početna</a><span>/</span><a href="<?php echo esc_url(home_url('/#proizvodi')); ?>">Proizvodi</a><span>/</span><?php echo esc_html($p->get_name()); ?></nav>
  <section class="pd">
    <div class="pd-img <?php echo esc_attr($grad); ?>"><?php if ($tag) : ?><span class="tag pd-tag"><?php echo esc_html($tag); ?></span><?php endif; ?><?php echo om_product_visual($p); ?></div>
    <div class="pd-info">
      <span class="eyebrow"><?php echo esc_html($catName); ?></span>
      <h1><?php echo esc_html($p->get_name()); ?></h1>
      <div class="pd-price"><?php echo wp_kses_post($p->get_price_html()); ?><small><?php echo esc_html($size); ?></small></div>
      <p class="pd-lead"><?php echo esc_html($lead); ?></p>
      <?php if ($p->is_in_stock()) : ?>
      <form class="pd-buy" method="post" action="<?php echo esc_url($p->get_permalink()); ?>">
        <div class="qty" role="group" aria-label="Količina">
          <button type="button" data-d="-1" aria-label="Smanji količinu">−</button>
          <output id="q" aria-live="polite">1</output>
          <button type="button" data-d="1" aria-label="Povećaj količinu">+</button>
        </div>
        <input type="hidden" name="quantity" id="qty-input" value="1">
        <button type="submit" name="add-to-cart" value="<?php echo esc_attr($p->get_id()); ?>" class="btn btn-primary">Dodaj u korpu</button>
      </form>
      <?php else : ?>
      <p class="out-note" role="status">Trenutno nema. Javite nam se za narudžbinu kad stigne nova berba.</p>
      <?php endif; ?>
      <ul class="pd-trust">
        <li><svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7h11v9H3zM14 10h4l3 3v3h-7"/><circle cx="7" cy="17.5" r="1.5"/><circle cx="17" cy="17.5" r="1.5"/></svg>Dostava 1 do 3 radna dana</li>
        <li><svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M17 6a7 7 0 1 0 0 12M4 10h9M4 14h9"/></svg>Plaćanje pouzećem</li>
      </ul>
      <?php if ($rest) : ?><p><?php echo esc_html($rest); ?></p><?php endif; ?>
      <dl class="facts">
        <?php foreach ($p->get_attributes() as $a) : if (!$a->get_visible()) continue; ?>
          <dt><?php echo esc_html(wc_attribute_label($a->get_name())); ?></dt><dd><?php echo esc_html($p->get_attribute($a->get_name())); ?></dd>
        <?php endforeach; ?>
      </dl>
    </div>
  </section>

  <?php if ($related) : ?>
  <section class="pd-related">
    <h2>Možda će vam se svidjeti</h2>
    <div class="grid">
      <?php foreach ($related as $r) get_template_part('template-parts/product-card', null, ['product' => $r]); ?>
    </div>
  </section>
  <?php endif; ?>
</div>
<?php endwhile;
get_footer();
