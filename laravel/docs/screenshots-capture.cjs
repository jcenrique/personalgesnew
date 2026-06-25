const { chromium } = require('playwright-chromium');
const path = require('path');
(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage();
  const base = 'http://127.0.0.1';
  const screenshots = [
    { url: `${base}/login`, file: 'login.png' },
    { url: `${base}/`, file: 'dashboard.png' },
    { url: `${base}/inspecciones`, file: 'inspecciones_list.png' },
    { url: `${base}/inspecciones/create`, file: 'inspecciones_create.png' },
  ];
  for (const item of screenshots) {
    try {
      await page.goto(item.url, { waitUntil: 'networkidle' });
      await page.screenshot({ path: path.join(__dirname, 'screenshots', item.file), fullPage: true });
      console.log('captured', item.file);
    } catch (err) {
      console.error('error', item.url, err.message);
    }
  }
  await browser.close();
})();
