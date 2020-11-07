FROM nginx:latest

ARG SITE_DOMAIN

ADD configs/nginx.conf /etc/nginx/
ADD configs/upstream.conf /etc/nginx/conf.d/
ADD configs/requestobserver.conf /etc/nginx/sites-templates/requestobserver.conf.template

RUN mkdir /etc/nginx/sites-available
RUN envsubst \$SITE_DOMAIN < /etc/nginx/sites-templates/requestobserver.conf.template > /etc/nginx/sites-available/requestobserver.conf

RUN usermod -u 1000 www-data