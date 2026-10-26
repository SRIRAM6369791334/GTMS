import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';

(async () => {
    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext({ viewport: { width: 1440, height: 900 } });
    const page = await context.newPage();

    // Login
    await page.goto('http://127.0.0.1:8002/login', { waitUntil: 'domcontentloaded' });
    await page.fill('input[name="email"], input[type="email"]', 'admin@gtms.com');
    await page.fill('input[name="password"], input[type="password"]', 'password');
    await Promise.all([
        page.waitForNavigation({ waitUntil: 'domcontentloaded' }).catch(() => {}),
        page.click('button[type="submit"]')
    ]);

    // Test customer 360
    const res = await page.goto('http://127.0.0.1:8002/customers/sri-bala-traders', { waitUntil: 'domcontentloaded' });
    console.log(`Status: ${res.status()}`);

    // Remove preloader
    await page.evaluate(() => {
        const p = document.getElementById('preloader');
        if (p) p.remove();
        const mw = document.getElementById('main-wrapper');
        if (mw) mw.classList.add('show');
    });

    const outDir = 'tests/e2e/screenshots/after';
    if (!fs.existsSync(outDir)) fs.mkdirSync(outDir, { recursive: true });

    await page.screenshot({ path: path.join(outDir, 'show_customer_360_desktop.png'), fullPage: false });
    console.log('Screenshot saved: show_customer_360_desktop.png');

    // Mobile
    await page.setViewportSize({ width: 390, height: 844 });
    await page.screenshot({ path: path.join(outDir, 'show_customer_360_mobile.png'), fullPage: false });
    console.log('Screenshot saved: show_customer_360_mobile.png');

    await browser.close();
})();
