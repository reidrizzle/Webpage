FROM php:8.2-apache

# Install PDO MySQL extension
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy project files to Apache root
COPY . /var/www/html/

# Create uploads directory and set permissions
RUN mkdir -p /var/www/html/uploads/avatars \
    && chmod -R 777 /var/www/html/uploads \
    && chown -R www-data:www-data /var/www/html

# Expose default port
EXPOSE 80
