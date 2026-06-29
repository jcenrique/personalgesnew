import { chromium } from 'playwright-chromium';
import fs from 'node:fs/promises';
import path from 'node:path';

const BASE_URL = process.env.CAPTURE_BASE_URL ?? 'http://127.0.0.1:8000';
const USER_EMAIL = process.env.CAPTURE_USER_EMAIL ?? '';
const USER_PASSWORD = process.env.CAPTURE_USER_PASSWORD ?? '';
const ADMIN_EMAIL = process.env.CAPTURE_ADMIN_EMAIL ?? USER_EMAIL;
const ADMIN_PASSWORD = process.env.CAPTURE_ADMIN_PASSWORD ?? USER_PASSWORD;

const ROOT = process.cwd();
const USER_DIR = path.join(ROOT, 'docs/images/usuario');
const ADMIN_DIR = path.join(ROOT, 'docs/images/admin');

const userShots = [
  ['/login', '01-login-usuario.png'],
  ['/', '02-dashboard-usuario.png'],
  ['/', '03-calendario-personal.png'],
  ['/', '04-detalle-evento-personal.png'],
  ['/additionaldays', '05-nueva-solicitud.png'],
  ['/additionaldays', '06-confirmacion-solicitud.png'],
  ['/sabados', '07-estado-solicitudes.png'],
  ['/', '08-notificaciones-usuario.png'],
  ['/', '09-perfil-usuario.png'],
];

const adminShots = [
  ['/login', '01-login-admin.png'],
  ['/admin', '02-dashboard-admin.png'],
  ['/admin/sabados', '03-solicitudes-listado.png'],
  ['/admin/sabados', '04-aprobar-solicitud.png'],
  ['/admin/sabados', '05-rechazar-solicitud.png'],
  ['/admin/users', '06-usuarios-listado.png'],
  ['/admin/users', '07-usuario-edicion.png'],
  ['/admin/training-actions', '08-training-actions.png'],
  ['/admin', '09-calendario-global.png'],
  ['/admin', '10-detalle-evento.png'],
  ['/admin/audits', '11-auditoria.png'],
  ['/admin', '12-notificaciones.png'],
];

async function assertBaseUrlReachable() {
  try {
    const response = await fetch(`${BASE_URL}/login`, { redirect: 'manual' });

    if (!response.ok && response.status !== 302) {
      throw new Error(`unexpected status ${response.status}`);
    }
  } catch (error) {
    const message = error instanceof Error ? error.message : String(error);

    throw new Error(
      [
        `No se puede acceder a ${BASE_URL}.`,
        'Levanta la app antes de ejecutar capturas, por ejemplo:',
        'php artisan serve --host=127.0.0.1 --port=8000',
        'Si usas otro puerto, exporta CAPTURE_BASE_URL con ese valor.',
        `Detalle técnico: ${message}`,
      ].join('\n')
    );
  }
}

async function ensureDirs() {
  await fs.mkdir(USER_DIR, { recursive: true });
  await fs.mkdir(ADMIN_DIR, { recursive: true });
}

async function capture(page, urlPath, filePath) {
  const url = `${BASE_URL}${urlPath}`;
  await page.goto(url, { waitUntil: 'domcontentloaded' });
  await page.waitForTimeout(600);
  await page.screenshot({ path: filePath, fullPage: true });
  console.log(`saved: ${filePath}`);
}

async function maybeOpenNotifications(page) {
  const button = page.getByRole('button', { name: /Abrir notificaciones/i });
  if (await button.count()) {
    await button.first().click({ force: true });
    await page.waitForTimeout(500);
  }
}

async function maybeOpenUserMenu(page) {
  const button = page.getByRole('button', { name: /Menú del Usuario/i });
  if (await button.count()) {
    await button.first().click({ force: true });
    await page.waitForTimeout(500);
  }
}

async function login(page, loginPath, email, password) {
  if (!email || !password) {
    console.warn(`skip login ${loginPath}: missing credentials`);
    return false;
  }

  await page.goto(`${BASE_URL}${loginPath}`, { waitUntil: 'domcontentloaded' });
  await page.fill('input[type="email"]', email);
  await page.fill('input[type="password"]', password);
  await page.getByRole('button', { name: /Entrar/i }).click();
  await page.waitForLoadState('domcontentloaded');
  await page.waitForTimeout(900);

  return true;
}

async function captureUserSet(browser) {
  const context = await browser.newContext({ viewport: { width: 1440, height: 900 } });
  const page = await context.newPage();

  await capture(page, '/login', path.join(USER_DIR, '01-login-usuario.png'));

  const loggedIn = await login(page, '/login', USER_EMAIL, USER_PASSWORD);
  if (!loggedIn) {
    await context.close();
    return;
  }

  for (const [route, filename] of userShots.slice(1)) {
    await page.goto(`${BASE_URL}${route}`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(600);

    if (filename === '04-detalle-evento-personal.png') {
      const event = page.locator('.fc-event, .ec-event, [data-event-id]').first();
      if (await event.count()) {
        await event.click({ force: true });
        await page.waitForTimeout(600);
      }
    }

    if (filename === '08-notificaciones-usuario.png') {
      await maybeOpenNotifications(page);
    }

    if (filename === '09-perfil-usuario.png') {
      await maybeOpenUserMenu(page);
    }

    await page.screenshot({ path: path.join(USER_DIR, filename), fullPage: true });
    console.log(`saved: ${path.join(USER_DIR, filename)}`);
  }

  await context.close();
}

async function captureAdminSet(browser) {
  const context = await browser.newContext({ viewport: { width: 1440, height: 900 } });
  const page = await context.newPage();

  await capture(page, '/login', path.join(ADMIN_DIR, '01-login-admin.png'));

  const loggedIn = await login(page, '/admin/login', ADMIN_EMAIL, ADMIN_PASSWORD);
  if (!loggedIn) {
    await context.close();
    return;
  }

  for (const [route, filename] of adminShots.slice(1)) {
    await page.goto(`${BASE_URL}${route}`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(700);

    if (filename === '04-aprobar-solicitud.png') {
      const approveButton = page.locator('[title*="Aprobar"], [aria-label*="Aprobar"], button:has-text("Aprobar")').first();
      if (await approveButton.count()) {
        await approveButton.click({ force: true });
        await page.waitForTimeout(600);
      }
    }

    if (filename === '05-rechazar-solicitud.png') {
      const rejectButton = page.locator('[title*="Rechazar"], [aria-label*="Rechazar"], button:has-text("Rechazar")').first();
      if (await rejectButton.count()) {
        await rejectButton.click({ force: true });
        await page.waitForTimeout(600);
      }
    }

    if (filename === '10-detalle-evento.png') {
      const event = page.locator('.fc-event, .ec-event, [data-event-id]').first();
      if (await event.count()) {
        await event.click({ force: true });
        await page.waitForTimeout(700);
      }
    }

    if (filename === '12-notificaciones.png') {
      await maybeOpenNotifications(page);
    }

    await page.screenshot({ path: path.join(ADMIN_DIR, filename), fullPage: true });
    console.log(`saved: ${path.join(ADMIN_DIR, filename)}`);
  }

  await context.close();
}

async function main() {
  await ensureDirs();
  await assertBaseUrlReachable();

  const browser = await chromium.launch({ headless: true });

  try {
    await captureUserSet(browser);
    await captureAdminSet(browser);
  } finally {
    await browser.close();
  }

  console.log('capture process finished');
}

main().catch((error) => {
  console.error(error);
  process.exitCode = 1;
});
