# Container ini sengaja TIDAK membatasi shell_exec/exec/file_get_contents —
# beberapa studi kasus (command injection, SSRF) butuh itu semua aktif.
# Base image resmi php:apache tidak menonaktifkan fungsi apa pun secara
# default, jadi tidak perlu konfigurasi tambahan untuk itu.
FROM php:8.2-apache

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html/data /var/www/html/uploads \
    && chmod -R 775 /var/www/html/data /var/www/html/uploads

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Render (dan platform sejenis) menyuntikkan PORT lewat environment variable
# saat container dijalankan — Apache defaultnya cuma dengar di port 80, jadi
# entrypoint ini yang nyesuaikan di waktu start, bukan di waktu build.
ENV PORT=8080
EXPOSE 8080

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
