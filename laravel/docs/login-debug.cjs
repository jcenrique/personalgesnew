const { chromium } = require('playwright-chromium');
const fs = require('fs');
(async ()=>{
  const browser = await chromium.launch({ args:['--no-sandbox'] });
  const page = await browser.newPage();
  await page.goto('http://127.0.0.1/login', { waitUntil: 'networkidle' });
  await page.waitForSelector('input#form\\.email', { timeout: 10000 });
  await page.fill('input#form\\.email', 'jcenrique@free.fr');
  await page.fill('input#form\\.password', 'Password123!');
  await page.click('button[type="submit"]');
  await page.waitForTimeout(8000);
  console.log('URL', page.url());
  const html = await page.content();
  const idx = html.indexOf('<form');
  const end = html.indexOf('</form>') + 7;
  console.log('form html:', html.substring(idx, end));
  await page.screenshot({ path: 'docs/screenshots/login_attempt.png', fullPage: true });
  await browser.close();
})();
