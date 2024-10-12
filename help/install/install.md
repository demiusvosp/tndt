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

Это все просто выкопировано из README и неактуально, будет переписано когда будем планировать какой-то инсталлятор