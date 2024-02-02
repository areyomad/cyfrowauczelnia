FROM php:8.2-apache

# Instalacja wymaganych pakietów i rozszerzeń PHP
RUN apt-get update && apt-get install -y unixodbc-dev gnupg2 \
    && curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - \
    && curl https://packages.microsoft.com/config/debian/11/prod.list > /etc/apt/sources.list.d/mssql-release.list \
    && apt-get update \
    && ACCEPT_EULA=Y apt-get install -y -o Dpkg::Options::="--force-overwrite" msodbcsql17 \
    # Instalacja rozszerzeń PHP
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && pecl install sqlsrv pdo_sqlsrv \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv
