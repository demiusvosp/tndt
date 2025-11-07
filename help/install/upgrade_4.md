Обновление до версии 4
============================

### Redis
В системе появляется новый сервис - Redis. Это не просто кеш, хотя и близко к нему, в нем система хранит
 - wiki Связи между ссылками и сущностями
 - statistic Предпосчитанную статистику

Хотя все лежащее в redis система может пересчитать по основному хранилищу, весьма рекомендуется хранить данные redis в 
отдельном томе. Для чего в docker-compose файл необходимо добавить из шаблона compose :
```yaml
  redis:
    image: redis:7-alpine
    volumes:
      - '<path to service config>:/usr/local/etc/redis/redis.conf:ro'
      - 'redis:/data:rw'

  volumes:
    storage:
    redis:
```
В `<path to service config>` необходимо скопировать файл `./docker/redis.conf` и, по необходимости настроить под себя.

Так же рекомендуется делать бекап redis-volume рядом с storage volume

Если имя сервиса или путь до него отличается от указанного (например используется внешний redisCluster, подключить redis
к бекенду можно через env `REDIS_URL`
```yaml
    environment:
      REDIS_URL: 'redis'
    links:
      - mysql
      - redis
```
