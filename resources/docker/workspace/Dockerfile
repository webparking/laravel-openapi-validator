FROM php:8.1-cli-alpine

ARG PUID=1000
ARG PGID=1000

RUN apk add --no-cache --virtual .build-deps \
    # phpize for extensions
    $PHPIZE_DEPS \
    && \
    apk add --no-cache \
    bash \
    # unzip for composer
    unzip \
    && \
    pecl install \
    # pcov for coverage runs
    pcov && docker-php-ext-enable pcov \
    && \
    apk del .build-deps

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Add a non-root user to prevent files being created with root permissions on host machine.
RUN addgroup -g ${PGID} user && \
    adduser -u ${PUID} -G user -D user

USER user
