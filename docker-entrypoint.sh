#!/bin/sh
set -e

# Ganti port yang Apache dengarkan sesuai $PORT yang disuntikkan platform
# hosting (Render dkk kasih port beda tiap deploy, gak selalu 80/8080).
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/:80>/:${PORT}>/" /etc/apache2/sites-available/000-default.conf

exec "$@"
