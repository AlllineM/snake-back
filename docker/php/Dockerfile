FROM php:7.4-fpm-alpine3.14

RUN set -ex \
  && apk --no-cache add \
    postgresql-dev

RUN docker-php-ext-install pdo pdo_pgsql

RUN apk add composer
