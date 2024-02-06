FROM php:8.2-apache

# Set the working directory to /var/www/html
WORKDIR /var/www/html

# Install any dependencies your application needs
RUN apt-get update && \
    apt-get install -y libpq-dev libyaml-dev zip unzip nano && \
    pecl install yaml && \
    docker-php-ext-install pdo pdo_mysql && \
    docker-php-ext-enable yaml

RUN a2enmod rewrite
RUN service apache2 restart

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Expose port 80 for the web server
EXPOSE 80

# Start Apache in the foreground
CMD composer install --no-interaction --optimize-autoloader;apache2-foreground