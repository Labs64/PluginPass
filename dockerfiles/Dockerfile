FROM wordpress:latest

MAINTAINER Labs64 NetLicensing netlicensing@labs64.com

# Install some must-haves
RUN apt-get update
RUN apt-get -y install vim git
RUN git --version

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version
