const fs = require('fs');
const path = require('path');
const { chromium } = require('playwright-chromium');
const { marked } = require('marked');

async function mdToPdf(mdPath, outPath, options = {}) {
  const md = fs.readFileSync(mdPath, 'utf8');
  // try to use an existing screenshot as logo for the cover
  let coverImageData = null;
  const candidate = path.join(__dirname, 'screenshots', 'dashboard.png');
  if (fs.existsSync(candidate)) {
    const buf = fs.readFileSync(candidate);
    coverImageData = `data:image/png;base64,${buf.toString('base64')}`;
  }

  const title = options.title || path.basename(mdPath, '.md').replace(/_/g, ' ');
  const now = new Date().toLocaleDateString('es-ES');

  const html = `<!doctype html>
  <html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
      @page { size: A4; margin: 20mm }
      body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; color:#111; line-height:1.4; }
      .cover { display:flex; flex-direction:column; align-items:center; justify-content:center; height:100vh; text-align:center }
      .cover h1 { font-size:38px; margin:0 0 8px; color:#0b5cff }
      .cover p { font-size:16px; margin:4px 0; color:#333 }
      .cover img { max-width:260px; margin:18px 0 }
      .content { padding: 8mm 6mm }
      h1,h2,h3 { color:#0b5cff }
      img { max-width:100%; height:auto }
      pre { background:#f6f8fa; padding:10px; overflow:auto }
      .page-break { page-break-after: always }
    </style>
  </head>
  <body>
    <section class="cover">
      <h1>${title}</h1>
      <p>Manual de la aplicación — SofTren</p>
      ${coverImageData ? `<img src="${coverImageData}" alt="Logo">` : ''}
      <p>Fecha: ${now}</p>
    </section>
    <div class="page-break"></div>
    <main class="content">
      ${marked(md)}
    </main>
  </body>
  </html>`;

  const browser = await chromium.launch();
  const page = await browser.newPage();
  await page.setContent(html, { waitUntil: 'networkidle' });
  await page.emulateMedia({ media: 'print' });
  await page.pdf({ path: outPath, format: 'A4', printBackground: true, displayHeaderFooter: true, headerTemplate: '<div></div>', footerTemplate: `<div style="font-size:10px;width:100%;text-align:center;color:#666"><span class="pageNumber"></span> / <span class="totalPages"></span></div>`, margin: { top: '18mm', bottom: '18mm', left: '12mm', right: '12mm' } });
  await browser.close();
}

(async () => {
  const docs = [
    { md: path.join(__dirname, 'manual_usuario.md'), out: path.join(__dirname, 'manual_usuario.pdf') },
    { md: path.join(__dirname, 'manual_administrador.md'), out: path.join(__dirname, 'manual_administrador.pdf') },
    { md: path.join(__dirname, 'manual_instalacion.md'), out: path.join(__dirname, 'manual_instalacion.pdf') },
  ];

  for (const d of docs) {
    try {
      console.log('Generating PDF from', d.md);
      await mdToPdf(d.md, d.out);
      console.log('Saved', d.out);
    } catch (err) {
      console.error('Error generating PDF for', d.md, err);
    }
  }
})();
