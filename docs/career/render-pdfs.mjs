import path from "node:path";
import { fileURLToPath, pathToFileURL } from "node:url";
import { chromium } from "playwright";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const jobs = [
  { input: "cv-fr.html", output: path.join(__dirname, "output", "ismael-rodmacq-cv-fr.pdf") },
  { input: "cv-en.html", output: path.join(__dirname, "output", "ismael-rodmacq-cv-en.pdf") },
];

const browser = await chromium.launch();
try {
  for (const job of jobs) {
    const page = await browser.newPage();
    await page.goto(pathToFileURL(path.join(__dirname, job.input)).href, { waitUntil: "networkidle" });
    await page.pdf({
      path: job.output,
      format: "A4",
      printBackground: true,
      margin: { top: "0mm", right: "0mm", bottom: "0mm", left: "0mm" },
      preferCSSPageSize: true,
    });
    await page.close();
  }
} finally {
  await browser.close();
}
