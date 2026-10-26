import { chromium } from '@playwright/test';

const BASE_URL = 'http://127.0.0.1:8002';

const ROUTES_TO_TEST = [
  { name: 'Dashboard', path: '/dashboard' },
  { name: 'Notifications', path: '/notifications' },
  { name: 'Customers Directory', path: '/customers' },
  { name: 'Customer Tracking', path: '/customer-tracking' },
  { name: 'Roles Management', path: '/roles' },
  { name: 'User Management', path: '/user' },
  { name: 'Branch Management', path: '/branch' },
  { name: 'Category Master', path: '/category' },
  { name: 'Unit Master', path: '/unit' },
  { name: 'Product Master', path: '/product' },
  { name: 'Product Stock Master', path: '/productstock' },
  { name: 'Lease Application (Ref)', path: '/application' },
  { name: 'Lease Dossier (Ref)', path: '/viewapplication?id=36' },
  { name: 'Mining Plans', path: '/miningplan' },
  { name: 'Mining Project Folders', path: '/projectfolder' },
  { name: 'Mining Process Flow', path: '/process' },
  { name: 'Environment Clearance', path: '/eviron' },
  { name: 'EC Certificate', path: '/ec-certificate' },
  { name: 'EC Compliance', path: '/ec-compliance' },
  { name: 'DGPS Survey', path: '/dgps-survey' },
  { name: 'Drone Survey', path: '/drone-survey' },
  { name: 'PPT Department', path: '/ppt-department' },
  { name: 'Quotations Index', path: '/accounts/quotations' },
  { name: 'Create Quotation', path: '/accounts/quotations/create' },
  { name: 'Money Receipts', path: '/accounts/receipts' },
  { name: 'Customer Ledger', path: '/accounts/ledger' },
  { name: 'Financial Reports', path: '/accounts/reports' },
];

(async () => {
  console.log(`=== Verifying all ${ROUTES_TO_TEST.length} routes from routes/web.php ===\n`);
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage();

  // Log in
  await page.goto(`${BASE_URL}/login`, { waitUntil: 'domcontentloaded' });
  await page.fill('input[name="email"]', 'admin@gtms.com');
  await page.fill('input[name="password"]', 'admin123');
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'domcontentloaded' }).catch(() => {}),
    page.click('button[type="submit"]')
  ]);

  let passCount = 0;
  let failCount = 0;

  for (const r of ROUTES_TO_TEST) {
    try {
      const resp = await page.goto(`${BASE_URL}${r.path}`, { waitUntil: 'domcontentloaded', timeout: 15000 });
      const status = resp ? resp.status() : 200;

      // Remove preloader
      await page.evaluate(() => {
        const p = document.getElementById('preloader');
        if (p) p.remove();
        const mw = document.getElementById('main-wrapper');
        if (mw) mw.classList.add('show');
      });

      // Check title or heading
      const title = await page.title();

      if (status === 200) {
        passCount++;
        console.log(`✅ [HTTP ${status}] ${r.name.padEnd(28)} -> ${title}`);
      } else {
        failCount++;
        console.error(`❌ [HTTP ${status}] ${r.name.padEnd(28)}`);
      }
    } catch (err) {
      failCount++;
      console.error(`❌ [ERROR]   ${r.name.padEnd(28)} -> ${err.message}`);
    }
  }

  console.log(`\n=== Verification Results: ${passCount} Passed, ${failCount} Failed ===`);
  await browser.close();
})();
