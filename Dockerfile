FROM alpine

ENV \
    # Normalizator version.
    NORMALIZATOR_VERSION=0.0.1

COPY . /opt/normalizator

RUN apk add --no-cache \
        curl \
        git \
        php82 \
        php82-fileinfo \
        php82-intl \
        php82-mbstring \
        php82-phar \
    # Composer dependencies.
        php82-openssl \
        php82-simplexml \
        php82-tokenizer \
        php82-xmlwriter \
        php82-zip \
    # Link latest PHP version to executable.
    && ln -s /usr/bin/php82 /usr/bin/php \
    # Install Composer.
    && curl -sS https://getcomposer.org/installer | php -- \
        --install-dir=/usr/bin --filename=composer \
    # Adjust php.ini configuration.
    && echo "memory_limit = -1" >> /etc/php82/php.ini \
    && echo "phar.readonly = Off" >> /etc/php82/php.ini \
    # Build normalizator.phar.
    && cd /opt/normalizator \
    && ./bin/build \
    && chmod +x normalizator.phar \
    && mv normalizator.phar /usr/local/bin/normalizator \
    # Clean build dependencies.
    && apk del --no-cache php82-zip \
        php82-openssl \
        php82-tokenizer \
        php82-xmlwriter \
        php82-simplexml \
    && rm -rf /opt/normalizator \
    && rm /usr/bin/composer

WORKDIR "/opt/app"

ENTRYPOINT ["normalizator"]
