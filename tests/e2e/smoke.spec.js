import { test, expect } from '@playwright/test';

test.describe('Playwright Setup Verification', () => {
  test('playwright environment test', async ({ page }) => {
    // Verify Playwright browser page can navigate and evaluate basic javascript
    await page.setContent('<h1>GTMS Playwright Initialized</h1>');
    await expect(page.locator('h1')).toHaveText('GTMS Playwright Initialized');
  });
});
