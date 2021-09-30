#!make
.PHONY: help up down ps exec init tests

env = dev
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
	$(info   init - initialize stage. (create db, migrates, create root user))
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

init:
	docker exec -it tndt_php_1 composer install # пока мы используем dev контейнер все ок, но в будущем для этого надо готовить отдельный контейнер с композером, git и yarn
#	docker exec -it tndt_php_1 php bin/console doctrine:schema:create -vv
	docker exec -it tndt_php_1 php bin/console doctrine:migrations:migrate --allow-no-migration -n -vv
	docker exec -it tndt_php_1 php bin/console doctrine:fixtures:load --group=install -n -vv

tests:
	docker exec -it tndt_php_1 php bin/console doctrine:fixtures:load -n -vv
	docker exec -it tndt_php_1 php ./vendor/bin/phpunit