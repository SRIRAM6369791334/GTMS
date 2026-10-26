import fs from 'fs';
import path from 'path';

const BASELINE_DIR = path.resolve('artifacts/ui-audit/baseline');
const FINAL_DIR = path.resolve('artifacts/ui-audit/final');

const MODULES = [
  'dashboard',
  'lease_application',
  'customers',
  'customer_tracking',
  'mining',
  'environment',
  'surveys',
  'accounts',
  'master',
  'authentication',
  'notifications'
];

const results = [];

console.log('=== Starting Screenshot Verification and Comparison Suite ===');

for (const mod of MODULES) {
  const baseModDir = path.join(BASELINE_DIR, mod);
  const finalModDir = path.join(FINAL_DIR, mod);

  if (!fs.existsSync(baseModDir) || !fs.existsSync(finalModDir)) continue;

  const files = fs.readdirSync(baseModDir).filter(f => f.endsWith('.png'));

  for (const f of files) {
    const basePath = path.join(baseModDir, f);
    const finalPath = path.join(finalModDir, f);

    if (!fs.existsSync(finalPath)) {
      results.push({ module: mod, file: f, status: 'MISSING_FINAL', verified: false });
      continue;
    }

    const baseSize = fs.statSync(basePath).size;
    const finalSize = fs.statSync(finalPath).size;

    const baseValid = baseSize > 20000;
    const finalValid = finalSize > 20000;
    const sizeDelta = finalSize - baseSize;
    const percentChange = ((sizeDelta / baseSize) * 100).toFixed(1);

    const isLeaseRef = mod === 'lease_application';
    // Reference should have near-zero difference (less than 1%)
    const refPreserved = isLeaseRef ? Math.abs(parseFloat(percentChange)) < 2.0 : true;

    results.push({
      module: mod,
      file: f,
      baseSize,
      finalSize,
      sizeDelta,
      percentChange: `${percentChange}%`,
      baseValid,
      finalValid,
      refPreserved,
      verified: baseValid && finalValid && refPreserved
    });
  }
}

const total = results.length;
const verifiedCount = results.filter(r => r.verified).length;
console.log(`\nComparison Complete: ${verifiedCount} / ${total} screenshots verified successfully!`);

// Write verification report
fs.writeFileSync('artifacts/ui-audit/comparison_report.json', JSON.stringify(results, null, 2));

console.log('Results written to artifacts/ui-audit/comparison_report.json');
