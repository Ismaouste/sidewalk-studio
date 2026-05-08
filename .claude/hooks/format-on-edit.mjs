#!/usr/bin/env node
import { execFileSync } from 'node:child_process';
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

const ext = path.extname(filePath).toLowerCase();
const eligible = new Set(['.vue', '.ts', '.tsx', '.js', '.jsx', '.mjs', '.css', '.scss', '.json', '.md', '.yaml', '.yml']);
if (!eligible.has(ext)) process.exit(0);

const projectDir = process.env.CLAUDE_PROJECT_DIR || process.cwd();
const absolute = path.resolve(filePath);
if (!absolute.startsWith(path.resolve(projectDir))) process.exit(0);

if (absolute.includes(`${path.sep}node_modules${path.sep}`) || absolute.includes(`${path.sep}vendor${path.sep}`)) {
  process.exit(0);
}

try {
  execFileSync('npx', ['--no-install', 'prettier', '--write', absolute], {
    cwd: projectDir,
    stdio: 'pipe',
    timeout: 25000,
    shell: process.platform === 'win32',
  });
} catch {
  // fail silently — never block an edit because of a format failure
}
process.exit(0);
