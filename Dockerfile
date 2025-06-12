# Use the official PHP 8.1 image with Apache
FROM php:8.2-apache

# Install the necessary extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN a2enmod rewrite

# Set the working directory to the web root
WORKDIR /var/www/html/

# Expose port 80
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
