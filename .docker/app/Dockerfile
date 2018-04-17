FROM php:7.2-fpm

LABEL Name=siggy Version=0.0.1

RUN apt-get update

#################################################
# NGINX INSTALL
#################################################
RUN apt-get install nginx -y

#################################################
#NGINX SETUP
#################################################

# delete any default configs
RUN rm -rf /etc/nginx/conf.d/*
RUN rm -rf /etc/nginx/sites-available/*
RUN rm -rf /etc/nginx/sites-enabled/*

RUN mkdir /etc/nginx/sites

COPY ./.docker/app/nginx/conf/nginx.conf /etc/nginx/
COPY ./.docker/app/nginx/conf/conf.d/ /etc/nginx/conf.d/
COPY ./.docker/app/nginx/conf/sites/ /etc/nginx/sites/

#################################################
#PHP SETUP
#################################################
RUN apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libssl-dev \
        libpng-dev
RUN docker-php-ext-install -j$(nproc) iconv \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd


#curl extension
RUN apt install -y curl
RUN apt install -y libcurl3
RUN apt install -y libcurl3-dev
RUN docker-php-ext-install -j$(nproc) curl

# Misc
RUN docker-php-ext-install fileinfo
RUN docker-php-ext-install -j$(nproc) ctype
RUN docker-php-ext-install -j$(nproc) json
RUN docker-php-ext-install -j$(nproc) hash
RUN docker-php-ext-install -j$(nproc) sockets
RUN docker-php-ext-install -j$(nproc) mbstring
RUN docker-php-ext-install -j$(nproc) tokenizer


# databases
RUN apt install -y libpq-dev
RUN apt install -y mysql-client
RUN docker-php-ext-install -j$(nproc) pdo
RUN docker-php-ext-install -j$(nproc) pgsql
RUN docker-php-ext-install -j$(nproc) pdo_pgsql
RUN docker-php-ext-install -j$(nproc) pdo_mysql 
RUN docker-php-ext-install -j$(nproc) mysqli

#xml
RUN apt install -y libxml2-dev
RUN apt install -y libxslt-dev
RUN docker-php-ext-install -j$(nproc) xml

#laravel supervisor
RUN apt install -y supervisor

# copy app
RUN rm -rf /var/www \
    && mkdir -p /var/www

# overwrite default configs
#COPY ./.docker/php/conf/php.ini /usr/local/etc/php/
COPY ./.docker/app/php/conf/php-fpm.conf /usr/local/etc/php-fpm.conf

#clean out the php-fpm directory and drop a complete config instead of the partial mess
RUN rm -rf /usr/local/etc/php-fpm.d/*
COPY ./.docker/app/php/conf/www.conf /usr/local/etc/php-fpm.d/www.conf


#################################################
#Cron Setup
#################################################
# install cron
RUN apt-get -y install cron

# Create the log file to be able to run tail
RUN touch /var/log/cron.log

#insert our cronjob
COPY ./.docker/app/artisan.crontab /etc/cron.d/artisan.crontab
RUN chmod 0644 /etc/cron.d/artisan.crontab

# create php user
RUN groupadd -g 3100 php-app
RUN useradd -g php-app -u 3100 php-app -ms /bin/bash
RUN passwd -l php-app

#create nginx user
RUN groupadd -g 3101 nginx
RUN useradd -g nginx -u 3101 nginx -ms /bin/bash
RUN passwd -l nginx

#add nginx to php-app group
RUN usermod -a -G php-app nginx


#################################################
#App data files
#################################################
COPY ./ /var/www/
RUN chmod -R 777 /var/www/storage && \
	chmod -R 777 /var/www/bootstrap/cache

RUN chown www-data:www-data -R /var/www/

WORKDIR /var/www

#################################################
#Entrypoint
#################################################
ADD ./.docker/app/entrypoint.sh /entrypoint.sh
RUN chmod a+x /entrypoint.sh
CMD /entrypoint.sh

EXPOSE 80/tcp 443/tcp 9000/tcp