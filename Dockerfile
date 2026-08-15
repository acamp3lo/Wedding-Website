FROM php:8.4-apache

# Install cron for scheduled tasks and clean up apt cache to reduce image size
RUN apt-get update && apt-get install -y \
    cron \
    && rm -rf /var/lib/apt/lists/*

# Install required MySQL extensions for PHP
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Copy Crontab
COPY config/crontab.txt /crontab.txt

# Create a cron file with the right permissions and an explicitly specified user
RUN crontab -u root /crontab.txt && rm /crontab.txt

# Enable Apache rewrite module
RUN a2enmod rewrite

# Configure Apache DocumentRoot to point directly to /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Set working directory
WORKDIR /var/www/html

# Copy project files into container
COPY . /var/www/html/

EXPOSE 80

# Start cron alongside Apache so scheduled tasks run in the container
CMD ["sh", "-c", "service cron start && apache2-foreground"]