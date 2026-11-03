/* Zajednički podaci i logika za sve stranice.
   Kad sajt pređe na WordPress/WooCommerce, ovo zamjenjuju proizvodi, članci i korpa iz baze. */

const SHIPPING = { fee: 3, freeFrom: 50 }; // pretpostavka: 3 € ispod 50 €, potvrdi stvarnu cijenu dostave

const label = { med: "Med", pcelinji: "Pčelinji proizvodi", poklon: "Poklon" };

const products = [
  { id: "bagrem", n: "Bagremov med", c: "med", d: "Svijetao, nježan i dugo tečan.", p: 20, w: "900 g", g: "g1", col: "#E8B93C", t: "Hit",
    long: ["Bagremov med je najsvjetiji i najblaži u ponudi. Okus je nježan i ne preglasi čaj, jogurt ni palačinke, pa ga rado biraju i oni koji inače ne vole jaku aromu.", "Dugo ostaje tečan, a kad kristališe, to je znak da je pravi. Teglu samo zagrijte u mlakoj vodi."],
    facts: [["Težina", "900 g"], ["Berba", "Maj"], ["Konzistencija", "Tečan, kristališe sporo"], ["Čuvanje", "Zatvoreno, na sobnoj temperaturi, dalje od sunca"]] },
  { id: "livada", n: "Livadski med", c: "med", d: "Bogata aroma ljekovitog bilja.", p: 20, w: "900 g", g: "g2", col: "#C98A22",
    long: ["Livadski med nastaje od cvijeća sa različitih livada, pa se okus malo razlikuje od berbe do berbe. Aroma je bogatija i zlatnija nego kod bagrema.", "Dobar je za svaki dan, uz hljeb i maslac ili u čaju od bilja."],
    facts: [["Težina", "900 g"], ["Berba", "Jun i jul"], ["Konzistencija", "Tečan, tokom zime može kristalisati"], ["Čuvanje", "Zatvoreno, na sobnoj temperaturi, dalje od sunca"]] },
  { id: "kesten", n: "Kesten", c: "med", d: "Taman i snažan, blago gorak.", p: 20, w: "900 g", g: "g2", col: "#8B5A1E",
    long: ["Kestenov med je taman, gust i snažnog okusa sa blagom gorčinom na kraju. Nije za svakoga, ali ko ga zavoli, rijetko mijenja.", "Odlično ide uz sireve i orahe, a u maloj količini daje karakter i toplim napicima."],
    facts: [["Težina", "900 g"], ["Berba", "Jun i jul"], ["Konzistencija", "Tečan, kristališe sporo"], ["Čuvanje", "Zatvoreno, na sobnoj temperaturi, dalje od sunca"]] },
  { id: "zalfija", n: "Med od žalfije", c: "med", d: "Mediteranski med sa ljekovitim svojstvima.", p: 20, w: "900 g", g: "g1", col: "#D9A92E",
    long: ["Med od žalfije dolazi sa sunčanih, kamenitih predjela. Svijetao je, blago začinski i prijatan uz topli čaj kad vas hvata prehlada.", "Okus je čist i lagano biljan, bez oštrine."],
    facts: [["Težina", "900 g"], ["Berba", "Maj i jun"], ["Konzistencija", "Tečan, vremenom može kristalisati"], ["Čuvanje", "Zatvoreno, na sobnoj temperaturi, dalje od sunca"]] },
  { id: "polen", n: "Polen", c: "pcelinji", d: "Prirodni cvjetni prah.", p: 7, w: "100 g", g: "g3", col: "#D4A62A",
    long: ["Cvjetni prah koji pčele skupljaju na nogicama i unose u košnicu. Prodaje se u obliku sitnih zrnaca.", "Dodajte ga u jogurt, kašu ili smuti, ili ga jedite kašikicom."],
    facts: [["Težina", "100 g"], ["Berba", "Maj do jul"], ["Oblik", "Suha zrnca"], ["Čuvanje", "Zatvoreno, u frižideru"]] },
  { id: "propolis", n: "Propolis", c: "pcelinji", d: "Prirodni štit za imunitet.", p: 6, w: "20 ml", g: "g4", col: "#9A5B1B",
    long: ["Propolis pčele prave od smola sa pupoljaka i koriste ga da zaštite košnicu. Ovdje je u obliku tečnog ekstrakta u maloj bočici.", "Koristi se u kapima, prema uputstvu na pakovanju."],
    facts: [["Zapremina", "20 ml"], ["Oblik", "Tečni ekstrakt"], ["Čuvanje", "Zatvoreno, na tamnom i hladnom mjestu"]] },
  { id: "sace", n: "Saće", c: "pcelinji", d: "Med u svom najčistijem obliku.", p: 25, w: "500 g", g: "g1", col: "#E0A526", t: "Novo",
    long: ["Med u saću, onakav kakav je u košnici. Saće se jede cijelo, zajedno sa voštanim zidovima ćelija.", "Lijepo izgleda na tanjiru sa sirevima, a može i kašikom direktno iz tegle."],
    facts: [["Težina", "500 g"], ["Berba", "Prema sezoni"], ["Oblik", "Cijelo saće"], ["Čuvanje", "Zatvoreno, na sobnoj temperaturi"]] },
  { id: "poklon", n: "Poklon paket", c: "poklon", d: "Tri meda u lijepoj kutiji.", p: 50, w: "3 × 250 g", g: "g4", col: "#B67F0E",
    long: ["Tri manje tegle meda u poklon kutiji. Dobar izbor za rođendan, praznike ili kad nekome želite da pošaljete nešto domaće.", "Sorte u paketu zavise od zaliha u trenutku slanja."],
    facts: [["Sadržaj", "3 tegle po 250 g"], ["Pakovanje", "Poklon kutija"], ["Čuvanje", "Zatvoreno, na sobnoj temperaturi, dalje od sunca"]] }
];

/* Članci su radni tekstovi: pregledaj ih i prilagodi prije objave. */
const posts = [
  { slug: "kako-vratiti-med-u-tecno-stanje", title: "Zašto se med kristališe i kako ga vratiti u tečno stanje", date: "2026-10-29", cat: "Savjeti", g: "g1", col: "#E8B93C",
    excerpt: "Kristalizacija nije kvar, nego prirodno ponašanje meda. Evo kako da ga vratite u tečno stanje, a da ne izgubi ništa od kvaliteta.",
    body: [["p", "Svako ko je kupio domaći med znao je da jednog jutra otvori teglu i u njoj nađe gustu, zrnastu masu. Mnogi pomisle da je med pokvaren. Nije. Kristalizacija je prirodan proces, a med koji se uopšte ne kristališe često je prethodno zagrijavan ili miješan."],
      ["h", "Zašto do toga dolazi"], ["p", "Med je zasićen rastvor šećera. Glukoze ima dovoljno da se vremenom izdvoji u sitne kristale. Što je više glukoze u odnosu na fruktozu, to će med brže kristalisati. Zato bagremov med ostaje tečan mjesecima, dok livadski često očvrsne preko zime."],
      ["h", "Kako ga vratiti u tečno stanje"], ["p", "Zatvorenu teglu stavite u posudu sa mlakom vodom, ne vrelom. Voda treba da bude topla koliko možete da držite ruku u njoj. Sačekajte pola sata, promiješajte i po potrebi ponovite. Mikrotalasna i ključala voda uništavaju enzime i miris."],
      ["h", "Može li da se spriječi"], ["p", "Ne može potpuno, ali se usporava. Čuvajte med na sobnoj temperaturi, bez čestih promjena. Frižider ubrzava kristalizaciju, a tegla na suncu kvari ukus."]] },
  { slug: "bagrem-livada-ili-kesten", title: "Bagrem, livada ili kesten: koji med za šta", date: "2026-09-22", cat: "Vodič", g: "g2", col: "#C98A22",
    excerpt: "Svaka vrsta meda ima drugačiji okus i drugu namjenu u kuhinji. Kratak vodič da izaberete pravi.",
    body: [["p", "Kad na polici stoje tri tegle meda, razlika često nije očigledna dok ne probate. Ovdje je kratak pregled onoga što smo sami primijetili kroz godine."],
      ["h", "Bagrem: za one koji žele nježno"], ["p", "Svijetao je i blag, pa ga dobro podnose djeca. Ne preglasi okus čaja ili mlijeka, pa je dobar izbor za svakodnevno zaslađivanje."],
      ["h", "Livadski: za svaki dan"], ["p", "Bogatiji je i zlatniji. Okus se mijenja prema tome šta je cvjetalo na livadama, pa je svaka berba malo drugačija. Dobar uz hljeb, jogurt i sir."],
      ["h", "Kesten: za one koji vole jako"], ["p", "Taman je i blago gorak. Odlično ide uz orahe, sireve i meso sa roštilja. Ako vam je prejak sam, probajte ga kao dodatak u marinadi."]] },
  { slug: "sta-pise-na-tegli", title: "Šta treba da piše na tegli pravog domaćeg meda", date: "2026-08-05", cat: "Savjeti", g: "g4", col: "#B67F0E",
    excerpt: "Domaći test sa vodom ili vatom ne dokazuje ništa. Evo čemu možete vjerovati kad birate med.",
    body: [["p", "Na internetu kruže razni kućni testovi za med: kap u čaši vode, plamen na fitilju, prstom po tegli. Nijedan od njih ne daje pouzdan odgovor. Pouzdanije je gledati ko prodaje i šta piše na pakovanju."],
      ["h", "Podaci koje treba tražiti"], ["p", "Ime i adresa proizvođača, vrsta meda, godina berbe, neto težina i rok trajanja. Ako prodavac ne zna da kaže gdje su košnice, to je razlog da budete oprezni."],
      ["h", "Ko stoji iza meda"], ["p", "Najbolja garancija je pčelar koji može da vam odgovori šta je cvjetalo i kad je med vrcan. Zato na našem sajtu pišemo berbu uz svaki proizvod."]] },
  { slug: "pcele-zimi", title: "Šta pčele rade zimi kad nema cvijeća", date: "2026-10-12", cat: "Iz pčelinjaka", g: "g3", col: "#D4A62A",
    excerpt: "Zimi u košnici je tiho, ali nije mirno. Pčele se okupe u klube i griju maticu cijelu zimu.",
    body: [["p", "Kad temperatura padne, pčele prestaju da izlijeću. U košnici se okupljaju u gusti klube oko matice i drhtanjem mišića održavaju toplotu. Spoljašnje pčele se postepeno mijenjaju sa onima iz sredine, tako da nijedna ne ostane predugo na hladnoći."],
      ["h", "Od čega žive"], ["p", "Od zaliha meda koje su same napravile. Zato pčelar na kraju sezone ostavlja dovoljno meda u košnici, a ne uzima sve."],
      ["h", "Šta radi pčelar"], ["p", "Zimi se košnice ne otvaraju. Posao je da se provjeri da li ima dovoljno zaliha, da nema vlage i da miševi ne uđu u košnicu. Pravi posao počinje u proljeće."]] }
];

const eur = n => (Number.isInteger(n) ? n : n.toFixed(2).replace(".", ",")) + " €";

const MONTHS = ["januar", "februar", "mart", "april", "maj", "jun", "jul", "avgust", "septembar", "oktobar", "novembar", "decembar"];
const fmtDate = s => { const [y, m, d] = s.split("-").map(Number); return `${d}. ${MONTHS[m - 1]} ${y}.`; };
const readTime = p => Math.max(1, Math.round(p.body.map(b => b[1]).join(" ").split(/\s+/).length / 180)) + " min čitanja";

const jar = c => `<svg viewBox="0 0 100 130" fill="none" aria-hidden="true"><rect x="22" y="4" width="56" height="16" rx="4" fill="#3B2412"/><path d="M18 24h64v90a12 12 0 0 1-12 12H30a12 12 0 0 1-12-12z" fill="#fff" fill-opacity=".4" stroke="#3B2412" stroke-width="3"/><path d="M22 50h56v64a8 8 0 0 1-8 8H30a8 8 0 0 1-8-8z" fill="${c}"/><rect x="30" y="68" width="40" height="32" rx="6" fill="#FFF6DC"/></svg>`;

/* Korpa: čuva se u pregledaču (localStorage), sa rezervom u memoriji */
const Cart = (() => {
  const KEY = "med-cart";
  let state = [];
  try { state = JSON.parse(localStorage.getItem(KEY)) || []; } catch { state = []; }
  state = state.filter(l => products.some(p => p.id === l.id) && l.q > 0);
  const save = () => { try { localStorage.setItem(KEY, JSON.stringify(state)); } catch { } sync(); };
  const sync = () => {
    const n = state.reduce((a, l) => a + l.q, 0);
    document.querySelectorAll(".cart span").forEach(s => { s.textContent = n; });
  };
  const api = {
    lines: () => state.map(l => ({ ...l, p: products.find(p => p.id === l.id) })),
    count: () => state.reduce((a, l) => a + l.q, 0),
    subtotal: () => api.lines().reduce((a, l) => a + l.p.p * l.q, 0),
    shipping: () => (state.length === 0 || api.subtotal() >= SHIPPING.freeFrom ? 0 : SHIPPING.fee),
    total: () => api.subtotal() + api.shipping(),
    add(id, q = 1) { const l = state.find(x => x.id === id); l ? l.q = Math.min(20, l.q + q) : state.push({ id, q }); save(); },
    set(id, q) { const l = state.find(x => x.id === id); if (!l) return; l.q = Math.max(1, Math.min(20, q)); save(); },
    remove(id) { state = state.filter(l => l.id !== id); save(); },
    clear() { state = []; save(); },
    bump() { document.querySelectorAll(".cart span").forEach(s => { s.classList.remove("bump"); void s.offsetWidth; s.classList.add("bump"); }); },
    sync
  };
  document.addEventListener("DOMContentLoaded", sync);
  return api;
})();

/* Zajedničko zaglavlje i podnožje */
const Site = {
  header(active) {
    const home = active === "home";
    const h = id => (home ? "#" : "index.html#") + id;
    const cur = k => (active === k ? ' aria-current="page"' : "");
    document.write(`
<a href="#main" class="skip">Preskoči na sadržaj</a>
<div class="curtain" aria-hidden="true"><span>Organski med</span></div>
<div class="progress" aria-hidden="true"></div>
<div class="promo">Besplatna dostava širom Crne Gore za narudžbine preko 50 € · Plaćanje pouzećem</div>
<nav class="nav" aria-label="Glavna navigacija"><div class="container">
  <a href="${home ? "#" : "index.html"}" class="logo"><span class="logo-mark">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="#3B2412" aria-hidden="true"><path d="M12 2l8.66 5v10L12 22l-8.66-5V7z"/></svg></span>Organski med</a>
  <ul id="menu">
    <li><a href="${h("proizvodi")}">Proizvodi</a></li>
    <li><a href="${h("o-nama")}">O nama</a></li>
    <li><a href="blog.html"${cur("blog")}>Blog</a></li>
    <li><a href="${h("faq")}">Pitanja</a></li>
    <li><a href="${h("kontakt")}">Kontakt</a></li>
  </ul>
  <div class="nav-end">
    <a href="korpa.html" class="cart" aria-label="Korpa"${cur("korpa")}>
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3B2412" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M3 4h2l2.5 11h10L20 7H6"/><circle cx="9" cy="20" r="1.5"/><circle cx="17" cy="20" r="1.5"/></svg><span>0</span></a>
    <button class="menu" type="button" aria-label="Meni" aria-expanded="false" aria-controls="menu">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3B2412" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg></button>
  </div>
</div></nav>`);
    document.addEventListener("DOMContentLoaded", () => {
      const nav = document.querySelector(".nav"), btn = nav.querySelector(".menu");
      btn.addEventListener("click", () => btn.setAttribute("aria-expanded", nav.classList.toggle("open")));
      nav.querySelectorAll("#menu a").forEach(a => a.addEventListener("click", () => { nav.classList.remove("open"); btn.setAttribute("aria-expanded", "false"); }));
    });
  },
  footer() {
    document.write(`
<footer><div class="container">
  <div class="foot-grid">
    <div><h4>Organski med</h4><p class="foot-note">Domaći i prirodni med iz Crne Gore, direktno od pčelara.</p></div>
    <div><h4>Shop</h4><ul><li><a href="index.html#proizvodi">Med</a></li><li><a href="index.html#proizvodi">Pčelinji proizvodi</a></li><li><a href="index.html#proizvodi">Pokloni</a></li></ul></div>
    <div><h4>Info</h4><ul><li><a href="index.html#o-nama">O nama</a></li><li><a href="blog.html">Blog</a></li><li><a href="index.html#faq">Dostava i plaćanje</a></li></ul></div>
    <div><h4>Kontakt</h4><ul><li><a href="tel:+3820000000">+382 6x xxx xxx</a></li><li><a href="mailto:info@primjer.me">info@primjer.me</a></li><li>Instagram</li></ul></div>
  </div>
  <div class="copy">© 2026 Organski med, Crna Gora. Sva prava zadržana. <span class="legal"><a href="#">Politika privatnosti</a> · <a href="#">Uslovi korišćenja</a></span></div>
</div></footer>`);
  }
};
