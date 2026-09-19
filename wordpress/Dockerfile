FROM wordpress:latest

# WP-CLI incluido en la imagen (antes se instalaba a mano y se perdia al recrear el contenedor)
ADD https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar /usr/local/bin/wp
RUN chmod +x /usr/local/bin/wp
