# Testovi toka kupovine

Jedan seam: tok kupovine u pregledaču (Proizvod, Korpa, Narudžbina pouzećem, e-mailovi). Testovi gađaju lokalni sajt
(Local, `organski-med.local`) i prave probne narudžbine sa e-mailom `e2e@test.local`.

## Priprema (jednom)

1. Pokrenite sajt u Local-u.
2. `php tools/e2e-setup.php "<putanja do wp-load.php>"` (isti PHP flagovi kao za ostale skripte u `tools/`).
   Uključuje privatni link za "Coming soon" mod, pravi skriveni probni Proizvod bez zalihe i piše `tests/e2e/.env`
   (adresa sajta, Mailpit i ključ; fajl nije u gitu). Ako je Mailpit na drugom portu, dodajte ga kao drugi argument.
3. `cd tests/e2e && npm install`. Testovi koriste instalirani Google Chrome, bez dodatnog preuzimanja.

## Pokretanje

    cd tests/e2e
    npm test

## Čišćenje

`php tools/e2e-cleanup.php "<putanja do wp-load.php>"` premješta probne narudžbine u smeće.
