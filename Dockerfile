FROM php:8.2-fpm

WORKDIR /var/www/salestool

RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    sqlite3 \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_sqlite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["entrypoint.sh"]