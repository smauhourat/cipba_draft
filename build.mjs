/**
 * Genera dist/ : la version estatica desplegable del prototipo que vive en project/.
 *
 * Que hace, en una linea: compila el JSX que hoy se transpila en el navegador,
 * sirve React localmente, y copia solo los archivos que el sitio referencia.
 *
 *   node build.mjs          (o: npm run build)
 *
 * project/ NO se modifica nunca. dist/ se borra y se regenera entero.
 */

import * as esbuild from 'esbuild';
import fs from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = path.dirname(fileURLToPath(import.meta.url));
const SRC = path.join(ROOT, 'project');
const OUT = path.join(ROOT, 'dist');

const PAGES = [
  'index.html',
  'institucional.html',
  'subcomisiones.html',
  'contacto.html',
  'pago-matricula.html',
  'honorarios.html',
  'normativa.html',
  'novedades.html',
  'novedad.html',
  'inscripcion.html',
  'rehabilitacion.html',
  'solicitud-credenciales.html',
  'baja.html',
];

// JSX compartido: se compila una sola vez y lo cargan varias paginas como script suelto,
// para no duplicar 36 K en cada pagina y aprovechar el cache del navegador entre paginas.
const SHARED = ['tramites-shell.jsx', 'novedades-data.jsx'];

const VENDOR = ['react.production.min.js', 'react-dom.production.min.js'];

// Directorios de assets que se copian por demanda (solo lo referenciado).
const ASSET_DIRS = ['uploads', 'assets/images'];

// docs/ se copia entero: los 4 PDF estan linkeados desde index.html y contacto.html.
const COPY_WHOLE = ['docs'];

const warnings = [];

// --- transformacion de JSX -------------------------------------------------

/**
 * Usamos transform(), no build(), y nunca bundle: no hay un solo import/export
 * en el codigo fuente, asi que bundlear no tendria nada que hacer y solo podria
 * meter helpers o ambito de modulo donde hoy no los hay.
 *
 * El envoltorio IIFE NO es opcional. Hoy Babel standalone evalua cada bloque
 * text/babel en su propio ambito, y el codigo depende de eso: tramites-shell.jsx
 * declara `const { useState, useEffect } = React` y `const Icon` a nivel
 * superior, y el bloque inline de honorarios/normativa/novedades/novedad declara
 * exactamente los mismos nombres. Como scripts clasicos sueltos comparten un
 * unico ambito lexico global, eso seria "SyntaxError: Identifier 'useState' has
 * already been declared" y la pagina no renderiza nada.
 *
 * El IIFE reproduce el aislamiento de hoy. La comunicacion entre archivos sigue
 * funcionando porque es via window: los compartidos publican con
 * Object.assign(window, {...}) y cada pagina lee `const { TopBar, ... } = window`.
 * Las referencias sueltas (novedades-data.jsx usa `Icon` sin declararlo) siguen
 * resolviendo por la cadena de ambitos hasta window.Icon.
 */
async function compileJsx(source, label) {
  const result = await esbuild.transform(source, {
    loader: 'jsx',
    jsx: 'transform', // emite React.createElement, no el runtime automatico
    jsxFactory: 'React.createElement',
    jsxFragment: 'React.Fragment',
    target: 'es2018',
    minify: true,
    legalComments: 'none',
    sourcefile: label,
  });
  for (const w of result.warnings) {
    warnings.push(`${label}: ${w.text}`);
  }

  const code = `(function(){${result.code}})();\n`;

  // Falla en build y no en produccion si algo salio mal en la transformacion.
  try {
    new Function(code);
  } catch (err) {
    throw new Error(`${label}: el JS generado no parsea (${err.message})`);
  }
  return code;
}

/**
 * Los <image-slot> son del editor de diseno: leen .image-slots.state.json, cuyas
 * claves ya no coinciden con ningun slot vivo, asi que hoy renderizan vacio.
 * Se quitan junto con image-slot.js. El recuadro gris lo sigue dando el div
 * contenedor, de modo que el layout no se mueve.
 */
function stripImageSlots(jsx) {
  return jsx.replace(/\s*<image-slot\b[^>]*><\/image-slot>/g, '');
}

/**
 * event-1.png son 3,93 MB a 4501x5626 px. event-1.jpg es la misma imagen a
 * 800x1000, igual que el resto de los eventos, y pesa 104 K.
 */
function useSmallEventImage(jsx) {
  return jsx.replace(/'event-1\.png'/g, "'event-1.jpg'");
}

const prepare = (jsx) => useSmallEventImage(stripImageSlots(jsx));

// --- reescritura del HTML --------------------------------------------------

const RE_UNPKG = /^[ \t]*<script src="https:\/\/unpkg\.com\/[^"]*"[^>]*><\/script>[ \t]*\r?\n/gm;
const RE_IMAGE_SLOT_TAG = /^[ \t]*<script src="image-slot\.js"><\/script>[ \t]*\r?\n/gm;
const RE_SHARED_TAG = /<script type="text\/babel" src="([\w.-]+)\.jsx"><\/script>/g;
const INLINE_OPEN = '<script type="text/babel">';

const VENDOR_TAGS = VENDOR.map((f) => `<script src="vendor/${f}"></script>`).join('\n');

function splitInlineBlock(html, page) {
  const open = html.indexOf(INLINE_OPEN);
  if (open === -1) throw new Error(`${page}: no se encontro el bloque ${INLINE_OPEN}`);
  if (html.indexOf(INLINE_OPEN, open + 1) !== -1) {
    throw new Error(`${page}: hay mas de un bloque inline text/babel; revisar a mano`);
  }
  const close = html.lastIndexOf('</script>');
  if (close < open) throw new Error(`${page}: no se encontro el cierre del bloque inline`);
  return {
    before: html.slice(0, open),
    jsx: html.slice(open + INLINE_OPEN.length, close),
    after: html.slice(close + '</script>'.length),
  };
}

async function buildPage(page) {
  const html = await fs.readFile(path.join(SRC, page), 'utf8');
  const { before, jsx, after } = splitInlineBlock(html, page);

  const name = page.replace(/\.html$/, '');
  await write(path.join(OUT, 'js', `${name}.js`), await compileJsx(prepare(jsx), page));

  // Las tres etiquetas de unpkg estan juntas; la primera se reemplaza por los
  // dos scripts locales y las otras dos desaparecen. integrity/crossorigin se
  // van con ellas: dejarlos apuntando a un archivo local lo haria bloquear.
  let out = before;
  let first = true;
  out = out.replace(RE_UNPKG, () => (first ? ((first = false), VENDOR_TAGS + '\n') : ''));
  if (first) throw new Error(`${page}: no se encontraron los scripts de unpkg`);

  out = out.replace(RE_IMAGE_SLOT_TAG, '');
  out = out.replace(RE_SHARED_TAG, (_m, base) => `<script src="js/${base}.js"></script>`);
  out += `<script src="js/${name}.js"></script>${after}`;

  await write(path.join(OUT, page), out);
}

// --- copia de archivos -----------------------------------------------------

async function write(file, content) {
  await fs.mkdir(path.dirname(file), { recursive: true });
  await fs.writeFile(file, content);
}

async function copy(from, to) {
  await fs.mkdir(path.dirname(to), { recursive: true });
  await fs.copyFile(from, to);
}

async function walk(dir) {
  const out = [];
  for (const e of await fs.readdir(dir, { withFileTypes: true })) {
    const full = path.join(dir, e.name);
    if (e.isDirectory()) out.push(...(await walk(full)));
    else out.push(full);
  }
  return out;
}

async function dirSize(dir) {
  const files = await walk(dir);
  const sizes = await Promise.all(files.map(async (f) => (await fs.stat(f)).size));
  return sizes.reduce((a, b) => a + b, 0);
}

const kb = (bytes) => `${(bytes / 1024).toFixed(0)} K`;
const mb = (bytes) => `${(bytes / 1024 / 1024).toFixed(2)} MB`;

/**
 * Poda de assets. No alcanza con buscar "uploads/..." en el HTML: index.html
 * arma las rutas de eventos y noticias con `assets/images/${e.img}`, y en el
 * array solo figura el nombre del archivo. Por eso se busca por basename.
 */
async function copyReferencedAssets() {
  const built = await walk(OUT);
  const haystack = (
    await Promise.all(
      built
        .filter((f) => /\.(html|js)$/.test(f))
        .map((f) => fs.readFile(f, 'utf8')),
    )
  ).join('\n');

  const copied = [];
  const skipped = [];

  for (const dir of ASSET_DIRS) {
    const absDir = path.join(SRC, dir);
    for (const entry of await fs.readdir(absDir, { withFileTypes: true })) {
      if (!entry.isFile()) continue;
      const rel = `${dir}/${entry.name}`;
      if (haystack.includes(entry.name)) {
        await copy(path.join(absDir, entry.name), path.join(OUT, dir, entry.name));
        copied.push(rel);
      } else {
        skipped.push({ rel, size: (await fs.stat(path.join(absDir, entry.name))).size });
      }
    }
  }
  return { copied, skipped };
}

// --- comprobaciones finales ------------------------------------------------

const FORBIDDEN = ['unpkg.com', 'text/babel', 'image-slot'];

async function verify() {
  const problems = [];
  const files = await walk(OUT);

  // 1. Nada del runtime de prototipo puede sobrevivir en dist/.
  for (const f of files.filter((f) => /\.(html|js)$/.test(f))) {
    const text = await fs.readFile(f, 'utf8');
    for (const needle of FORBIDDEN) {
      if (text.includes(needle)) {
        problems.push(`${path.relative(OUT, f)} todavia contiene "${needle}"`);
      }
    }
  }

  // 2. Toda referencia local tiene que existir en dist/. Esto atrapa cualquier
  //    imagen que la poda por basename haya descartado de mas.
  const existing = new Set(files.map((f) => path.relative(OUT, f).split(path.sep).join('/')));
  for (const f of files.filter((f) => f.endsWith('.html'))) {
    const text = await fs.readFile(f, 'utf8');
    for (const [, ref] of text.matchAll(/(?:src|href)="([^"]+)"/g)) {
      if (/^(https?:|mailto:|tel:|data:|#|\/\/)/.test(ref)) continue;
      const target = ref.split(/[?#]/)[0];
      if (!target) continue;
      if (!existing.has(target)) {
        problems.push(`${path.relative(OUT, f)} referencia "${target}", que no esta en dist/`);
      }
    }
  }
  return problems;
}

// --- main ------------------------------------------------------------------

async function main() {
  await fs.rm(OUT, { recursive: true, force: true });

  for (const file of SHARED) {
    const jsx = await fs.readFile(path.join(SRC, file), 'utf8');
    const name = file.replace(/\.jsx$/, '.js');
    await write(path.join(OUT, 'js', name), await compileJsx(prepare(jsx), file));
  }

  for (const page of PAGES) await buildPage(page);

  for (const f of VENDOR) {
    await copy(path.join(SRC, 'vendor', f), path.join(OUT, 'vendor', f));
  }

  for (const dir of COPY_WHOLE) {
    for (const f of await walk(path.join(SRC, dir))) {
      await copy(f, path.join(OUT, path.relative(SRC, f)));
    }
  }

  const { copied, skipped } = await copyReferencedAssets();

  console.log(`\nPaginas generadas: ${PAGES.length}`);
  console.log(`JSX compartido:    ${SHARED.length} (${SHARED.join(', ')})`);
  console.log(`React local:       ${VENDOR.length} archivos en vendor/`);
  console.log(`\nAssets copiados (${copied.length}):`);
  for (const r of copied) console.log(`  + ${r}`);

  console.log(`\nAssets omitidos por no estar referenciados (${skipped.length}):`);
  for (const s of skipped.sort((a, b) => b.size - a.size)) {
    console.log(`  - ${s.rel}  (${kb(s.size)})`);
  }

  if (warnings.length) {
    console.log(`\nAvisos del compilador (${warnings.length}):`);
    for (const w of warnings) console.log(`  ! ${w}`);
  }

  const problems = await verify();
  if (problems.length) {
    console.error(`\nBUILD FALLIDO — ${problems.length} problema(s):`);
    for (const p of problems) console.error(`  x ${p}`);
    process.exit(1);
  }

  console.log(`\nComprobaciones OK.`);
  console.log(`dist/ = ${mb(await dirSize(OUT))}  (project/ = ${mb(await dirSize(SRC))})`);
  console.log(`\nSubir el contenido de dist/ a /cipba/ en el hosting.\n`);
}

main().catch((err) => {
  console.error(`\nBUILD FALLIDO: ${err.message}`);
  process.exit(1);
});
