#!make
.PHONY: help up down ps back_bash back_exec front_build front_exec redis_console init tests

# default arguments
env = dev
type = unit
pwd = $(shell pwd)
user_id = $(shell id -u)

ifeq ($(env), dev)
	compose_file =
	profiles = --profile dev
else
	compose_file = -f compose.yml -f compose.$(env).yml
	profiles =
endif

help:
	$(info Usage commands:)
	$(info   make help - print this help)
	$(info   up ENV=test - up current environment (default dev))
	$(info   down ENV=test - down current environment (default dev))
	$(info   ps ENV=test - compose ps current environment (default dev))
	$(info   back_bash - login to current php container shell)
	$(info   back_exec [<command>] - exec comand into php container)
	$(info   front_build [watch] - exec yarn install and compile and deploy front o public (one or watches changes) )
	$(info   front_exec [<command>] - exec command into front_builder container)
	$(info   redis_console - run redis-cli and connect to tndt redis container)
	$(info   init - initialize stage. (create db, migrates, create root user))
	$(info   tests type=behat (default unit) - run test suites)
	$(info )

up:
	$(info Up $(env) environment stack)
	docker compose $(compose_file) $(profiles) up -d

down:
	docker compose $(compose_file) $(profiles)  stop

ps:
	docker compose $(compose_file)  ps

back_exec:
	docker compose exec -u$(user_id) php '$(filter-out $@,$(MAKECMDGOALS))'

back_bash:
	docker compose exec -u$(user_id) php  /bin/bash

front_exec:
	docker compose run --rm -u$(user_id)  front_builder $(filter-out $@,$(MAKECMDGOALS))

front_build:
	docker compose run --rm front_builder yarn install
	docker compose run --rm front_builder yarn encore $(env) $(filter-out $@,$(MAKECMDGOALS))
%:

redis_console:
	redis-cli -h localhost -p 4004

init:
	docker compose exec php composer install
	docker compose exec php bin/console doctrine:schema:create -vv
	docker compose exec php bin/console doctrine:migrations:migrate --allow-no-migration -n -vv

tests:
ifeq ($(type), behat)
	docker compose exec php ./vendor/bin/behat
else
	docker compose exec php bin/console doctrine:fixtures:load -n -vv
	docker compose exec php ./vendor/bin/phpunit
endif
