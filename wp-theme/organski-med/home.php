<?php
/** Blog: lista objavljenih članaka (stranica za objave). */
get_header();
$posts = $GLOBALS['wp_query']->posts;
$first = array_shift($posts);
?>
<section class="section blog-head"><div class="container">
  <div class="section-head"><span class="eyebrow">Blog</span><h1>Sve o medu, iz prve ruke</h1><p>Savjeti, vodiči i priče iz pčelinjaka.</p></div>
  <?php if (!$first) : ?>
    <p class="empty-note">Uskoro stižu prvi članci.</p>
  <?php else : ?>
  <article class="blog-feature">
    <a href="<?php echo esc_url(get_permalink($first)); ?>" aria-label="<?php echo esc_attr(get_the_title($first)); ?>"><?php echo om_post_cover($first); ?></a>
    <div class="post-body">
      <div class="post-meta"><span class="eyebrow"><?php echo esc_html(om_post_cat($first)); ?></span> · <?php echo esc_html(om_fmt_date($first)); ?> · <?php echo esc_html(om_read_time($first)); ?></div>
      <h2><a href="<?php echo esc_url(get_permalink($first)); ?>"><?php echo esc_html(get_the_title($first)); ?></a></h2>
      <p><?php echo esc_html(get_the_excerpt($first)); ?></p>
      <a href="<?php echo esc_url(get_permalink($first)); ?>" class="btn btn-outline">Pročitaj članak</a>
    </div>
  </article>
  <?php if ($posts) : ?>
  <div class="posts">
    <?php foreach ($posts as $p) : ?>
    <article class="post-card">
      <a href="<?php echo esc_url(get_permalink($p)); ?>" aria-label="<?php echo esc_attr(get_the_title($p)); ?>"><?php echo om_post_cover($p); ?></a>
      <div class="post-body">
        <div class="post-meta"><span class="eyebrow"><?php echo esc_html(om_post_cat($p)); ?></span> · <?php echo esc_html(om_fmt_date($p)); ?></div>
        <h3><a href="<?php echo esc_url(get_permalink($p)); ?>"><?php echo esc_html(get_the_title($p)); ?></a></h3>
        <p><?php echo esc_html(get_the_excerpt($p)); ?></p>
        <span class="post-meta"><?php echo esc_html(om_read_time($p)); ?></span>
      </div>
    </article>
    <?php endforeach; ?>
  </div>
  <?php endif; endif; ?>
</div></section>
<?php get_footer();
