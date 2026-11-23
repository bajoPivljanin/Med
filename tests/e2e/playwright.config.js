// Konfiguracija testova toka kupovine. Prije prvog pokretanja: php tools/e2e-setup.php (vidi README.md).
const fs = require('fs');
const path = require('path');

const envFile = path.join(__dirname, '.env');
if (fs.existsSync(envFile)) {
  for (const line of fs.readFileSync(envFile, 'utf8').split(/\r?\n/)) {
    const m = line.match(/^([A-Z_]+)=(.*)$/);
    if (m && !(m[1] in process.env)) process.env[m[1]] = m[2];
  }
}

module.exports = {
  testDir: __dirname,
  testMatch: '*.spec.js',
  timeout: 60_000,
  workers: 1,
  reporter: [['list']],
  use: {
    baseURL: process.env.OM_BASE_URL || 'http://organski-med.local',
    channel: 'chrome', // koristi instalirani Chrome, bez preuzimanja pregledača
    reducedMotion: 'reduce', // bez zavjese i animacija pri prelasku
    locale: 'sr-Latn-ME',
    screenshot: 'only-on-failure',
    trace: 'retain-on-failure',
  },
  outputDir: path.join(__dirname, 'test-results'),
};
