# Laravel 11 PHP-FPM + Nginx + Composer
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --no-scripts --no-progress
COPY . .
RUN composer install --no-dev --no-interaction --prefer-dist --no-progress

FROM php:8.3-fpm-alpine AS php
RUN apk add --no-cache nginx supervisor icu-dev libzip-dev oniguruma-dev bash curl wget git \
    && docker-php-ext-install pdo pdo_mysql intl mbstring zip \
    && rm -rf /var/cache/apk/*
WORKDIR /var/www/html
COPY --from=vendor /app /var/www/html
RUN chown -R www-data:www-data storage bootstrap/cache

# Nginx config for Laravel
RUN mkdir -p /run/nginx
RUN printf "server {\n  listen 80;\n  server_name _;\n  root /var/www/html/public;\n  index index.php;\n  location /up { return 200 'ok'; add_header Content-Type text/plain; }\n  location / {\n    try_files $uri $uri/ /index.php?$query_string;\n  }\n  location ~ \\.php$ {\n    include fastcgi_params;\n    fastcgi_pass 127.0.0.1:9000;\n    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;\n    fastcgi_read_timeout 60s;\n  }\n}\n" > /etc/nginx/conf.d/default.conf

# Supervisor to run php-fpm and nginx
RUN printf "[supervisord]\nnodaemon=true\n\n[program:php-fpm]\ncommand=/usr/local/sbin/php-fpm -F\nautorestart=true\n\n[program:nginx]\ncommand=/usr/sbin/nginx -g 'daemon off;'\nautorestart=true\n" > /etc/supervisord.conf

ENV APP_ENV=production APP_DEBUG=false
EXPOSE 80
HEALTHCHECK --interval=30s --timeout=5s CMD wget -qO- http://127.0.0.1/up || exit 1
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
