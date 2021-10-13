FROM node:16

WORKDIR /var/www

RUN apt-get update -qq && apt-get install -y build-essential

RUN npm install -g sass node-sass sass-loader
RUN yarn add node-sass

