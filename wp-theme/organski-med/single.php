<?php
/** Članak. */
get_header();
while (have_posts()) : the_post();
    $more = get_posts(['numberposts' => 2, 'exclude' => [get_the_ID()], 'post_status' => 'publish']);
    ?>
<div class="container">
  <nav class="crumbs" aria-label="Putanja"><a href="<?php echo esc_url(home_url('/')); ?>">Početna</a><span>/</span><a href="<?php echo esc_url(home_url('/blog/')); ?>">Blog</a><span>/</span><?php echo esc_html(om_post_cat()); ?></nav>
  <article class="article">
    <header>
      <div class="post-meta"><span class="eyebrow"><?php echo esc_html(om_post_cat()); ?></span> · <?php echo esc_html(om_fmt_date()); ?> · <?php echo esc_html(om_read_time()); ?></div>
      <h1><?php the_title(); ?></h1>
      <p class="article-lead"><?php echo esc_html(get_the_excerpt()); ?></p>
    </header>
    <?php echo om_post_cover(null, 'article-cover'); ?>
    <div class="prose"><?php the_content(); ?></div>
    <div class="article-cta">
      <div><h3>Probajte domaći med</h3><p>Bagrem, livada, kesten i žalfija, direktno od pčelara.</p></div>
      <a href="<?php echo esc_url(home_url('/#proizvodi')); ?>" class="btn btn-primary">Pogledaj proizvode</a>
    </div>
  </article>
  <?php if ($more) : ?>
  <section class="pd-related">
    <h2>Još sa bloga</h2>
    <div class="posts posts-2">
      <?php foreach ($more as $m) : ?>
      <article class="post-card">
        <a href="<?php echo esc_url(get_permalink($m)); ?>" aria-label="<?php echo esc_attr(get_the_title($m)); ?>"><?php echo om_post_cover($m); ?></a>
        <div class="post-body">
          <div class="post-meta"><span class="eyebrow"><?php echo esc_html(om_post_cat($m)); ?></span> · <?php echo esc_html(om_fmt_date($m)); ?></div>
          <h3><a href="<?php echo esc_url(get_permalink($m)); ?>"><?php echo esc_html(get_the_title($m)); ?></a></h3>
          <p><?php echo esc_html(get_the_excerpt($m)); ?></p>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>
</div>
<?php endwhile;
get_footer();
