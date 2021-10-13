FROM node:16-alpine

WORKDIR /var/www

# yarn install требует наличия python2
RUN apk add --no-cache python2 make g++
