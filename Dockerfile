FROM php:7.4-alpine

WORKDIR /app

RUN apk add --no-cache --virtual .build $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -f "/usr/src/php.tar.xz" "/usr/src/php.tar.xz.asc" \
    && docker-php-source delete \
    && rm /usr/local/bin/phpdbg \
    && rm -rf /tmp/pear ~/.pearrc \
    && apk del .build

RUN { \
    echo "zend.assertions=1"; \
    echo "assert.exception=1"; \
    echo "memory_limit=-1"; \
    echo "error_reporting=-1"; \
    echo "display_errors=1"; \
} | tee /usr/local/etc/php/conf.d/custom.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer global require hirak/prestissimo

COPY composer.* ./
RUN composer install --no-autoloader --no-dev

COPY . .
RUN composer dump-autoload --no-dev -a

CMD ["/app/runner.sh"]
