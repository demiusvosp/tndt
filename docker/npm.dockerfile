FROM node:20

RUN apt-get update -qq && apt-get install -y build-essential

WORKDIR /app
