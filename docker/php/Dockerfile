FROM php:8.3-fpm-alpine

# Packages runtime + outilsss
RUN set -eux; \
    apk add --no-cache bash git icu-libs libpq libzip oniguruma

# === Build des extensions ===
RUN set -eux; \
    # deps de compilation (contiennent autoconf, make, gcc, etc.)
    apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS icu-dev libpq-dev libzip-dev oniguruma-dev; \
    \
    docker-php-ext-install intl pgsql pdo_pgsql opcache; \
    \
    # PECL peut demander une maj de protocole
    pecl channel-update pecl.php.net; \
    pecl install apcu; \
    docker-php-ext-enable apcu; \
    \
    # nettoyage (on garde seulement les libs runtime)
    apk del .build-deps; \
    rm -rf /tmp/pear

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Répertoire de travail
WORKDIR /var/www/html

# Optimisation: cache composer dans /tmp/composer (monté en volume)
ENV COMPOSER_CACHE_DIR=/tmp/composer

# Installe les deps si composer.json existe (builds reproductibles)
COPY composer.json composer.lock* ./
RUN if [ -f composer.json ]; then composer install --no-scripts --no-progress --prefer-dist; fi

# Copie du code (en dev on monte un volume par-dessus)
COPY . .

# Droits conseillés (facultatif selon ton setup)
RUN mkdir -p var && chown -R www-data:www-data var
USER www-data
