<?php get_header(); ?>
<section class="section"><div class="container">
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
  <article>
    <div class="section-head"><h2><?php the_title(); ?></h2></div>
    <div class="entry"><?php the_content(); ?></div>
  </article>
<?php endwhile; endif; ?>
</div></section>
<?php get_footer();
