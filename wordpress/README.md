# WordPress local (CIPBA Distrito VII)

Entorno Docker de desarrollo + tema hijo `cipba` (sobre Astra).

## Uso
```
cd wordpress
cp .env.example .env      # y ajustar credenciales
docker compose up -d
```
- Sitio: http://localhost:8080 — phpMyAdmin: http://localhost:8081

## Que vive donde
- `themes/cipba/` — tema hijo, **versionado**; montado como bind mount en el contenedor (se edita aca).
- `wp-content` (plugins, uploads, otros temas) — volumen Docker `wordpress_docker_wp_content`, **no** esta en git.
- Base de datos — volumen `wordpress_docker_db_data`. Menus, paginas y opciones viven ahi, no en archivos.
- `plugins.txt` — lista de plugins y versiones (los de terceros no se versionan).
- `scripts/` — scripts WP-CLI reutilizables (`docker exec wordpress_app wp --allow-root eval-file ...`).

## Cuidado
- No correr `docker compose down -v` (borra los volumenes con la DB y los uploads).
- Al editar `style.css` subir `CHILD_THEME_CIPBA_VERSION` en `functions.php` para invalidar cache.
- Plan de migracion a DonWeb: `../project/plan-migracion-donweb.md`.
