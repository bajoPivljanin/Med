<?php get_header(); ?>
<header class="hero"><div class="container">
  <div>
    <span class="eyebrow">Domaći pčelinjak u Crnoj Gori</span>
    <h1>Organski med, <em>prirodan</em> i domaći.</h1>
    <p class="lead">Prirodni med iz Crne Gore, direktno od pčelara. Bez dodataka i bez prerade. Svaku teglu pažljivo cijedimo i pakujemo, da dobijete ukus prirode kakav jeste.</p>
    <div class="hero-actions">
      <a href="#proizvodi" class="btn btn-primary">Naruči med →</a>
      <a href="#o-nama" class="btn btn-outline">Naša priča</a>
    </div>
  </div>
  <div class="hero-visual">
    <?php $heroPhoto = om_theme_photo('om_hero_image', 'large', 'hero-photo'); ?>
    <?php if ($heroPhoto) : echo $heroPhoto; else : ?>
    <svg class="jar" viewBox="0 0 100 130" fill="none" role="img" aria-label="Tegla organskog meda">
      <rect x="22" y="4" width="56" height="16" rx="4" fill="#3B2412"/>
      <path d="M18 24h64v90a12 12 0 0 1-12 12H30a12 12 0 0 1-12-12z" fill="#fff" fill-opacity=".35" stroke="#3B2412" stroke-width="3"/>
      <path d="M22 50h56v64a8 8 0 0 1-8 8H30a8 8 0 0 1-8-8z" fill="#B67F0E"/>
      <rect x="30" y="68" width="40" height="32" rx="6" fill="#FFF6DC"/><text x="50" y="89" font-size="11" text-anchor="middle" fill="#3B2412" font-weight="800" font-family="serif">MED</text>
    </svg>
        <?php endif; ?>
    <span class="chip a"><svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 19c0-9 5-14 14-14 0 9-5 14-14 14zM5 19l7-7"/></svg>100% prirodno</span>
    <span class="chip b"><svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7h11v9H3zM14 10h4l3 3v3h-7"/><circle cx="7" cy="17.5" r="1.5"/><circle cx="17" cy="17.5" r="1.5"/></svg>Dostava širom Crne Gore</span>
    <span class="chip c"><svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9z"/></svg>Preko 500 kupaca</span>
  </div>
</div></header>

<section class="stats"><div class="container">
  <div><b>20+</b><span>Godina iskustva</span></div>
  <div><b>270</b><span>Košnica</span></div>
  <div><b>500+</b><span>Zadovoljnih kupaca</span></div>
  <div><b>100%</b><span>Domaća proizvodnja</span></div>
</div></section>
<div class="trust"><div class="container">
  <span><svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7h11v9H3zM14 10h4l3 3v3h-7"/><circle cx="7" cy="17.5" r="1.5"/><circle cx="17" cy="17.5" r="1.5"/></svg>Dostava 1 do 3 dana</span>
  <span><svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 8l9-5 9 5v8l-9 5-9-5zM3 8l9 5 9-5M12 13v8"/></svg>Sigurno pakovanje</span>
  <span><svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M17 6a7 7 0 1 0 0 12M4 10h9M4 14h9"/></svg>Plaćanje pouzećem</span>
  <span><svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>Garancija kvaliteta</span>
</div></div>

<section class="section" id="proizvodi"><div class="container">
  <div class="section-head"><span class="eyebrow">Naša ponuda</span><h2>Domaći med iz Crne Gore</h2><p>Od nježnog bagrema do jakog kestena, izaberite svoj omiljeni prirodni med.</p></div>
  <div class="filters">
    <button class="active" aria-pressed="true" data-f="all">Svi</button><button aria-pressed="false" data-f="med">Med</button><button aria-pressed="false" data-f="pcelinji-proizvodi">Pčelinji proizvodi</button><button aria-pressed="false" data-f="poklon">Poklon</button>
  </div>
  <div class="grid" id="shop">
    <?php
    $products = function_exists('wc_get_products') ? wc_get_products(['status' => 'publish', 'visibility' => 'catalog', 'limit' => -1, 'orderby' => 'menu_order', 'order' => 'ASC']) : [];
    foreach ($products as $product) get_template_part('template-parts/product-card', null, ['product' => $product]);
    ?>
  </div>
</div></section>

<section class="section benefits"><div class="container">
  <div class="section-head"><span class="eyebrow">Zašto organski med</span><h2>Dobro za vas, svaki dan</h2></div>
  <div class="benefit-grid">
    <div class="benefit feature"><div class="ic"><svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3s6 6.5 6 11a6 6 0 0 1-12 0c0-4.5 6-11 6-11z"/></svg></div><h3>Čist i nepatvoren</h3><p>Naš prirodni med nije zagrijavan niti miješan. Zadržava sve enzime i vitamine, a u tegli je upravo ono što je pčela donijela u košnicu.</p></div>
    <div class="benefit"><div class="ic"><svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M13 2L5 14h6l-1 8 8-12h-6z"/></svg></div><h3>Prirodna energija</h3><p>Zdrava zamjena za rafinisani šećer, ukusna i hranljiva.</p></div>
    <div class="benefit"><div class="ic"><svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s7-6 7-11a7 7 0 0 0-14 0c0 5 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg></div><h3>Poznato porijeklo</h3><p>Znamo svaku livadu i svaku košnicu u Crnoj Gori iz koje dolazi vaš med.</p></div>
  </div>
</div></section>

<section class="section"><div class="container">
  <div class="section-head"><span class="eyebrow">Proces</span><h2>Od cvijeta do vašeg stola</h2></div>
  <div class="steps">
    <div class="step"><h3>Paša</h3><p>Pčele skupljaju nektar sa cvjetnih livada.</p></div>
    <div class="step"><h3>Vrcanje</h3><p>Med se hladno vrca iz saća.</p></div>
    <div class="step"><h3>Filtriranje</h3><p>Samo cijeđenje, bez zagrijavanja.</p></div>
    <div class="step"><h3>Dostava</h3><p>Pakujemo i šaljemo na vašu adresu u Crnoj Gori.</p></div>
  </div>
</div></section>

<section class="section about" id="o-nama"><div class="container">
  <div>
    <span class="eyebrow">O nama</span><h2>Porodična tradicija u svakoj tegli</h2>
    <p>Ovdje ide vaša priča: kako je počelo, koliko godina se bavite pčelarstvom i u kom dijelu Crne Gore se nalazi vaš pčelinjak.</p>
    <p>Zamijenite ovaj tekst stvarnim opisom vašeg pčelinjaka.</p>
    <a href="#kontakt" class="btn btn-primary btn-mt">Kontaktirajte nas</a>
  </div>
  <div class="photo"><?php $aboutPhoto = om_theme_photo('om_about_image', 'large', 'about-photo'); echo $aboutPhoto ?: '[ fotografija pčelara / pčelinjaka ]'; ?></div>
</div></section>

<section class="section"><div class="container">
  <div class="section-head"><span class="eyebrow">Recenzije · primjer teksta</span><h2>Šta kažu kupci</h2></div>
  <div class="quotes">
    <div class="quote"><div class="stars">★★★★★</div><p>„Najbolji bagrem koji sam probala. Stiglo za dva dana.“</p><b>Kupac 1, Podgorica</b></div>
    <div class="quote"><div class="stars">★★★★★</div><p>„Pravi domaći med, ukus iz djetinjstva.“</p><b>Kupac 2, Nikšić</b></div>
    <div class="quote"><div class="stars">★★★★★</div><p>„Naručujemo već treću godinu zaredom.“</p><b>Kupac 3, Budva</b></div>
  </div>
</div></section>

<section class="section benefits" id="faq"><div class="container">
  <div class="section-head"><span class="eyebrow">Pitanja</span><h2>Česta pitanja o prirodnom medu</h2></div>
  <div class="faq">
    <details><summary>Koliko traje dostava meda u Crnoj Gori?</summary><p>Dostava širom Crne Gore traje obično 1 do 3 radna dana.</p></details>
    <details><summary>Kako mogu platiti organski med?</summary><p>Plaćanje je pouzećem, prilikom preuzimanja paketa.</p></details>
    <details><summary>Zašto se prirodni med kristalizuje?</summary><p>To je prirodan proces i znak pravog meda. Teglu zagrijte u mlakoj vodi i med će ponovo postati tečan.</p></details>
  </div>
</div></section>

<section class="cta" id="kontakt"><div class="container">
  <h2>Spremni za pravi domaći med?</h2><p>Naručite danas, a mi ga šaljemo već sutra.</p>
  <a href="#proizvodi" class="btn btn-primary">Pogledaj proizvode →</a>
</div></section>
<?php get_footer();
