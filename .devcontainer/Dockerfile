FROM php:8.3-cli

# Extensões necessárias para Pest/PHPUnit e ferramentas de desenvolvimento
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install -j"$(nproc)" dom mbstring \
    && rm -rf /var/lib/apt/lists/*

# pcov: driver de cobertura leve para os testes (pest --coverage)
RUN pecl install pcov \
    && docker-php-ext-enable pcov

# Instalar Composer manualmente (caso não use feature)
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm composer-setup.php

WORKDIR /app
