import { chromium } from '@playwright/test';
import fs from 'fs';
import path from 'path';

const BASE_URL = 'http://127.0.0.1:8002';
const ARTIFACT_DIR = path.resolve('artifacts/ui-audit/detail_and_wizards');
fs.mkdirSync(ARTIFACT_DIR, { recursive: true });

const pagesToTest = [
  // Show / Dossier Views
  { name: 'show_dgps_survey', path: '/dgps-survey/178' },
  { name: 'show_drone_survey', path: '/drone-survey/199' },
  { name: 'show_ppt_department', path: '/ppt-department/32' },
  { name: 'show_ec_certificate', path: '/ec-certificate/109' },
  { name: 'show_ec_compliance', path: '/ec-compliance/30' },
  { name: 'show_eviron_project', path: '/eviron/150' },
  { name: 'show_customer_360', path: '/customers/sri-bala-traders' },
  { name: 'show_quotation', path: '/accounts/quotations/45' },
  { name: 'show_receipt_voucher', path: '/accounts/receipts/247' },
  { name: 'show_customer_ledger', path: '/accounts/ledger/sri-bala-traders' },

  // Wizard / Multi-Step Forms
  { name: 'wizard_dgps_step1', path: '/dgps-survey/step/1' },
  { name: 'wizard_drone_step1', path: '/drone-survey/step/1' },
  { name: 'wizard_ppt_step1', path: '/ppt-department/step/1' },
  { name: 'wizard_ec_cert_step1', path: '/ec-certificate/step/1' },
  { name: 'wizard_ec_comp_step1', path: '/ec-compliance/step/1' },
  { name: 'wizard_eviron_create', path: '/eviron/create' },
  { name: 'wizard_enviro_b2_step1', path: '/environment-b2/step/1' },
  { name: 'wizard_mining_newapp', path: '/newapplication' },
  { name: 'form_create_quotation', path: '/accounts/quotations/create' },
  { name: 'form_collect_payment', path: '/accounts/payments/create' },
  { name: 'subcat1_tor', path: '/environstage1' },
  { name: 'subcat2_eia', path: '/environstage2' }
];

const viewports = [
  { name: 'desktop', width: 1440, height: 900 },
  { name: 'mobile', width: 390, height: 844 }
];

async function run() {
  console.log('=== Capturing Detail, Show & Wizard Pages with Preloader Detachment ===');
  const browser = await chromium.launch();
  const context = await browser.newContext();
  const page = await context.newPage();

  // Login
  await page.goto(`${BASE_URL}/login`, { waitUntil: 'domcontentloaded' });
  await page.evaluate(() => {
    const p = document.getElementById('preloader');
    if (p) p.remove();
    const w = document.getElementById('main-wrapper');
    if (w) w.classList.add('show');
  });

  await page.fill('input[name="email"]', 'admin@gtms.com');
  await page.fill('input[name="password"]', 'admin123');
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'domcontentloaded' }).catch(() => {}),
    page.click('button[type="submit"]')
  ]);
  console.log('✅ Logged in successfully as Admin\n');

  let passed = 0;
  let failed = 0;

  for (const item of pagesToTest) {
    for (const vp of viewports) {
      await page.setViewportSize({ width: vp.width, height: vp.height });
      try {
        const res = await page.goto(`${BASE_URL}${item.path}`, { waitUntil: 'domcontentloaded', timeout: 25000 });
        
        // Detach preloader
        await page.evaluate(() => {
          const preloader = document.getElementById('preloader');
          if (preloader) preloader.remove();
          const wrapper = document.getElementById('main-wrapper');
          if (wrapper) wrapper.classList.add('show');
        });

        await page.waitForTimeout(500);

        const status = res ? res.status() : 'Unknown';
        const title = await page.title();
        const filePath = path.join(ARTIFACT_DIR, `${item.name}_${vp.name}.png`);
        await page.screenshot({ path: filePath, fullPage: false });
        const stats = fs.statSync(filePath);

        if (status === 200) {
          console.log(`✅ [${vp.name.padEnd(7)}] ${item.name.padEnd(24)} -> HTTP 200 (${(stats.size / 1024).toFixed(1)} KB) | "${title.slice(0, 35)}..."`);
          passed++;
        } else {
          console.log(`⚠️ [${vp.name.padEnd(7)}] ${item.name.padEnd(24)} -> HTTP ${status}`);
          failed++;
        }
      } catch (err) {
        console.error(`❌ [${vp.name.padEnd(7)}] ${item.name.padEnd(24)} -> Error: ${err.message}`);
        failed++;
      }
    }
  }

  await browser.close();
  console.log(`\n=== Verification Summary: ${passed} Passed, ${failed} Failed ===`);
}

run().catch(err => {
  console.error('Fatal error:', err);
  process.exit(1);
});
