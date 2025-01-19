Установка системы
============================

`<ENVIRONMENT>` - prod, dev or test.

### Локальный инстанс
1. Распаковать архив или git clone
2. `make up`
3. `make init`
4. `make build_front`
5. check write permission for ./var directory

### prod Стейдж
1. Скопировать compose.prod.distV2.yml в место разворачивания стейджа
2. Отредактировать под устанавливаемый стейдж
3. Скопировать содержимое `config/prod` в отдельную диреткорию и настроить систему 
3. `docker compose up -d`
4. `docker-compose exec php ./bin/console cache:clear`
5. `docker-compose exec php ./bin/console cache:warmup`
6. `docker-compose exec php bin/console doctrine:schema:create -vv` # для первого запуска
6. `docker-compose exec php ./bin/console doctrine:migrations:migrate`
7. `docker-compose exec php chmod 777 -R /app/var/cache/prod`


Обновление системы
============================

### dev-stage
1. обновление php-пакетов `make back_exec composer install`
2. `docker-compose exec php ./bin/console doctrine:migrations:migrate`
3. обновлние фронтенд `make front_build`

### prod-stage
2. `docker compose pull`
3. `docker-compose up -d`
4. `docker-compose exec php ./bin/console cache:clear`
5. `docker-compose exec php ./bin/console cache:warmup`
6. `docker-compose exec php ./bin/console doctrine:migrations:migrate`
7. `docker-compose exec php chmod 777 -R /app/var/cache/prod`

### Обновления требующие дополнительных действий
 - [версия 0.3.1](upgrade_3.1.md)

[Полный список версий](history.md) 

Особенности обслуживания системы
-------

### База данных
Для корректной работы с текстами на национальных языках, в частности с русским необходимо проверить, что созданная БД
имеет сравнение utf8mb4_general_ci, таблица же migrations для поддержки длинных названий миграций должна иметь
сравнение ascii_general_ci в ней никогда не будет русских символов. В будущем при схлопывании миграций будут убраны
длинное название миграции создающей root пользователя и данное требование станет неактуальным.

### Директория var
Необходимо следить, чтобы поддиректории ./var были доступны для записи. Теоретически они создаются системой
автоматически с нужными полномочиями, но при запуске команд в контейнере от root, при работе php-fpm от другого
пользователя часть кеш, создаваемый `./bin/console cache:warmup` может быть не от того пользователя. Необходимо либо
вызывать команды от того же пользователя системы, под которым работает php-fpm, либо после вызова этих команд делать
`chown www-data:www-data -R ./var/`

### Миграции базы данных
`make back_exec "bin/console doctrine:migration:execute 'App\\Migrations\\Version20230529175045' --up|--down"` - накатить конкретную миграцию

`make back_exec "bin/console doctrine:migration:migrate prev"` - откатить последнюю миграцию
