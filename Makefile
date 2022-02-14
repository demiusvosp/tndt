#!make
.PHONY: help up down ps exec init tests build_front

# default arguments
env = dev
type = unit
pwd = $(shell pwd)

ifeq ($(env), dev)
	compose_file =
else
	compose_file = -f docker-compose.yml -f docker-compose.$(env).yml
endif

help:
	$(info Usage commands:)
	$(info   make help - print this help)
	$(info   up ENV=test - up current environment (default dev))
	$(info   down ENV=test - down current environment (default dev))
	$(info   ps ENV=test - compose ps current environment (default dev))
	$(info   exec - login to current php container shell)
	$(info   front_build [watch] - exec yarn install and compile and deploy front o public (one or watches changes) )
	$(info   front_exec [<command>] - exec command into front_builder container)
	$(info   init - initialize stage. (create db, migrates, create root user))
	$(info   tests type=behat (default unit) - run test suites)
	$(info )

up:
	$(info Up $(env) environment stack)
	docker-compose  $(compose_file) up -d

down:
	docker-compose $(compose_file)  stop

ps:
	docker-compose $(compose_file)  ps

exec:
	docker exec -it tndt_php_1 /bin/bash

front_exec:
	docker-compose run front_builder $(filter-out $@,$(MAKECMDGOALS))

front_build:
	docker-compose run front_builder yarn install
	docker-compose run front_builder yarn encore dev $(filter-out $@,$(MAKECMDGOALS))
%:

init:
	docker exec -it tndt_php_1 composer install # пока мы используем dev контейнер все ок, но в будущем для этого надо готовить отдельный контейнер с композером, git и yarn
	docker exec -it tndt_php_1 php bin/console doctrine:schema:create -vv
	docker exec -it tndt_php_1 php bin/console doctrine:migrations:migrate --allow-no-migration -n -vv
	docker exec -it tndt_php_1 php bin/console doctrine:fixtures:load --group=install -n -vv

tests:
ifeq ($(type), behat)
	docker exec -it tndt_php_1 php ./vendor/bin/behat
else
	docker exec -it tndt_php_1 php bin/console doctrine:fixtures:load -n -vv
	docker exec -it tndt_php_1 php ./vendor/bin/phpunit
endif
