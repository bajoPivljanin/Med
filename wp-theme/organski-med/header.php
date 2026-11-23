<?php
$home = is_front_page();
$h = fn(string $id) => $home ? "#$id" : home_url("/#$id");
?><!doctype html>
<html lang="sr-Latn-ME">
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="theme-color" content="#FFFCF5">
<noscript><style>.curtain{display:none}</style></noscript>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a href="#main" class="skip">Preskoči na sadržaj</a>
<div class="curtain" aria-hidden="true"><span>Organski med</span></div>
<div class="progress" aria-hidden="true"></div>
<div class="promo">Besplatna dostava širom Crne Gore za narudžbine preko 50 € · Plaćanje pouzećem</div>
<nav class="nav" aria-label="Glavna navigacija"><div class="container">
  <a href="<?php echo esc_url($home ? '#' : home_url('/')); ?>" class="logo"><span class="logo-mark">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="#3B2412" aria-hidden="true"><path d="M12 2l8.66 5v10L12 22l-8.66-5V7z"/></svg></span>Organski med</a>
  <ul id="menu">
    <li><a href="<?php echo esc_url($h('proizvodi')); ?>">Proizvodi</a></li>
    <li><a href="<?php echo esc_url($h('o-nama')); ?>">O nama</a></li>
    <li><a href="<?php echo esc_url(home_url('/blog/')); ?>">Blog</a></li>
    <li><a href="<?php echo esc_url($h('faq')); ?>">Pitanja</a></li>
    <li><a href="<?php echo esc_url($h('kontakt')); ?>">Kontakt</a></li>
  </ul>
  <div class="nav-end">
    <a href="<?php echo esc_url(om_cart_url()); ?>" class="cart" aria-label="Korpa">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3B2412" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M3 4h2l2.5 11h10L20 7H6"/><circle cx="9" cy="20" r="1.5"/><circle cx="17" cy="20" r="1.5"/></svg><span><?php echo om_cart_count(); ?></span></a>
    <button class="menu" type="button" aria-label="Meni" aria-expanded="false" aria-controls="menu">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3B2412" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg></button>
  </div>
</div></nav>
<main id="main">
<?php do_action('om_before_main'); ?>
