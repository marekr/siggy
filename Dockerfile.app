FROM siggy-core:latest

COPY .docker/php/conf/www.conf /usr/local/etc/php-fpm.d/
COPY .docker/php/conf/php-fpm.conf /usr/local/etc/php-fpm.conf
COPY .docker/php/conf/docker.conf /usr/local/etc/php-fpm.d/

COPY .docker/entrypoint.sh /entrypoint.sh

RUN chown -R www-data:www-data /var/www
WORKDIR /var/www
USER www-data

COPY --chown=www-data:www-data composer.lock ./
COPY --chown=www-data:www-data . .

RUN composer install
RUN composer clear-cache

