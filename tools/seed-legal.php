<?php
/**
 * Pravi NACRTE pravnih stranica (neobjavljene). Tekst nije pravno pregledan: mjesta u [uglastim zagradama]
 * treba popuniti, a cio tekst mora da potvrdi neko ko poznaje crnogorske propise o prodaji na daljinu
 * i zaštiti podataka o ličnosti. Može se pokretati više puta; ne vraća objavljenu stranicu u nacrt
 * i ne prepisuje tekst koji ste već izmijenili u editoru.
 *
 * Pokretanje: php -d extension=mysqli -d mysqli.default_port=10035 tools/seed-legal.php "<putanja do wp-load.php>"
 */
$wpLoad = $argv[1] ?? '';
if (!is_file($wpLoad)) { fwrite(STDERR, "Navedite putanju do wp-load.php\n"); exit(1); }
$_SERVER['HTTP_HOST'] = 'organski-med.local';
define('WP_USE_THEMES', false);
require $wpLoad;

$note = '<p class="draft-note"><strong>NACRT.</strong> Ovaj tekst nije pravno pregledan i ne smije se objaviti dok ga ne potvrdi neko ko poznaje crnogorske propise. Mjesta u [uglastim zagradama] treba popuniti.</p>';

$pages = [
 'uslovi-kupovine' => ['Uslovi kupovine', $note . '
<h2>Prodavac</h2>
<p>Prodavac je [NAZIV FIRME / PREDUZETNIKA], [ADRESA], PIB [PIB]. Podaci su i na stranici „Podaci o prodavcu“.</p>
<h2>Proizvodi i cijene</h2>
<p>Cijene su iskazane u eurima. [Navesti da li cijene sadrže PDV.] Slike su ilustrativne. Berba i težina navedeni su uz svaki proizvod.</p>
<h2>Naručivanje</h2>
<p>Narudžbina se ostavlja preko sajta, bez pravljenja naloga. Ugovor je zaključen kad prodavac potvrdi narudžbinu. Potvrdu dobijate e-mailom.</p>
<h2>Plaćanje</h2>
<p>Plaćanje je isključivo pouzećem, gotovinom kuriru prilikom preuzimanja paketa.</p>
<h2>Dostava</h2>
<p>Dostava je samo na teritoriji Crne Gore. Cijena dostave je 3 € za narudžbine ispod 50 €, a za narudžbine od 50 € naviše dostava je besplatna. Rok dostave je obično 1 do 3 radna dana. [Potvrditi cijenu i rok sa kurirskom službom.]</p>
<h2>Raspoloživost</h2>
<p>Neki proizvodi su sezonski. Proizvod koji nije na zalihi označen je kao „Trenutno nema“ i ne može se naručiti.</p>
<h2>Povrat i reklamacije</h2>
<p>Pogledajte stranicu „Povrat i reklamacije“.</p>
<h2>Mjerodavno pravo</h2>
<p>[Navesti mjerodavno pravo i nadležni organ za rješavanje sporova.]</p>'],

 'povrat-i-reklamacije' => ['Povrat i reklamacije', $note . '
<h2>Pravo na odustanak</h2>
<p>Kupac ima pravo da u roku od [14] dana od prijema proizvoda odustane od narudžbine bez navođenja razloga. [Potvrditi rok i izuzetke za hranu, na primjer otvorena ili zapečaćena pakovanja.]</p>
<h2>Kako odustati</h2>
<p>Javite nam se na [E-MAIL] ili [TELEFON] sa brojem narudžbine. Proizvod se vraća neotvoren, u originalnom pakovanju. [Navesti ko snosi trošak povrata.]</p>
<h2>Povrat novca</h2>
<p>Novac se vraća u roku od [14] dana od prijema vraćenog proizvoda. [Navesti način povrata, pošto je plaćanje pouzećem.]</p>
<h2>Reklamacije</h2>
<p>Ako proizvod stigne oštećen ili nije onakav kako je opisan, javite nam se u roku od [ROK] dana, sa fotografijom i brojem narudžbine. Na reklamaciju odgovaramo u roku od [ROK] dana.</p>
<h2>Kristalizacija meda</h2>
<p>Kristalizacija je prirodna pojava i nije nedostatak proizvoda. Savjet kako da vratite med u tečno stanje nalazi se na blogu.</p>'],

 'politika-privatnosti' => ['Politika privatnosti', $note . '
<h2>Ko obrađuje vaše podatke</h2>
<p>[NAZIV FIRME / PREDUZETNIKA], [ADRESA], [E-MAIL ZA PITANJA O PRIVATNOSTI].</p>
<h2>Koje podatke prikupljamo</h2>
<p>Prilikom narudžbine: ime i prezime, adresu, grad, poštanski broj, broj telefona, e-mail adresu i napomenu uz narudžbinu.</p>
<h2>Zašto ih koristimo</h2>
<p>Samo da obradimo i isporučimo narudžbinu, obavijestimo vas o njoj i odgovorimo na vaša pitanja. [Dopuniti pravni osnov obrade.]</p>
<h2>Sa kim ih dijelimo</h2>
<p>Kurirskoj službi, samo onoliko koliko je potrebno za dostavu. [Navesti naziv kurirske službe i eventualne druge obrađivače, na primjer hosting.]</p>
<h2>Koliko ih čuvamo</h2>
<p>[Navesti rok čuvanja, u skladu sa računovodstvenim i poreskim propisima.]</p>
<h2>Vaša prava</h2>
<p>Imate pravo da zatražite uvid, ispravku ili brisanje podataka, kao i da podnesete prigovor nadležnom organu. [Navesti organ i kontakt.]</p>
<h2>Kolačići</h2>
<p>[Opisati kolačiće koje sajt koristi, ili navesti da koristi samo neophodne.]</p>'],

 'podaci-o-prodavcu' => ['Podaci o prodavcu', $note . '
<ul>
<li>Naziv: [NAZIV FIRME / PREDUZETNIKA]</li>
<li>Adresa: [ADRESA]</li>
<li>PIB: [PIB]</li>
<li>Matični broj / upis u registar: [BROJ]</li>
<li>E-mail: [E-MAIL]</li>
<li>Telefon: [TELEFON]</li>
<li>Radno vrijeme za pitanja i narudžbine: [RADNO VRIJEME]</li>
</ul>'],
];

foreach ($pages as $slug => [$title, $content]) {
    $existing = get_page_by_path($slug);
    if ($existing) { echo "postoji #{$existing->ID} $slug ({$existing->post_status}), preskačem\n"; continue; }
    $id = wp_insert_post(['post_type' => 'page', 'post_title' => $title, 'post_name' => $slug, 'post_content' => $content, 'post_status' => 'draft']);
    echo "napravljeno #$id $slug (draft)\n";
}
echo "Gotovo.\n";
