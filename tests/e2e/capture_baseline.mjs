import { chromium } from '@playwright/test';
import fs from 'fs';
import path from 'path';

const BASE_URL = 'http://127.0.0.1:8002';
const ARTIFACT_DIR = path.resolve('artifacts/ui-audit/baseline');
const TESTS_BASELINE_DIR = path.resolve('tests/e2e/screenshots/baseline');

const VIEWPORTS = [
  { name: 'desktop', width: 1440, height: 900 },
  { name: 'laptop', width: 1366, height: 768 },
  { name: 'tablet', width: 768, height: 1024 },
  { name: 'mobile', width: 390, height: 844 },
];

const PAGES = [
  { name: 'dashboard', url: '/dashboard', module: 'dashboard' },
  { name: 'lease_application', url: '/application', module: 'lease_application' },
  { name: 'lease_application_view', url: '/viewapplication?id=36', module: 'lease_application' },
  { name: 'customer_directory', url: '/customers', module: 'customers' },
  { name: 'customer_tracking', url: '/customer-tracking', module: 'customer_tracking' },
  { name: 'mining_plan', url: '/miningplan', module: 'mining' },
  { name: 'mining_project_folder', url: '/projectfolder', module: 'mining' },
  { name: 'mining_process', url: '/process', module: 'mining' },
  { name: 'environmental_clearance', url: '/eviron', module: 'environment' },
  { name: 'ec_certificate', url: '/ec-certificate', module: 'environment' },
  { name: 'ec_compliance', url: '/ec-compliance', module: 'environment' },
  { name: 'dgps_survey', url: '/dgps-survey', module: 'surveys' },
  { name: 'drone_survey', url: '/drone-survey', module: 'surveys' },
  { name: 'ppt_department', url: '/ppt-department', module: 'surveys' },
  { name: 'accounts_quotations', url: '/accounts/quotations', module: 'accounts' },
  { name: 'accounts_receipts', url: '/accounts/receipts', module: 'accounts' },
  { name: 'accounts_ledger', url: '/accounts/ledger', module: 'accounts' },
  { name: 'accounts_reports', url: '/accounts/reports', module: 'accounts' },
  { name: 'accounts_quotations_create', url: '/accounts/quotations/create', module: 'accounts' },
  { name: 'category', url: '/category', module: 'master' },
  { name: 'product', url: '/product', module: 'master' },
  { name: 'productstock', url: '/productstock', module: 'master' },
  { name: 'unit', url: '/unit', module: 'master' },
  { name: 'users', url: '/user', module: 'authentication' },
  { name: 'roles', url: '/roles', module: 'authentication' },
  { name: 'branch', url: '/branch', module: 'authentication' },
  { name: 'notifications', url: '/notifications', module: 'notifications' },
];

async function removePreloaderAndSettle(page) {
  // Turn off / remove preloader overlay and ensure main wrapper is visible
  await page.evaluate(() => {
    const preloader = document.getElementById('preloader');
    if (preloader) preloader.remove();
    const wrapper = document.getElementById('main-wrapper');
    if (wrapper) wrapper.classList.add('show');
  });
  // Small pause for layout & fonts to settle
  await page.waitForTimeout(600);
}

(async () => {
  fs.mkdirSync(ARTIFACT_DIR, { recursive: true });
  fs.mkdirSync(TESTS_BASELINE_DIR, { recursive: true });

  const browser = await chromium.launch({ headless: true });
  const inventory = [];

  console.log(`=== Starting Master Baseline Capture across ${PAGES.length} pages & 4 viewports ===`);

  for (const vp of VIEWPORTS) {
    console.log(`\n--- Viewport: ${vp.name} (${vp.width}x${vp.height}) ---`);
    const context = await browser.newContext({ viewport: { width: vp.width, height: vp.height } });
    const page = await context.newPage();

    // Authenticate
    await page.goto(`${BASE_URL}/login`, { waitUntil: 'domcontentloaded' });
    await page.fill('input[name="email"]', 'admin@gtms.com');
    await page.fill('input[name="password"]', 'admin123');
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }).catch(() => {}),
      page.click('button[type="submit"]')
    ]);

    for (const p of PAGES) {
      const moduleDir = path.join(ARTIFACT_DIR, p.module);
      fs.mkdirSync(moduleDir, { recursive: true });
      const targetFilename = `${p.name}_${vp.name}.png`;
      const artifactPath = path.join(moduleDir, targetFilename);
      const testPath = path.join(TESTS_BASELINE_DIR, targetFilename);

      try {
        const resp = await page.goto(`${BASE_URL}${p.url}`, { waitUntil: 'domcontentloaded', timeout: 15000 });
        const statusCode = resp ? resp.status() : 200;

        await removePreloaderAndSettle(page);

        // Capture screenshot
        await page.screenshot({ path: artifactPath, fullPage: false });
        fs.copyFileSync(artifactPath, testPath);

        const stats = fs.statSync(artifactPath);
        const isPreloaderSuspect = stats.size < 20000; // Suspect if under 20KB

        inventory.push({
          page: p.name,
          module: p.module,
          url: p.url,
          viewport: vp.name,
          dimensions: `${vp.width}x${vp.height}`,
          status: statusCode,
          fileSize: stats.size,
          path: artifactPath,
          isPreloaderSuspect,
          verified: !isPreloaderSuspect && statusCode === 200
        });

        console.log(`[${vp.name}] ${p.name.padEnd(26)} -> ${stats.size.toString().padStart(7)} bytes (HTTP ${statusCode}) ${isPreloaderSuspect ? '⚠️ LOADER SUSPECT!' : '✅ OK'}`);
      } catch (err) {
        console.error(`[${vp.name}] ${p.name} ERROR:`, err.message);
        inventory.push({
          page: p.name,
          module: p.module,
          url: p.url,
          viewport: vp.name,
          dimensions: `${vp.width}x${vp.height}`,
          status: 'ERROR',
          error: err.message,
          verified: false
        });
      }
    }

    await context.close();
  }

  // Write inventory JSON
  const invPath = path.join(ARTIFACT_DIR, 'baseline_inventory.json');
  fs.writeFileSync(invPath, JSON.stringify(inventory, null, 2));
  console.log(`\nInventory written to ${invPath}`);

  await browser.close();
  console.log('=== Baseline Capture Complete ===');
})();
