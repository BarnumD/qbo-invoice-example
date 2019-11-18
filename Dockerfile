# escape=`
FROM php:7.2-cli

#Set timezone
RUN echo "UTC" > /etc/timezone
RUN dpkg-reconfigure -f noninteractive tzdata

#Install general pre-requisites
RUN apt-get update; `
    apt-get install -y git zip curl wget

#Install PHP tools
RUN apt-get update && apt-get install --yes --no-install-recommends libssl-dev

#Install Composer
RUN curl -sS https://getcomposer.org/installer -o composer-setup.php; `
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer; `
    rm composer-setup.php;

COPY . /app
WORKDIR /app
RUN composer install
CMD [ "php", "-S", "0.0.0.0:80" ]