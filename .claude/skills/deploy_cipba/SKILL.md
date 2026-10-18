---
name: deploy_cipba
description: Prepara el paquete de deploy del WordPress local del CIPBA a un dominio de DonWeb (cipba.site, cipba.website, cipba.org u otro), siguiendo project/runbook-migracion-manual.md. Pregunta el dominio y el tipo de deploy, genera los archivos en wordpress/backups/deploy-<dominio>-AAAAMMDD-HHMM/ y deja el checklist del hosting. Usar cuando el usuario pida /deploy_cipba o preparar un deploy a producción/staging.
---

# Deploy del CIPBA a DonWeb

Referencia: `project/runbook-migracion-manual.md`. Este comando hace **solo la parte local** (Fases 0, 0-bis, 1 y 2): arma y verifica el paquete. **Nunca** te conectes al hosting, subas archivos ni toques producción: las Fases 3–7 las hace el usuario en el panel de Ferozo / FTP / phpMyAdmin.

## 1. Preguntar antes de empezar

Usá AskUserQuestion, en una sola llamada, con dos preguntas:

- **Dominio** (header "Dominio"): opciones `cipba.site`, `cipba.website` y `cipba.org` (producción final); el usuario puede escribir otro en "Other". Aceptá con o sin `https://`; el script lo normaliza.
- **Tipo de deploy** (header "Tipo"):
  - **Completo (base + wp-content)**: primera instalación o reinstalación, o cuando cambiaron contenido, menús, opciones, plugins o uploads.
  - **Solo tema**: solo cambió código de `wordpress/themes/cipba/`. No toca la base de producción.

Si eligió **Completo**, antes de seguir avisá del riesgo y pedí confirmación explícita (otra AskUserQuestion): importar la base **pisa todo el contenido del sitio de destino**, incluidas las consultas recibidas por el formulario de Contacto (Fluent Forms) y cualquier cambio cargado directamente allá (Datos del Distrito, trámites, documentos…). Si el sitio ya tiene contenido real propio, el runbook dice **no reimportar la base**: recomendá "Solo tema" y replicar a mano los cambios de contenido. Si el dominio es `cipba.org`, remarcalo especialmente. Si no confirma, terminá.

## 2. Chequeos de git (antes de correr)

- `git status --short -- wordpress/themes/cipba`: el tema se empaqueta **tal como está en disco**, incluidos los cambios sin commitear. Si los hay, avisalo (no bloquea).
- Si hay cambios en `style.css` o `assets/js/` respecto del último commit, o desde el último deploy (comparar con la versión del tema en el `resumen.txt` del `deploy-<dominio>-*` más reciente en `wordpress/backups/`, si existe), y `CHILD_THEME_CIPBA_VERSION` en `functions.php` **no** cambió: avisá que hay que subir la versión, porque si no los navegadores siguen usando el CSS/JS viejo. Ofrecé subirla (patch +1). No la cambies sin que el usuario acepte.

## 3. Generar el paquete

Desde la raíz del repo, con timeout de 600000 ms (el completo tarda 3–6 minutos):

```powershell
powershell -NoProfile -ExecutionPolicy Bypass -File .\wordpress\scripts\preparar-deploy.ps1 -Dominio <dominio>            # completo
powershell -NoProfile -ExecutionPolicy Bypass -File .\wordpress\scripts\preparar-deploy.ps1 -Dominio <dominio> -SoloTema  # solo tema
```

Si falla porque Docker o los contenedores no están corriendo, **no** los levantes: avisale al usuario (`cd wordpress; docker compose up -d`).

El script termina con `ALERTAS=<n>` y `DEPLOY_DIR=<ruta>`, y deja `resumen.txt` en esa carpeta.

## 4. Interpretar las alertas

- **Archivos sospechosos en wp-content** ("REVISAR"): **frená**. No recomiendes subir el paquete; mostrá las rutas y referí a `project/plan-limpieza-migracion.md`.
- **Plugins con actualización pendiente**: los mínimos obligatorios del runbook son **UpdraftPlus ≥ 1.26.8** y **FluentSMTP ≥ 2.4.1**; si alguno está por debajo, es bloqueante. Para el resto, mostrá la lista y preguntá si quiere actualizar antes de subir. Si acepta: `docker exec wordpress_app wp --allow-root plugin update <plugins…>`, después volvé a correr el script (repite el humo sobre el sitio actualizado). Si no, seguí: es la decisión que se viene tomando (ver "Plugins sin actualizar" en el registro del runbook).
- **Entradas de Fluent Forms > 0**: pedile que las revise o vacíe (Fluent Forms → Entries) y volvé a correr. No las borres vos.
- **Usuario `admin`**, **registro abierto**, **Duplicator / WP Reset instalados**: bloqueantes según el runbook (Fase 0 / 0-bis). Explicá cómo resolverlos; no los cambies sin permiso.
- **Humo con errores**, **restos de `localhost:8080`**, **zip incompleto o sin el tema o algún plugin**, **base local modificada**: bloqueantes; investigá la causa antes de dar el paquete por bueno.
- **Zip > 128 MB** (o el aviso de que está cerca): hay que subirlo por **FTP**, no por el Administrador de archivos.
- `blog_public = 1` en la salida del chequeo: esa opción viaja con la base. Si el destino es staging (`cipba.site`, `cipba.website`), recordá marcar *Ajustes → Lectura → Disuadir a los motores de búsqueda* después de importar.

## 5. Responder al usuario

Breve, en español:

1. Carpeta generada, archivos y tamaños, y el estado de las alertas.
2. Checklist del hosting con el dominio y los nombres de archivo reales:
   - **Completo, primera instalación o reinstalación:** Fase 3 (dominio, base nueva, PHP 8.4, **SSL activo antes de abrir por https**) → Fase 4 (WordPress limpio, misma versión que local (`WP core` del resumen), es_AR, prefijo `wp_`) → Fase 5 (subir y extraer `wp-content-<dominio>.zip` en la raíz, permisos 755/644) → Fase 6 (importar `deploy-<dominio>.sql` por phpMyAdmin) → Fase 7 (checklist post-migración, incluida **la purga de LiteSpeed Cache**) → Fase 8 (endurecer) si es reinstalación o la primera vez en ese hosting.
   - **Completo, re-deploy sobre una instalación que ya funciona:** Fases 5 y 6, después Enlaces permanentes → Guardar, Mega Menu → Menu Themes → Guardar, y purgar LiteSpeed Cache.
   - **Solo tema:** subir `cipba-tema-<versión>.zip` a `wp-content/themes/`, renombrar la carpeta `cipba` vieja a `cipba-old`, extraer (queda `wp-content/themes/cipba/`), probar el sitio, borrar `cipba-old` y el zip, y purgar LiteSpeed Cache. También se puede subir la carpeta por FTP.
   Remití al runbook para el detalle y a su tabla de *Solución de problemas*.
3. Recordá que la carpeta tiene hashes de contraseñas (backup y deploy `.sql`): no subirla a GitHub y borrar los zip y sql del hosting cuando termine.

## 6. Registrar el paquete

Para un deploy **completo** sin alertas bloqueantes, agregá una entrada en `project/runbook-migracion-manual.md`, sección "Registro de verificación", arriba de los paquetes anteriores, con el formato de las existentes: `### Paquete preparado el AAAA-MM-DD (vigente para <dominio>)`, archivos y tamaños, reemplazos, restos de `localhost:8080`, entradas del zip, versión del tema y de WP core, resultado del chequeo y del humo, y la lista de plugins sin actualizar. Marcá el paquete anterior del mismo dominio como "reemplazado por el del AAAA-MM-DD". No hagas commit sin que el usuario lo pida.
