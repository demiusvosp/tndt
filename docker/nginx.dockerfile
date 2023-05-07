FROM nginx:alpine as base
LABEL maintainer="Dmitry demius Vospennikov"
LABEL description="Task and Doc Tracker application frontend part"

RUN apk add -U tzdata


FROM base as dev

COPY ./docker/dev.nginx.conf /etc/nginx/conf.d/default.conf

VOLUME /app/public
VOLUME /var/log/nginx


FROM base as prod

COPY ./docker/prod.nginx.conf /etc/nginx/conf.d/default.conf
COPY ./public /app/public
