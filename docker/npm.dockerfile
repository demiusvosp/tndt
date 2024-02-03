FROM node:18

RUN apt-get update -qq && apt-get install -y build-essential

#RUN npm install -g sass node-sass sass-loader
#RUN yarn add node-sass

WORKDIR /app
