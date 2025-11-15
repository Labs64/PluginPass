FROM wordpress:latest

# Install system dependencies and development tools
RUN apt-get update && apt-get -y install \
    vim \
    git \
    zip \
    unzip \
    wget \
    && rm -rf /var/lib/apt/lists/*

RUN git --version

# Verify PHP version (should be 8.2+ for WordPress latest)
RUN php -v

# Install WP-CLI
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x wp-cli.phar \
    && mv wp-cli.phar /usr/local/bin/wp

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

# Enable recommended PHP extensions for WordPress
RUN docker-php-ext-install opcache

# Configure PHP for development
RUN echo "display_errors = On" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "log_errors = On" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "upload_max_filesize = 64M" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/custom.ini

# Set working directory
WORKDIR /var/www/html
