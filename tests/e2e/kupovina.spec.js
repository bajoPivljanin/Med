const { test, expect } = require('@playwright/test');

const MAILPIT = process.env.OM_MAILPIT_URL || 'http://localhost:10030';
const SHARE_KEY = process.env.OM_SHARE_KEY;
const OUT_OF_STOCK_ID = process.env.OM_OUT_OF_STOCK_ID;
const norm = s => s.replace(/\u00a0/g, ' ').replace(/\s+/g, ' ').trim();

test.beforeAll(() => {
  if (!SHARE_KEY) throw new Error('Nedostaje OM_SHARE_KEY. Pokrenite tools/e2e-setup.php (vidi tests/e2e/README.md).');
});

// Sajt je u "Coming soon" modu: test ulazi preko privatnog linka, kao posjetilac bez prijave.
test.beforeEach(async ({ page }) => {
  await page.goto(`/?woo-share=${SHARE_KEY}`);
});

async function addFromHome(page, name) {
  await page.goto('/');
  await page.getByRole('link', { name: `Dodaj ${name} u korpu` }).click();
  await expect(page.locator('.om-notices')).toContainText('dodato u korpu');
}

async function mailFor(request, subjectPart) {
  for (let i = 0; i < 20; i++) {
    const res = await request.get(`${MAILPIT}/api/v1/search`, { params: { query: `subject:"${subjectPart}"` } });
    const body = await res.json();
    if (body.messages && body.messages.length) return body.messages[0];
    await new Promise(r => setTimeout(r, 750));
  }
  return null;
}

test('narudžbina ispod 50 €: dostava 3 €, plaćanje pouzećem, potvrde e-mailom', async ({ page, request }) => {
  await addFromHome(page, 'Livadski med');

  await page.goto('/korpa/');
  await expect(page.locator('.ship-note')).toContainText('Dodajte još 30 € do besplatne dostave');
  const totals = norm(await page.locator('.cart_totals').innerText());
  expect(totals).toContain('Dostava: 3 €');
  expect(totals).toMatch(/Ukupno 23 €/);

  await page.getByRole('link', { name: 'Nastavi na narudžbinu' }).click();
  await expect(page).toHaveURL(/narudzbina/);

  // Samo Crna Gora i samo plaćanje pouzećem
  await expect(page.locator('#billing_country')).toHaveValue('ME');
  await expect(page.locator('.wc_payment_method')).toHaveCount(1);
  await expect(page.locator('.wc_payment_method')).toContainText('Plaćanje pouzećem');

  await page.fill('#billing_first_name', 'Test');
  await page.fill('#billing_last_name', 'Kupac');
  await page.fill('#billing_address_1', 'Ulica Test 1');
  await page.fill('#billing_city', 'Podgorica');
  await page.fill('#billing_postcode', '81000');
  await page.fill('#billing_phone', '067000000');
  await page.fill('#billing_email', 'e2e@test.local');
  await page.locator('#place_order').click();

  await expect(page).toHaveURL(/order-received/);
  const overview = norm(await page.locator('.woocommerce-order-overview').innerText());
  expect(overview).toContain('23 €');
  expect(overview).toContain('Plaćanje pouzećem');
  const orderNo = norm(await page.locator('.woocommerce-order-overview__order strong').innerText());

  // Kupac dobija potvrdu, vlasnik obavještenje
  const customerMail = await mailFor(request, `Vaša narudžbina #${orderNo} je primljena`);
  expect(customerMail, 'potvrda kupcu nije stigla').toBeTruthy();
  expect(customerMail.To.map(t => t.Address)).toContain('e2e@test.local');
  const ownerMail = await mailFor(request, `Nova narudžbina #${orderNo}`);
  expect(ownerMail, 'obavještenje vlasniku nije stiglo').toBeTruthy();

  // Korpa je prazna poslije narudžbine
  await page.goto('/');
  await expect(page.locator('.nav-end .cart span')).toHaveText('0');
});

test('narudžbina od 50 € naviše: besplatna dostava', async ({ page }) => {
  await page.goto('/product/livada/');
  await page.locator('.qty button[data-d="1"]').click();
  await page.locator('.qty button[data-d="1"]').click();
  await expect(page.locator('#q')).toHaveText('3');
  await page.getByRole('button', { name: 'Dodaj u korpu' }).click();

  await page.goto('/korpa/');
  await expect(page.locator('.ship-note')).toContainText('Imate besplatnu dostavu');
  const totals = norm(await page.locator('.cart_totals').innerText());
  expect(totals).toContain('Besplatna dostava');
  expect(totals).toMatch(/Ukupno 60 €/);
});

test('proizvod bez zalihe: "Trenutno nema" i ne može u korpu', async ({ page }) => {
  await page.goto('/product/e2e-nema-na-zalihi/');
  await expect(page.locator('.out-note')).toContainText('Trenutno nema');
  await expect(page.getByRole('button', { name: 'Dodaj u korpu' })).toHaveCount(0);

  // Direktan pokušaj dodavanja ne uspijeva
  await page.goto(`/?add-to-cart=${OUT_OF_STOCK_ID}`);
  await expect(page.locator('.om-notices')).toContainText('ne može se dodati u korpu');
  await expect(page.locator('.nav-end .cart span')).toHaveText('0');
});
