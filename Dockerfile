FROM php:8.4.1-fpm

# Instal dependensi Linux yang dibutuhkan untuk Kafka
RUN apt-get update && apt-get install -y \
    librdkafka-dev \
    git \
    unzip \
    libssh-dev \
    && rm -rf /var/lib/apt/lists/*

    RUN docker-php-ext-install pdo pdo_mysql

    RUN pecl install --force rdkafka && docker-php-ext-enable rdkafka

    COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set folder kerja di dalam Docker
WORKDIR /var/webapps