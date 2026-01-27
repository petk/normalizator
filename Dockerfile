FROM alpine

COPY . /opt/normalizator

RUN apk add --no-cache \
        curl \
        git \
        php85 \
        php85-fileinfo \
        php85-iconv \
        php85-intl \
        php85-mbstring \
        php85-phar \
    # Composer dependencies.
        php85-openssl \
        php85-simplexml \
        php85-tokenizer \
        php85-xmlwriter \
        php85-zip \
    # Link latest PHP version to executable.
    && (test -h /usr/bin/php || test -e /usr/bin/php) || ln -s /usr/bin/php85 /usr/bin/php 2>/dev/null \
    # Install Composer.
    && curl -sS https://getcomposer.org/installer | php -- \
        --install-dir=/usr/bin --filename=composer \
    # Adjust php.ini configuration.
    && echo "memory_limit = -1" >> /etc/php85/php.ini \
    && echo "phar.readonly = Off" >> /etc/php85/php.ini \
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
        php85-openssl \
        php85-simplexml \
        php85-tokenizer \
        php85-xmlwriter \
        php85-zip \
    && rm -rf /opt/normalizator \
    && rm /usr/bin/composer

WORKDIR "/opt/app"

ENTRYPOINT ["normalizator"]
