# Changelog

## [master] - present

### New feature
- [tndt-11](http://tasks.demius.ru/p/tndt-11) - Система активности работы над задачей.
#### Minor features
- [tndt-121](http://tasks.demius.ru/p/tndt-121) - Отображать на дашборде проекта когда он создан и обновлен
### Refactoring
- [tndt-94](http://tasks.demius.ru/p/tndt-94), [tndt-26](http://tasks.demius.ru/p/tndt-26) - Обновление системы до php8, Symfony6.4
- [tndt-42](http://tasks.demius.ru/p/tndt-42), [tndt-104](http://tasks.demius.ru/p/tndt-104) - Перераспределить файлы конфигурации, расположение моделей, сервисов. (теперь все конфиги одного окружения в одной папке, и их легче подменять для каждого инстанса)
- [tndt-108](http://tasks.demius.ru/p/tndt-108) - Рефакторинг TaskSettings и получения справочников из проекта
- [tndt-109](http://tasks.demius.ru/p/tndt-109) - Комментарии прицеплять к типу, а не классу
### Bugfixes
- [tndt-115](http://tasks.demius.ru/p/tndt-115) - Смена статуса документа не обновялет его дату
- [tndt-30](http://tasks.demius.ru/p/tndt-30) - Обновлять дату проекта, когда в нем совершается работа

## [v0.2.2] - 2023-05-07

### New feature
- [tndt-25](http://tasks.demius.ru/p/tndt-25) - Гибкое описание времени
- [tndt-89](http://tasks.demius.ru/p/tndt-89) - Переход с этапа на этап на странице задачи
- [tndt-90](http://tasks.demius.ru/p/tndt-90) - Списки. номера задач/документов по правому краю
### Refactoring
- [tndt-96](http://tasks.demius.ru/p/tndt-96) - Добавить в проект Спецификации и перевести на них репозитории
- [tndt-98](http://tasks.demius.ru/p/tndt-98) - Разобраться с User->getRoles()
- [tndt-99](http://tasks.demius.ru/p/tndt-99) - Разделить UserController и пользовательский и менеджерский и 
### Bugfixes
- [tndt-83](http://tasks.demius.ru/p/tndt-83) - Закрытие задачи без комментария, не обновляет её дату
- [tndt-98](http://tasks.demius.ru/p/tndt-98) - Разобраться с User->getRoles()
- [tndt-106](http://tasks.demius.ru/p/tndt-106) - Временные зоны проекта. 

## [v0.2.1.1] - 2023-04-06

### Bugfixes
- **hotfix** - [tndt-92](http://tasks.demius.ru/p/tndt-92) Шторка со справкой md не подгружалась в продовый контейнер
- [tndt-93](http://tasks.demius.ru/p/tndt-93) Обновил пакеты composer, npm

## [v0.2.1] - 2023-04-02

### New feature
- [tndt-8](http://tasks.demius.ru/p/tndt-8) - Шторка с кратким описанием правил написания markdown
- [tndt-55](http://tasks.demius.ru/p/tndt-55) - Баджи на задачах, документах, пользователях
- [tndt-71](http://tasks.demius.ru/p/tndt-71) - Флаг deprecated в документах
### Minor features
- [tndt-58](http://tasks.demius.ru/p/tndt-58) - На вкладке дашборда показывать, что есть еще проекты
### Bugfixes
- [tndt-74](http://tasks.demius.ru/p/tndt-74) - Ошибка при создании/редактировании описания проекта.
- [tndt-69](http://tasks.demius.ru/p/tndt-69) - В продовых логах появился debug
- [tndt-20](http://tasks.demius.ru/p/tndt-20) - Закрытую задачу нельзя вновь закрыть

## [v0.2.0] - 2022-01-15

### New feature
- [tndt-3](http://tasks.demius.ru/p/tndt-3) - Кастомные страницы ошибок с выводом основной причины ошибки пользователю
- [tndt-4](http://tasks.demius.ru/p/tndt-4) - Система справочников и справочник тип задачи
- [tndt-5](http://tasks.demius.ru/p/tndt-5) - Справочник этап задачи
- [tndt-13](http://tasks.demius.ru/p/tndt-13) - Справочник сложность задачи
- [tndt-15](http://tasks.demius.ru/p/tndt-15) - Справочник приоритет задачи
- [tndt-33](http://tasks.demius.ru/p/tndt-33) - Перевернуть список комментариев, чтобы самые новые были вверху
- [tndt-48](http://tasks.demius.ru/p/tndt-48) - Команда назначения дефолтных значений справочников
### Bugfixes
- [tndt-29](http://tasks.demius.ru/p/tndt-29) - root не видит приватных проектов
- [tndt-36](http://tasks.demius.ru/p/tndt-36) - Не отображать Панель управления если в ней нет кнопок.


## [v0.1.2.1] - 2021-11-20

### Bugfixes
- hotfix - Так быстро переделывали систему комментариев, что не везде поменяли местами комментируемый объект и форму комментария. В итоге при закрытии задачи с комментарием закрытия, система валилась неправильно вызывая функцию добавления комментария.


## [v0.1.2] - 2021-11-15

### New feature 
- [tndt-21](http://tasks.demius.ru/p/tndt-21) - Комментарии поддерживают markdown
- [tndt-28](http://tasks.demius.ru/p/tndt-28) - Улучшение дашборда. Не отображаем root, показываем скрытые, но доступные пользователю проекты, увеличиваем количество задач и документов.

### Bugfixes
- [tndt-22](http://tasks.demius.ru/p/tndt-22) - Не даем писать комментарии неавторизованным пользователям.
- [tndt-23](http://tasks.demius.ru/p/tndt-23) - Ошибка при открытии редактирования профиля.
- [tndt-24](http://tasks.demius.ru/p/tndt-24) - При создании задачи асайнером становится не создающий, а выбранный пользователь проекта
- [tndt-31](http://tasks.demius.ru/p/tndt-31) - В виджете проекта исправлена ссылка на проект. Скрытые проекты видно тем, кому они доступны.


## [v0.1.1] - 2021-11-10

- [tndt-7](http://tasks.demius.ru/p/tndt-7) - Расширяем размеры полей в формах, ибо в описании задачи уперлись в него
- [tndt-12](http://tasks.demius.ru/p/tndt-12) - Базовая простая система комментариев, чтобы так же можно было точнее описать процесс проработки задачи.


## [v0.1.0] - 2021-11-08

- [tndt-1](http://tasks.demius.ru/p/tndt-1) - Minimal Viable Product