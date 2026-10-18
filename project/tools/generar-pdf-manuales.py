"""Genera los PDF de los manuales a partir de los .md de project/.

    pip install markdown            # una sola vez
    python project/tools/generar-pdf-manuales.py

Convierte cada manual a HTML con los colores y tipografias del sitio y lo
imprime a PDF con Chrome (o Edge) en modo headless. Los PDF quedan al lado
de los .md (project/manual-*.pdf). Correrlo de nuevo cada vez que cambie un .md.

Formato esperado del .md: un "# Titulo", el texto de portada y un separador
"---"; a partir de ahi, secciones "## ...". Los enlaces internos del indice usan
los anclajes estilo GitHub ("#1-antes-de-empezar").
"""
import re
import shutil
import subprocess
import sys
import tempfile
from datetime import date
from pathlib import Path

try:
    import markdown
except ImportError:
    sys.exit("Falta la libreria 'markdown': pip install markdown")

REPO = Path(__file__).resolve().parents[2]
TEMA = REPO / "wordpress" / "themes" / "cipba"
FONTS = (TEMA / "assets" / "fonts").as_uri()
LOGO = (TEMA / "assets" / "img" / "logo-blanco.svg").as_uri()

MANUALES = [
    ("manual-usuario-contenido", "Manual de usuario", "Carga de contenido del sitio"),
    ("manual-tecnico-tema", "Manual técnico", "Mantenimiento del tema cipba"),
]

NAVEGADORES = [
    r"C:\Program Files\Google\Chrome\Application\chrome.exe",
    r"C:\Program Files (x86)\Google\Chrome\Application\chrome.exe",
    r"C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe",
    r"C:\Program Files\Microsoft\Edge\Application\msedge.exe",
    "google-chrome", "chromium", "chromium-browser", "microsoft-edge",
]

CSS = """
@font-face { font-family: Lato; font-weight: 400; src: url('%(f)s/lato-400.woff2'); }
@font-face { font-family: Lato; font-weight: 700; src: url('%(f)s/lato-700.woff2'); }
@font-face { font-family: Lato; font-weight: 900; src: url('%(f)s/lato-900.woff2'); }
@font-face { font-family: 'Roboto Condensed'; font-weight: 700; src: url('%(f)s/roboto-condensed-700.woff2'); }
@font-face { font-family: 'Roboto Condensed'; font-weight: 900; src: url('%(f)s/roboto-condensed-900.woff2'); }

@page {
  size: A4;
  margin: 20mm 18mm;
  @bottom-left { content: "CIPBA Distrito VII — %(titulo)s"; font: 8pt Lato, sans-serif; color: #607a7c; }
  @bottom-right { content: "Página " counter(page) " de " counter(pages); font: 8pt Lato, sans-serif; color: #607a7c; }
}
@page :first { @bottom-left { content: none; } @bottom-right { content: none; } }

html { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
body { font-family: Lato, system-ui, sans-serif; font-size: 10pt; line-height: 1.5; color: #373d3e; margin: 0; }

/* Portada */
.cover { height: 250mm; display: flex; flex-direction: column; break-after: page; }
.cover__band { background: linear-gradient(135deg,#14484a 0%%,#1a5f5c 60%%,#00a48a 100%%); color: #fff; padding: 22mm 14mm 16mm; border-radius: 6px; }
.cover__band img { height: 16mm; margin-bottom: 14mm; }
.cover__eyebrow { font-family: 'Roboto Condensed', sans-serif; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; font-size: 10pt; color: #c0e2ca; }
.cover h1 { font-family: 'Roboto Condensed', sans-serif; font-weight: 900; font-size: 30pt; line-height: 1.1; margin: 4mm 0 3mm; color: #fff; }
.cover__sub { font-size: 13pt; color: #e8f5f1; margin: 0; }
.cover__intro { padding: 12mm 2mm 0; font-size: 11pt; }
.cover__intro blockquote { margin-top: 6mm; }
.cover__meta { margin-top: auto; padding: 4mm 2mm 0; font-size: 9pt; color: #607a7c; border-top: 1px solid #c0e2ca; }

/* Titulos: las secciones corren seguidas (sin salto de pagina forzado),
   pero un titulo nunca queda solo al pie de una pagina. */
h1, h2, h3, h4 { font-family: 'Roboto Condensed', 'Arial Narrow', sans-serif; color: #14484a; line-height: 1.2; break-after: avoid; }
h2 { font-size: 17pt; font-weight: 900; margin: 10mm 0 4mm; padding-bottom: 2mm; border-bottom: 2px solid #00a48a; }
h3 { font-size: 13pt; font-weight: 700; margin: 6mm 0 2mm; color: #008c75; }
h4 { font-size: 11pt; margin: 4mm 0 1mm; }
h2 + p, h3 + p, h3 + ul, h3 + ol { break-before: avoid; }
#indice { margin-top: 0; }

p { margin: 0 0 2.5mm; }
p, li { orphans: 3; widows: 3; }
a { color: #008c75; text-decoration: none; }
strong { color: #14484a; }
ul, ol { margin: 0 0 3mm; padding-left: 6mm; }
li { margin-bottom: 1mm; }
hr { display: none; }

code { font-family: Consolas, 'Courier New', monospace; font-size: 8.6pt; background: #eef2f1; color: #14484a; padding: .3mm 1.2mm; border-radius: 3px; overflow-wrap: break-word; }
pre { background: #0d3234; color: #e8f5f1; padding: 3mm 4mm; border-radius: 4px; font-size: 7.5pt; line-height: 1.4; white-space: pre-wrap; overflow-wrap: anywhere; break-inside: avoid; margin: 0 0 3mm; }
pre code { background: none; color: inherit; padding: 0; font-size: inherit; }

blockquote { margin: 0 0 3mm; padding: 2.5mm 4mm; background: #e8f5f1; border-left: 3px solid #00a48a; border-radius: 0 4px 4px 0; }
blockquote p:last-child { margin-bottom: 0; }

table { width: 100%%; border-collapse: collapse; margin: 0 0 4mm; font-size: 8.8pt; }
thead { display: table-header-group; }
tr { break-inside: avoid; }
th { background: #14484a; color: #fff; text-align: left; font-weight: 700; padding: 1.8mm 2.2mm; }
td { padding: 1.6mm 2.2mm; border-bottom: 1px solid #c0e2ca; vertical-align: top; }
tbody tr:nth-child(even) td { background: #f7faf9; }

/* Indice en dos columnas */
#indice + ol { columns: 2; column-gap: 10mm; padding-left: 5mm; }
#indice + ol li { break-inside: avoid; }
"""


def slugify(value, separator="-"):
    """Mismo criterio que GitHub, para que funcionen los enlaces del indice."""
    value = re.sub(r"[^\w\- ]", "", value.strip().lower())
    return value.replace(" ", separator)


def buscar_navegador():
    for nav in NAVEGADORES:
        ruta = nav if Path(nav).is_file() else shutil.which(nav)
        if ruta:
            return ruta
    sys.exit("No se encontro Chrome ni Edge para imprimir el PDF.")


def version_tema():
    m = re.search(r"CHILD_THEME_CIPBA_VERSION',\s*'([^']+)'", (TEMA / "functions.php").read_text(encoding="utf-8"))
    return m.group(1) if m else "?"


def armar_html(slug, titulo, subtitulo):
    src = (REPO / "project" / f"{slug}.md").read_text(encoding="utf-8")
    m = re.match(r"#\s+(.+?)\n(.*?)\n---\n(.*)", src, re.S)
    if not m:
        sys.exit(f"{slug}.md: falta el '# Titulo' o el separador '---' de la portada.")
    intro_md, cuerpo_md = m.group(2), m.group(3)

    md = markdown.Markdown(
        extensions=["tables", "fenced_code", "toc", "sane_lists"],
        extension_configs={"toc": {"slugify": slugify}},
    )
    intro = md.convert(intro_md)
    md.reset()
    cuerpo = md.convert(cuerpo_md).replace('id="índice"', 'id="indice"')

    return f"""<!doctype html><html lang="es"><head><meta charset="utf-8">
<title>{titulo} — CIPBA Distrito VII</title>
<style>{CSS % {"f": FONTS, "titulo": titulo}}</style></head><body>
<section class="cover">
  <div class="cover__band">
    <img src="{LOGO}" alt="CIPBA Distrito VII">
    <div class="cover__eyebrow">Sitio web CIPBA Distrito VII</div>
    <h1>{titulo}</h1>
    <p class="cover__sub">{subtitulo}</p>
  </div>
  <div class="cover__intro">{intro}</div>
  <div class="cover__meta">Colegio de Ingenieros de la Provincia de Buenos Aires — Distrito VII<br>
  Versión del tema: {version_tema()} · Actualizado al {date.today():%d/%m/%Y}</div>
</section>
{cuerpo}
</body></html>"""


def main():
    navegador = buscar_navegador()
    with tempfile.TemporaryDirectory() as tmp:
        tmp = Path(tmp)
        for slug, titulo, subtitulo in MANUALES:
            html = tmp / f"{slug}.html"
            html.write_text(armar_html(slug, titulo, subtitulo), encoding="utf-8")
            pdf = REPO / "project" / f"{slug}.pdf"
            subprocess.run(
                [
                    navegador, "--headless=new", "--disable-gpu", "--no-first-run",
                    f"--user-data-dir={tmp / 'perfil'}", "--no-pdf-header-footer",
                    "--virtual-time-budget=8000", "--allow-file-access-from-files",
                    f"--print-to-pdf={pdf}", html.as_uri(),
                ],
                check=True, capture_output=True, timeout=180,
            )
            print(f"{pdf.relative_to(REPO)}  ({pdf.stat().st_size / 1024:.0f} KB)")


if __name__ == "__main__":
    main()
