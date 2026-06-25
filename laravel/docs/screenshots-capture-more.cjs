const { chromium } = require('playwright-chromium');
const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

const laravelRoot = path.resolve(__dirname, '..');
const sailPath = path.join(laravelRoot, 'vendor', 'bin', 'sail');
const base = 'http://127.0.0.1';
const folder = path.join(__dirname, 'screenshots');

const getAuthSession = () => {
  const php = `<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\\Contracts\\Console\\Kernel::class);
$kernel->bootstrap();
session()->start();
auth()->loginUsingId(1);
session()->save();
echo config('session.cookie').'::'.session()->getId();
`;

  const tmp = path.join(laravelRoot, 'docs', 'tmp-session.php');
  fs.writeFileSync(tmp, php, { encoding: 'utf8' });
  try {
    const output = execSync(`${JSON.stringify(sailPath)} php docs/tmp-session.php`, {
      cwd: laravelRoot,
      encoding: 'utf8',
      stdio: ['ignore', 'pipe', 'pipe'],
    }).trim();

    let [cookieName, sessionId] = output.split('::');
    if (!cookieName || !sessionId) {
      throw new Error(`Unexpected session output: ${output}`);
    }
    cookieName = cookieName.trim();
    sessionId = sessionId.trim();

    return { cookieName, sessionId };
  } finally {
    try { fs.unlinkSync(tmp); } catch (e) {}
  }
};

const capturePage = async (page, url, filename) => {
  console.log('Visiting', url);
  await page.goto(url, { waitUntil: 'networkidle' });
  await page.screenshot({ path: path.join(folder, filename), fullPage: true });
  console.log('Captured', filename);
};

(async () => {
  fs.mkdirSync(folder, { recursive: true });

  const browser = await chromium.launch({ args: ['--no-sandbox'] });
  const context = await browser.newContext();
  const page = await context.newPage();

  await capturePage(page, `${base}/login`, 'login_page.png');

  const { cookieName, sessionId } = getAuthSession();
  console.log('Using session cookie', cookieName, 'with session id', sessionId);

  const cookieObj = {
    name: cookieName,
    value: sessionId,
    url: `${base}/`,
  };
  console.log('Adding cookie:', JSON.stringify(cookieObj));
  await context.addCookies([cookieObj]);

  const screenshots = [
    { url: `${base}/`, file: 'dashboard_logged_in.png' },
    { url: `${base}/inspecciones`, file: 'inspecciones_list.png' },
    { url: `${base}/inspecciones/create`, file: 'inspecciones_create.png' },
    { url: `${base}/courses`, file: 'courses_list.png' },
    { url: `${base}/rechazos`, file: 'rechazos_list.png' },
    { url: `${base}/computos`, file: 'computos_list.png' },
    { url: `${base}/additionaldays`, file: 'additionaldays_list.png' },
    { url: `${base}/sabados`, file: 'sabados_list.png' },
    { url: `${base}/admin`, file: 'admin_dashboard.png' },
    { url: `${base}/admin/users`, file: 'admin_users.png' },
    { url: `${base}/admin/audits`, file: 'admin_audits.png' },
  ];

  for (const { url, file } of screenshots) {
    try {
      await capturePage(page, url, file);
    } catch (err) {
      console.error('Error capturing', url, '-', err.message);
    }
  }

  await browser.close();
})();
