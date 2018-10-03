FROM php:5.5-cli

RUN apt-get update && apt-get install -y zip unzip gzip libc6 --no-install-recommends && rm -r /var/lib/apt/lists/*

RUN docker-php-ext-install mbstring \
    && curl -s https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ADD . /opt/graze/data-flow

WORKDIR /opt/graze/data-flow

CMD /bin/bash
