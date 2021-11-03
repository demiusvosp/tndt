FROM nginx:alpine

COPY ./docker/nginx.conf /etc/nginx/conf.d/default.conf

COPY ./public /app/public

VOLUME /var/log/nginx