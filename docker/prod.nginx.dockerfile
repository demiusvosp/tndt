FROM nginx:alpine

COPY ./docker/nginx.conf /etc/nginx/conf.d/default.conf

COPY ./public /var/www/public

VOLUME /var/log/nginx