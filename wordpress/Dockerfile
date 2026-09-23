FROM wordpress:7.1.1

# WP-CLI incluido en la imagen (antes se instalaba a mano y se perdia al recrear el contenedor)
ADD https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar /usr/local/bin/wp
RUN chmod +x /usr/local/bin/wp

# Apache escucha en 8080 (igual que el puerto publicado) para que el sitio pueda llamarse a si mismo
# por http://localhost:8080 desde dentro del contenedor (loopback de Duplicator, WP-Cron, health checks).
RUN sed -i 's/Listen 80$/Listen 8080/' /etc/apache2/ports.conf \
 && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:8080>/' /etc/apache2/sites-available/000-default.conf
