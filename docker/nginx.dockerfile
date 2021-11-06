FROM nginx:alpine as base

COPY ./public /app/public


FROM base as dev_stage

COPY ./docker/dev.nginx.conf /etc/nginx/conf.d/default.conf

VOLUME /var/log/nginx


FROM base as prod_stage

COPY ./docker/prod.nginx.conf /etc/nginx/conf.d/default.conf
