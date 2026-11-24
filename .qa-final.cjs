const { chromium } = require('./.qa-playwright/node_modules/playwright-core');
(async()=>{

const base = 'http://localhost:8088';
const browser = await chromium.launch({
  headless: true,
  executablePath: 'C:/Program Files/Google/Chrome/Application/chrome.exe',
});

async function open(page, path) {
  await page.goto(base + path, { waitUntil: 'commit', timeout: 60000 });
  await page.waitForSelector(path.startsWith('/shop') ? '.bhp-filters' : '.bhp-mega', { state: 'attached', timeout: 60000 });
  await page.waitForTimeout(150);
}

const result = { viewports: [], brandFilter: null, menu: [] };
for (const width of [1440, 1024, 768, 390]) {
  const page = await browser.newPage({ viewport: { width, height: 900 } });
  await open(page, '/shop/');
  const shop = await page.evaluate(() => ({
    width: innerWidth,
    overflow: document.documentElement.scrollWidth > document.documentElement.clientWidth,
    clientWidth: document.documentElement.clientWidth,
    scrollWidth: document.documentElement.scrollWidth,
    cards: document.querySelectorAll('ul.products li.product').length,
    pagination: document.querySelectorAll('.woocommerce-pagination a, .woocommerce-pagination span').length,
    filter: !!document.querySelector('.bhp-filters'),
    priceNotice: !!document.querySelector('.bhp-filter-unavailable'),
  }));
  if (width <= 1024) {
    await page.locator('.bhp-filter-open').click();
    await page.waitForTimeout(100);
    shop.filterOpen = await page.evaluate(() => {
      const el = document.querySelector('.bhp-filters');
      const r = el?.getBoundingClientRect();
      return { open: el?.classList.contains('is-open'), left: r && Math.round(r.left), right: r && Math.round(r.right), width: r && Math.round(r.width), overflow: document.documentElement.scrollWidth > document.documentElement.clientWidth };
    });
    await page.locator('.bhp-filter-close').click();
    shop.filterClosed = await page.evaluate(() => !document.querySelector('.bhp-filters')?.classList.contains('is-open'));
  }
  result.viewports.push(shop);
  await page.close();
}

{
  const page = await browser.newPage({ viewport: { width: 1440, height: 900 } });
  await open(page, '/shop/');
  const brandDetails = page.locator('.bhp-filters details').nth(1);
  if (!(await brandDetails.getAttribute('open'))) await brandDetails.locator('summary').click();
  const brandInput = page.locator('.bhp-brand-list label').filter({ hasText: 'بوش' }).locator('input');
  await brandInput.evaluate((input) => { input.checked = true; input.dispatchEvent(new Event('change', { bubbles: true })); });
  await page.locator('.bhp-filter-submit').click();
  await page.waitForTimeout(250);
  result.brandFilter = await page.evaluate(() => ({
    url: location.href,
    products: document.querySelectorAll('ul.products li.product').length,
    active: [...document.querySelectorAll('.bhp-active-filters a')].map((x) => x.textContent.trim()),
    fakeZeroPrice: /min_price=0|از ۰|تا ۰/.test(location.href + document.body.innerText),
  }));
  await page.close();
}

for (const width of [1440, 1024]) {
  const page = await browser.newPage({ viewport: { width, height: 900 } });
  await open(page, '/');
  await page.locator('.bhp-mega>summary').click();
  await page.waitForTimeout(150);
  result.menu.push(await page.evaluate(() => {
    const el = document.querySelector('.bhp-mega-panel');
    const r = el?.getBoundingClientRect();
    return { width: innerWidth, open: !!document.querySelector('.bhp-mega[open]'), left: r && Math.round(r.left), right: r && Math.round(r.right), panelWidth: r && Math.round(r.width), overflow: document.documentElement.scrollWidth > document.documentElement.clientWidth };
  }));
  await page.close();
}

console.log(JSON.stringify(result, null, 2));
await browser.close();
})().catch((error)=>{console.error(error);process.exit(1)});
