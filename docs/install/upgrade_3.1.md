
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
системы сбора логов с контейнеров.

Вывод логов через gelf позволит получить более подробную информацию, а так же эффективнее по ней искать.
Для подключения заведены новые env-переменные GRAYLOG_HOST и GRAYLOG_PORT, их можно указать их в docker compose файле
```yaml
    environment:
      GRAYLOG_HOST: logs.develop.int
      GRAYLOG_PORT: 12201 # порт по умолчанию можно не указывать
```
Для того чтобы отделить логи системы от логов других систем, они маркируются атрибутом 'service' с значением равным установленному `APP_HOST`

Так же нужно скопировать директорию config/prod отдельно, отредактировать там файл конфигурации 
`config/prod/packages/monolog.yaml`
```yaml
monolog:
  handlers:
    main:
      type: fingers_crossed
      action_level: error
      handler: graylog
      excluded_http_codes: [404, 405]
    graylog:
      type: gelf
      publisher: { id: 'gelf.publisher' }
      formatter: 'monolog.formatter.gelf_message'
      channels: ['!event']
      level: info
```
Для отправки в graylog нужно установить значение handler: graylog. Так же там можно настроить, уровень лога, который 
отправляется, исключить какие-то из каналов. Или даже задать несколько разных хэндлеров для разных каналов или уровней, 
отправляющий в разные хосты graylog

После чего подмонтировать её в контейнер
```yaml
    volumes:
      - '<your path to config>:/app/config/prod:ro'
```

Опционально можно переопределить атрибут в который записывается информация о сервисе. По умолчанию это атрибут 'service'
и в него кладется значение переменной `APP_HOST`. 
```yaml
  environment:
      GRAYLOG_TAG: 'tndt:php'
      GRAYLOG_TAGNAME: 'tag'
```
после чего к записям лога будет добавляться атрибут 'tag' со значением 'tndt:php', это позволит использовать настроенные
ранее правила распределения логов по streams.

