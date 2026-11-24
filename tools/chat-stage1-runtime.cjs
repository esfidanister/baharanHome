const { chromium } = require('../.qa-playwright/node_modules/playwright-core');

(async () => {
  const browser = await chromium.launch({
    headless: true,
    executablePath: 'C:/Program Files/Google/Chrome/Application/chrome.exe',
  });
  const result = { viewports: [], requests: [], consoleErrors: [], phpVisibleErrors: [] };
  const page = await browser.newPage({ viewport: { width: 390, height: 844 } });
  page.on('console', msg => { if (msg.type() === 'error') result.consoleErrors.push(msg.text()); });
  page.on('request', req => { if (req.url().includes('/admin-ajax.php')) result.requests.push({ method: req.method(), postData: req.postData() }); });
  page.on('response', async response => {
    if (response.url().includes('/admin-ajax.php')) result.requests[result.requests.length - 1].status = response.status();
  });
  await page.goto('http://localhost:8088/', { waitUntil: 'networkidle', timeout: 60000 });
  const widths = [320, 360, 375, 393, 412, 480, 600, 768, 900, 1024, 1280, 1440];
  for (const width of widths) {
    await page.setViewportSize({ width, height: 844 });
    await page.waitForTimeout(100);
    result.viewports.push(await page.evaluate((width) => {
      const widget = document.querySelector('#bhc-widget');
      const panel = document.querySelector('.bhc-panel');
      const launcher = document.querySelector('.bhc-launch');
      const nav = document.querySelector('.bhp-mobile-nav');
      const r = x => x && ({ left: Math.round(x.left), right: Math.round(x.right), top: Math.round(x.top), bottom: Math.round(x.bottom), width: Math.round(x.width), height: Math.round(x.height) });
      const wr = widget?.getBoundingClientRect(), pr = panel?.getBoundingClientRect(), lr = launcher?.getBoundingClientRect(), nr = nav?.getBoundingClientRect();
      return { width, overflow: document.documentElement.scrollWidth > document.documentElement.clientWidth, widget: r(wr), panel: r(pr), launcher: r(lr), nav: r(nr), aria: { expanded: launcher?.getAttribute('aria-expanded'), controls: launcher?.getAttribute('aria-controls') } };
    }, width));
  }
  await page.setViewportSize({ width: 390, height: 844 });
  await page.locator('.bhc-launch').click();
  const opened = await page.locator('.bhc-panel').isVisible();
  const greeting = await page.locator('.bhc-bot').innerText();
  await page.locator('textarea[name="message"]').fill('سلام');
  await page.locator('.bhc-form button[type="submit"]').click();
  await page.waitForTimeout(1200);
  result.chat = { opened, greeting, messages: await page.locator('.bhc-messages').innerText(), ariaExpanded: await page.locator('.bhc-launch').getAttribute('aria-expanded') };
  await page.locator('.bhc-close').click();
  result.chat.closed = !(await page.locator('.bhc-panel').isVisible());
  await page.reload({ waitUntil: 'networkidle', timeout: 60000 });
  result.aiKeyInHtml = /ai_key|Bearer\s+[A-Za-z0-9]/i.test(await page.content());
  result.bhi = await page.evaluate(() => window.BHI ? { userId: window.BHI.userId, nonce: window.BHI.nonce } : null);
  result.consoleErrors = [...new Set(result.consoleErrors)];
  console.log(JSON.stringify(result, null, 2));
  await browser.close();
})().catch(error => { console.error(error); process.exit(1); });
