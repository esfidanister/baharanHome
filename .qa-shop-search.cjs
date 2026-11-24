const { chromium } = require('./.qa-playwright/node_modules/playwright-core');

(async () => {
  const browser = await chromium.launch({
    headless: true,
    executablePath: 'C:/Program Files/Google/Chrome/Application/chrome.exe',
  });
  const page = await browser.newPage({ viewport: { width: 390, height: 844 } });
  const failures = [];
  page.on('requestfailed', request => failures.push({ url: request.url(), error: request.failure()?.errorText }));
  page.on('console', message => failures.push({ console: message.text() }));
  page.on('response', async response => {
    if (response.url().includes('admin-ajax.php')) {
      failures.push({ ajax: response.status(), url: response.url(), body: (await response.text()).slice(0, 300) });
    }
  });
  await page.goto('http://localhost:8088/shop/?qa=search', { waitUntil: 'commit', timeout: 25000 });
  await page.waitForTimeout(1000);
  const input = page.locator('#bh-product-search');
  await input.fill('بوش');
  await page.waitForTimeout(1200);
  const result = await page.evaluate(() => {
    const box = document.querySelector('#bh-search-results');
    return {
      hidden: box?.hidden ?? true,
      count: box?.querySelectorAll('.bh-search-result').length ?? 0,
      text: (box?.innerText || '').slice(0, 240),
      overflow: document.documentElement.scrollWidth > document.documentElement.clientWidth,
      config: typeof BHSearch === 'undefined' ? null : { ajax: BHSearch.ajax, minimum: BHSearch.minimum, nonce: Boolean(BHSearch.nonce) },
    };
  });
  await page.screenshot({ path: '.qa-search-mobile.png', fullPage: false });
  console.log(JSON.stringify({ result, failures }));
  await browser.close();
})().catch((error) => { console.error(error); process.exit(1); });
