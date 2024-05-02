FROM alpine

COPY . /opt/normalizator

RUN apk add --no-cache \
        curl \
        git \
        php83 \
        php83-fileinfo \
        php83-intl \
        php83-mbstring \
        php83-phar \
    # Composer dependencies.
        php83-openssl \
        php83-simplexml \
        php83-tokenizer \
        php83-xmlwriter \
        php83-zip \
    # Link latest PHP version to executable.
    && (test -h /usr/bin/php || test -e /usr/bin/php) || ln -s /usr/bin/php83 /usr/bin/php 2>/dev/null \
    # Install Composer.
    && curl -sS https://getcomposer.org/installer | php -- \
        --install-dir=/usr/bin --filename=composer \
    # Adjust php.ini configuration.
    && echo "memory_limit = -1" >> /etc/php83/php.ini \
    && echo "phar.readonly = Off" >> /etc/php83/php.ini \
    # Build normalizator.phar.
    && cd /opt/normalizator \
    && composer install -q --no-dev \
    && ./bin/build \
    && chmod +x normalizator.phar \
    && mv normalizator.phar /usr/local/bin/normalizator \
    # Clean build dependencies.
    && apk del --no-cache \
        curl \
        git \
        php83-openssl \
        php83-simplexml \
        php83-tokenizer \
        php83-xmlwriter \
        php83-zip \
    && rm -rf /opt/normalizator \
    && rm /usr/bin/composer

WORKDIR "/opt/app"

ENTRYPOINT ["normalizator"]
