<?php
/** Kartica Proizvoda. Očekuje $args['product'] (WC_Product). */
$p = $args['product'];
[$catSlug, $catName] = om_product_cat($p);
$tag = $p->get_meta('_om_tag');
$grad = $p->get_meta('_om_gradient') ?: 'g1';
$size = om_product_size($p);
$name = $p->get_name();
?>
<article class="card<?php echo $p->is_in_stock() ? '' : ' is-out'; ?>" data-cat="<?php echo esc_attr($catSlug); ?>">
  <a class="img <?php echo esc_attr($grad); ?>" href="<?php echo esc_url($p->get_permalink()); ?>" aria-label="<?php echo esc_attr($name); ?>">
    <?php if ($tag) : ?><span class="tag"><?php echo esc_html($tag); ?></span><?php endif; ?>
    <?php echo om_product_visual($p); ?>
  </a>
  <div class="body">
    <span class="cat"><?php echo esc_html($catName); ?></span>
    <h3><a href="<?php echo esc_url($p->get_permalink()); ?>"><?php echo esc_html($name); ?></a></h3>
    <p><?php echo esc_html(wp_strip_all_tags($p->get_short_description())); ?></p>
    <div class="foot">
      <div class="price"><?php echo wp_kses_post($p->get_price_html()); ?><small><?php echo esc_html($size); ?></small></div>
      <?php if ($p->is_in_stock()) : ?>
        <a class="add" href="<?php echo esc_url($p->add_to_cart_url()); ?>" rel="nofollow" aria-label="<?php echo esc_attr('Dodaj ' . $name . ' u korpu'); ?>">+</a>
      <?php else : ?>
        <span class="out-badge">Trenutno nema</span>
      <?php endif; ?>
    </div>
  </div>
</article>
