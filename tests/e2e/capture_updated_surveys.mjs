import { chromium } from '@playwright/test';
import fs from 'fs';
import path from 'path';

const BASE_URL = 'http://127.0.0.1:8002';
const ARTIFACT_DIR = path.resolve('artifacts/ui-audit/final');
fs.mkdirSync(ARTIFACT_DIR, { recursive: true });

const targetRoutes = [
  { name: 'dgps_survey', path: '/dgps-survey' },
  { name: 'drone_survey', path: '/drone-survey' },
  { name: 'ppt_department', path: '/ppt-department' },
  { name: 'ec_compliance', path: '/ec-compliance' }
];

const viewports = [
  { name: 'desktop', width: 1440, height: 900 },
  { name: 'laptop', width: 1366, height: 768 },
  { name: 'tablet', width: 768, height: 1024 },
  { name: 'mobile', width: 390, height: 844 }
];

async function run() {
  console.log('=== Capturing Updated Survey Registers (Table Action Squircle Polish) ===');
  const browser = await chromium.launch();
  const context = await browser.newContext();
  const page = await context.newPage();

  // Login as admin
  await page.goto(`${BASE_URL}/login`, { waitUntil: 'domcontentloaded' });
  await page.evaluate(() => {
    const preloader = document.getElementById('preloader');
    if (preloader) preloader.remove();
    const wrapper = document.getElementById('main-wrapper');
    if (wrapper) wrapper.classList.add('show');
  });

  await page.fill('input[name="email"], input[type="email"]', 'admin@gtms.com');
  await page.fill('input[name="password"], input[type="password"]', 'admin123');
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'domcontentloaded' }).catch(() => {}),
    page.click('button[type="submit"]')
  ]);
  console.log('✅ Authenticated successfully as Admin');

  for (const route of targetRoutes) {
    for (const vp of viewports) {
      await page.setViewportSize({ width: vp.width, height: vp.height });
      const response = await page.goto(`${BASE_URL}${route.path}`, { waitUntil: 'domcontentloaded', timeout: 25000 });
      
      // Detach preloader
      await page.evaluate(() => {
        const preloader = document.getElementById('preloader');
        if (preloader) preloader.remove();
        const wrapper = document.getElementById('main-wrapper');
        if (wrapper) wrapper.classList.add('show');
      });

      await page.waitForTimeout(600);

      const filePath = path.join(ARTIFACT_DIR, `${route.name}_${vp.name}.png`);
      await page.screenshot({ path: filePath, fullPage: false });
      const stats = fs.statSync(filePath);
      console.log(`📸 [${vp.name.padEnd(7)}] ${route.name.padEnd(16)} -> ${filePath} (${(stats.size / 1024).toFixed(1)} KB)`);
    }
  }

  await browser.close();
  console.log('=== All updated survey screenshots captured successfully! ===');
}

run().catch(err => {
  console.error('Fatal error:', err);
  process.exit(1);
});
