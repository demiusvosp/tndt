Tasks and Docs tracker
============================

CRM менеджмента задач и документов, максимально гибкий, позволяющий вести, как программные проекты, так и далекие от 
программирования. Разрабатывается итерационным методом, т.е. на прод выкатывается минимально рабочая версия, даже при 
условии, что она не имеет никаких преимуществ перед существующими системами. Развивается преимущественно в сторону тех 
фич, которых мне наиболее не хватает. На данный момент является скорее лабораторией по использованию фреймворка symfony4
и архитектурных подходов к реализации системы, нежели реально полезной на практике системой. 

### Цели
Есть множество хороших систем документирования, как-то confluence или mediawiki. Но главная проблема в документации, 
поддержание её в актуальном состоянии, и когда она находится в отдельном сервисе про неё частенько забывают. Объединяя 
её с таск-трекером, появляется возможность обращаться к ней из задач, создавать страницы из описания задач (что хуже 
отдельного специального документа, но лучше полного отсутствия документации), это позволяет делать простые и быстрые 
кросс-линки между задачами и документами. Что позволяет, сначала по git-коммиту быстро найти задачу, а по задаче найти 
документацию (которую можно будет привязать к задаче, эпику, категории, тегу).
Вторая причина появления этого проекта, желание собрать в одном месте все полюбившиеся фичи и особенности, которые 
может быть и не всегда необходимы, но без них в других такс-треккеров мне чего-то да не хватает. Например:
* Нумерация задач в пределах проекта. Это не очень важно, но приятно видеть по номеру задачи объем работ выполненных
  (или запланированных) по данному проекту, а не по всем в системе
* Возможность легко ссылаться на задачу через [abc-123], что есть не везде (в прославленной Jira, например приходится 
вручную копировать url), т.е. полноценную wiki-систему подталкивающую к использованию и заполнению документации.
* Возможность указать прогресс выполнения (как в Flyspray) Не самая нужная вещь в коммерческой разработки, но мне 
 приятно отмечать прогресс сложной задачи, не по реально выполненному, а вручную указав 50 или 80%, даже если они пошли 
не на реализацию кода.
* Гибкие настройки проектов. Мне хотелось бы в одном треккере иметь как програмные проекты, с версиями, приоритетами и 
 другими свойствами задачи, и рядом бытовые (вроде работ по дому или даче)

REQUIREMENTS
------------

* PHP >=7.4
* MySQL like db
предпочтительный способ деплоя через docker-контейнеры. 


INSTALLATION
------------
`<ENVIRONMENT>` - prod, dev or test.

1. Unpack archive, or git clone
2. `make up env=<ENVIRONMENT>`
3. `make init`
4. `make build_front`
5. check write permission for ./var directory

Особенности обслуживания системы
-------

###База данных
Для корректной работы с текстами на национальных языках, в частности с русским необходимо проверить, что созданная БД 
имеет сравнение utf8mb4_general_ci, таблица же migrations для поддержки длинных названий миграций должна иметь 
сравнение ascii_general_ci в ней никогда не будет русских символов. В будущем при схлопывании миграций будут убраны 
длинное название миграции создающей root пользователя и данное требование станет неактуальным. 

###Директория var
Необходимо следить, чтобы поддиректории ./var были доступны для записи. Теоретически они создаются системой 
автоматически с нужными полномочиями, но при запуске команд в контейнере от root, при работе php-fpm от другого 
пользователя часть кеш, создаваемый `./bin/console cache:warmup` может быть не от того пользователя. Необходимо либо 
вызывать команды от того же пользователя системы, под которым работает php-fpm, либо после вызова этих команд делать 
`chown www-data:www-data -R ./var/`


TESTING
-------

1. `make up env=test`

Unit tests: `make tests type=unit`

Functional application tests: `make tests type=behat`

