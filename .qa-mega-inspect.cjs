const { chromium } = require('./.qa-playwright/node_modules/playwright-core');

(async () => {
  const browser = await chromium.launch({
    headless: true,
    executablePath: 'C:/Program Files/Google/Chrome/Application/chrome.exe',
  });
  const page = await browser.newPage({ viewport: { width: 1280, height: 800 } });
  await page.goto('http://localhost:8088/?qa=css', { waitUntil: 'commit', timeout: 25000 });
  await page.waitForTimeout(900);
  const rules = await page.evaluate(async () => {
    const result = [];
    for (const link of document.querySelectorAll('link[rel="stylesheet"]')) {
      try {
        const text = await (await fetch(link.href)).text();
        const matches = text.match(/[^{}]*bhp-mega-panel[^{}]*\{[^}]*\}/g) || [];
        if (matches.length) result.push({ href: link.href, matches: matches.slice(-8) });
      } catch (_) {}
    }
    return result;
  });
  await page.locator('.bhp-mega summary').click();
  const ancestors = await page.locator('.bhp-mega-panel').evaluate((element) => {
    const result = [];
    for (let node = element; node; node = node.parentElement) {
      const style = getComputedStyle(node);
      result.push({ tag: node.tagName, cls: String(node.className), transform: style.transform, filter: style.filter, perspective: style.perspective, contain: style.contain, willChange: style.willChange, contentVisibility: style.contentVisibility });
    }
    return result;
  });
  const computed = await page.locator('.bhp-mega-panel').evaluate((element) => {
    const style = getComputedStyle(element);
    const rect = element.getBoundingClientRect();
    return { rect: [rect.left, rect.right], left: style.left, right: style.right, width: style.width, transform: style.transform, marginLeft: style.marginLeft, marginRight: style.marginRight };
  });
  console.log(JSON.stringify({ rules, computed, ancestors }, null, 2));
  await browser.close();
})().catch((error) => { console.error(error); process.exit(1); });
