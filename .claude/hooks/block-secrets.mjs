#!/usr/bin/env node
import { readFileSync } from 'node:fs';
import path from 'node:path';

let payload;
try {
  payload = JSON.parse(readFileSync(0, 'utf8'));
} catch {
  process.exit(0);
}

const filePath = payload?.tool_input?.file_path;
if (!filePath || typeof filePath !== 'string') process.exit(0);

const basename = path.basename(filePath).toLowerCase();
const fullPath = filePath.toLowerCase();

const sensitivePatterns = [
  /^\.env(\.|$)/,
  /(^|[/\\])credentials/i,
  /\.pem$/i,
  /\.key$/i,
  /\.p12$/i,
  /\.pfx$/i,
  /(^|[/\\])id_rsa/i,
  /^secrets?\.(json|ya?ml|toml)$/i,
  /(^|[/\\])\.aws[/\\]/i,
];

const matched = sensitivePatterns.find((re) => re.test(basename) || re.test(fullPath));

if (matched) {
  process.stderr.write(
    `Blocked: '${filePath}' matches a sensitive file pattern (${matched}). ` +
      `Edit it manually if intended.\n`,
  );
  process.exit(2);
}
process.exit(0);
