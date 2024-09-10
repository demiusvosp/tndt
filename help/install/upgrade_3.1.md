
### Новый env-параметр APP_HOST
Указывает на домен на котором развернута система, он будет добавляться к ссылкам, по нему будет определяться какие 
ссылки являются внутренними
  Например:
```
  environment:
    APP_HOST: tasks.demius.ru
    
```

### Вывод логов в graylog

По умолчанию логи выводятся в `stderr`, благодаря чем их можно читать через `docker log`, или же использовать свои 
системы сбора логов в контейнеров.

ВЫвод логов через gelf позволит получить более подробную информацию а так же эффективнее по ней искать.
Для подключения заведены новые env-переменные GRAYLOG_HOST и GRAYLOG_PORT, их можно указать их в docker compose файле
```yaml
    environment:
      GRAYLOG_HOST: logs.develop.int
      GRAYLOG_PORT: 12201 # порт по умолчанию можно не указывать
```

Так же нужно скопировать директорию config/prod отдельно, отредактировать там файл конфигурации 
`config/prod/packages/monolog.yaml`
```yaml
monolog:
  handlers:
    main:
      type: fingers_crossed
      action_level: graylog
      handler: nested
      excluded_http_codes: [404, 405]
    graylog:
      type: gelf
      publisher:
        hostname: '%env(GRAYLOG_HOST)%'
        port: '%env(GRAYLOG_PORT)%'
      level: info
```
Для отправки в graylog нужно установить handler в graylog. Так же там можно настроить, уровень лога, который 
отправляется, исключить какие-то из каналов. Или даже задать несколько разных хандлеров для разных каналов или уровней, 
отправляющий в разные хосты graylog

После чего подмонтировать её в контейнер
```yaml
    volumes:
      - '<your path to config>:/app/config/prod:ro'
```
