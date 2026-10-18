---
name: bkp_cipba
description: Genera un backup completo del entorno local de WordPress del CIPBA (dump de la base, wp-content y .env) en wordpress/backups/local-AAAAMMDD-HHMM/, para replicar el entorno en otra PC. Usar cuando el usuario pida /bkp_cipba o un backup del entorno local.
---

# Backup del entorno local CIPBA

Genera los 3 archivos que hacen falta (además del código de GitHub) para replicar el entorno en otra PC. El procedimiento de restauración está en `wordpress/README.md`, sección "Replicar el entorno en otra PC".

## Pasos

1. Correr el script (con PowerShell; tarda 1–3 minutos, usar un timeout de 600000 ms):
   ```powershell
   powershell -NoProfile -ExecutionPolicy Bypass -File .\wordpress\scripts\backup-entorno-local.ps1
   ```
   Si falla porque los contenedores no están corriendo, **no** los levantes por tu cuenta: avisale al usuario (`cd wordpress; docker compose up -d`).

2. Revisar la salida: debe terminar con `Backup listo:` y una línea `BACKUP_DIR=…`. El script ya verifica que el dump termine en "Dump completed" y que el `.tar.gz` se pueda leer.

3. Correr `git status --short` y, si hay cambios sin commitear o commits sin pushear (`git log @{u}..`), avisarlo: esos cambios **no** están en el backup ni en GitHub. No hagas commit ni push sin que el usuario lo pida.

4. Responder breve, en español:
   - Carpeta generada y los 3 archivos con su tamaño.
   - Estado de git (si hay algo pendiente).
   - Recordatorio: llevar la carpeta por pendrive o un Drive privado; **no subirla a GitHub** (tiene hashes de contraseñas y envíos del formulario). Para restaurar, ver `wordpress/README.md`.
   - Si hay backups `local-*` viejos en `wordpress/backups/`, mencionar cuántos son y cuánto ocupan, por si el usuario quiere borrarlos. No borrar nada por tu cuenta.
