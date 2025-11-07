История изменений
============================


## [0.3.2.1] - 2025.11.07
- [tndt-223](http://tasks.demiusvosp.ru/p/tndt-223) - Поменять ссылки на demius.ru в связи с уступкой домена demius.ru

## [0.3.2] - 2025.01.23
- **New features**
  - [tndt-19](http://tasks.demiusvosp.ru/p/tndt-19) - Фильтрация и сортировка списка задач
  - [tndt-46](http://tasks.demiusvosp.ru/p/tndt-46) - Отдельные роли работы менеджмента проектов и пользователей
- **Refactoring**
  - [tndt-72](http://tasks.demiusvosp.ru/p/tndt-72) - Индексы на таблицу task
  - [tndt-161](http://tasks.demiusvosp.ru/p/tndt-161) - Обновить фронт до Vue3
  - [tndt-169](http://tasks.demiusvosp.ru/p/tndt-169) - Структурировали документацию и вынесли историю
- **Bugfixes**
  - [tndt-184](http://tasks.demiusvosp.ru/p/tndt-184) - Страница создания пользователя
  - [tndt-185](http://tasks.demiusvosp.ru/p/tndt-185) - Закрытие задачи не обновляет время проекта
----

## [0.3.1] - 2024.11.10
- **New features**
    - [tndt-9](http://tasks.demiusvosp.ru/p/tndt-9) - wiki-разметка. Этап 0. Элементарная система гиперссылок
    - [tndt-153](http://tasks.demiusvosp.ru/p/tndt-153) - Страница общей статистики системы
    - [tndt-167](http://tasks.demiusvosp.ru/p/tndt-167) - Отправка логов через GELF
- **minor features**
    - [tndt-40](http://tasks.demiusvosp.ru/p/tndt-40) - Базовый профайлинг своих сервисов
    - [tndt-66](http://tasks.demiusvosp.ru/p/tndt-66) - Экранирование html - сущностей в md полях
    - [tndt-174](http://tasks.demiusvosp.ru/p/tndt-174) - Кнопки создать задачу, создать документ в сайдбаре лучше заметны
- **Refactoring**
    - [tndt-160](http://tasks.demiusvosp.ru/p/tndt-160) - Обновить docker-compose and node container
    - [tndt-163](http://tasks.demiusvosp.ru/p/tndt-163) - В списке документов вобще нет обозначения статуса.
- **Bugfixes**
    - [tndt-151](http://tasks.demiusvosp.ru/p/tndt-151) - Теперь slug документов уникален в пределах проекта
    - [tndt-168](http://tasks.demiusvosp.ru/p/tndt-168) - tabler-icons.svg переехал в другой пакет
    - [tndt-179](http://tasks.demiusvosp.ru/p/tndt-179) - Закрытие задачи не обновляет её время
- **Замечания к обновлению в [upgrade_3.1.md](upgrade_3.1.md)**


## [0.3] - 2024.03.07
- **New feature**
    - **Major features**
        - [tndt-11](http://tasks.demiusvosp.ru/p/tndt-11) - Система активности работы над задачей.
        - **[tndt-143](http://tasks.demiusvosp.ru/p/tndt-143)**, [tndt-141](http://tasks.demiusvosp.ru/p/tndt-141), [tndt-146](http://tasks.demiusvosp.ru/p/tndt-146), [tndt-147](http://tasks.demiusvosp.ru/p/tndt-147), [tndt-149](http://tasks.demiusvosp.ru/p/tndt-149) - Переход от AdminLTE на Tabler. Переписывание почти всей верстки и больше половины стилей
    - **Minor features**
        - [tndt-121](http://tasks.demiusvosp.ru/p/tndt-121) - Отображать на дашборде проекта когда он создан и обновлен
        - [tndt-123](http://tasks.demiusvosp.ru/p/tndt-123), [tndt-125](http://tasks.demiusvosp.ru/p/tndt-125) - Списки задач и документов на главной странице
        - [tndt-150](http://tasks.demiusvosp.ru/p/tndt-150) - Иконки в виде SVG-спрайтов. На выбор tabler-icons, самонарисованные, и да, font-awesome тоже доступен
- **Refactoring**
    - [tndt-26](http://tasks.demiusvosp.ru/p/tndt-26), [tndt-94](http://tasks.demiusvosp.ru/p/tndt-94) - Обновление системы до php8, Symfony6.4
    - [tndt-39](http://tasks.demiusvosp.ru/p/tndt-39) - Получение главной сущности страницы через MapEntity, и использование её через Request
    - [tndt-42](http://tasks.demiusvosp.ru/p/tndt-42), [tndt-104](http://tasks.demiusvosp.ru/p/tndt-104) - Перераспределить файлы конфигурации, расположение моделей, сервисов. (теперь все конфиги одного окружения в одной папке, и их легче подменять для каждого инстанса)
    - [tndt-102](http://tasks.demiusvosp.ru/p/tndt-102), [tndt-112](http://tasks.demiusvosp.ru/p/tndt-112) - Адаптивность под небольшой ноут и под мобилку
    - [tndt-108](http://tasks.demiusvosp.ru/p/tndt-108) - Рефакторинг TaskSettings и получения справочников из проекта
    - [tndt-109](http://tasks.demiusvosp.ru/p/tndt-109) - Комментарии прицеплять к типу, а не классу
- **Bugfixes**
    - [tndt-30](http://tasks.demiusvosp.ru/p/tndt-30), [tndt-134](http://tasks.demiusvosp.ru/p/tndt-134) - Обновлять дату проекта, когда в нем совершается работа
    - [tndt-35](http://tasks.demiusvosp.ru/p/tndt-35) - В sidebar пункты вылезают за границу панели
    - [tndt-91](http://tasks.demiusvosp.ru/p/tndt-91) - У баджа secondary не отображается фон.
    - [tndt-57](http://tasks.demiusvosp.ru/p/tndt-57), [tndt-115](http://tasks.demiusvosp.ru/p/tndt-115) - Бизнес-евенты теперь обновляют или не обновляют даты когда нужно


## [v0.2.2] - 2023-05-07
- **New feature**
    - [tndt-25](http://tasks.demiusvosp.ru/p/tndt-25) - Гибкое описание времени
    - [tndt-89](http://tasks.demiusvosp.ru/p/tndt-89) - Переход с этапа на этап на странице задачи
    - [tndt-90](http://tasks.demiusvosp.ru/p/tndt-90) - Списки. номера задач/документов по правому краю
- **Refactoring**
    - [tndt-96](http://tasks.demiusvosp.ru/p/tndt-96) - Добавить в проект Спецификации и перевести на них репозитории
    - [tndt-98](http://tasks.demiusvosp.ru/p/tndt-98) - Разобраться с User->getRoles()
    - [tndt-99](http://tasks.demiusvosp.ru/p/tndt-99) - Разделить UserController и пользовательский и менеджерский и
- **Bugfixes**
    - [tndt-83](http://tasks.demiusvosp.ru/p/tndt-83) - Закрытие задачи без комментария, не обновляет её дату
    - [tndt-98](http://tasks.demiusvosp.ru/p/tndt-98) - Разобраться с User->getRoles()
    - [tndt-106](http://tasks.demiusvosp.ru/p/tndt-106) - Временные зоны проекта.


## [v0.2.1.1] - 2023-04-06
- **Bugfixes**
    - **hotfix** - [tndt-92](http://tasks.demiusvosp.ru/p/tndt-92) Шторка со справкой md не подгружалась в продовый контейнер
    - [tndt-93](http://tasks.demiusvosp.ru/p/tndt-93) Обновил пакеты composer, npm


## [v0.2.1] - 2023-04-02
- **New feature**
    - [tndt-8](http://tasks.demiusvosp.ru/p/tndt-8) - Шторка с кратким описанием правил написания markdown
    - [tndt-55](http://tasks.demiusvosp.ru/p/tndt-55) - Баджи на задачах, документах, пользователях
    - [tndt-71](http://tasks.demiusvosp.ru/p/tndt-71) - Флаг deprecated в документах
- **Minor features**
    - [tndt-58](http://tasks.demiusvosp.ru/p/tndt-58) - На вкладке дашборда показывать, что есть еще проекты
- **Bugfixes**
    - [tndt-74](http://tasks.demiusvosp.ru/p/tndt-74) - Ошибка при создании/редактировании описания проекта.
    - [tndt-69](http://tasks.demiusvosp.ru/p/tndt-69) - В продовых логах появился debug
    - [tndt-20](http://tasks.demiusvosp.ru/p/tndt-20) - Закрытую задачу нельзя вновь закрыть


## [v0.2.0] - 2022-01-15
- **New feature**
    - [tndt-3](http://tasks.demiusvosp.ru/p/tndt-3) - Кастомные страницы ошибок с выводом основной причины ошибки пользователю
    - [tndt-4](http://tasks.demiusvosp.ru/p/tndt-4) - Система справочников и справочник тип задачи
    - [tndt-5](http://tasks.demiusvosp.ru/p/tndt-5) - Справочник этап задачи
    - [tndt-13](http://tasks.demiusvosp.ru/p/tndt-13) - Справочник сложность задачи
    - [tndt-15](http://tasks.demiusvosp.ru/p/tndt-15) - Справочник приоритет задачи
    - [tndt-33](http://tasks.demiusvosp.ru/p/tndt-33) - Перевернуть список комментариев, чтобы самые новые были вверху
    - [tndt-48](http://tasks.demiusvosp.ru/p/tndt-48) - Команда назначения дефолтных значений справочников
- **Bugfixes**
    - [tndt-29](http://tasks.demiusvosp.ru/p/tndt-29) - root не видит приватных проектов
    - [tndt-36](http://tasks.demiusvosp.ru/p/tndt-36) - Не отображать Панель управления если в ней нет кнопок.


## [v0.1.2.1] - 2021-11-20
- **Bugfixes**
    - hotfix - Так быстро переделывали систему комментариев, что не везде поменяли местами комментируемый объект и форму комментария. В итоге при закрытии задачи с комментарием закрытия, система валилась неправильно вызывая функцию добавления комментария.


## [v0.1.2] - 2021-11-15
- **New feature**
    - [tndt-21](http://tasks.demiusvosp.ru/p/tndt-21) - Комментарии поддерживают markdown
    - [tndt-28](http://tasks.demiusvosp.ru/p/tndt-28) - Улучшение дашборда.
- **Bugfixes**
    - [tndt-22](http://tasks.demiusvosp.ru/p/tndt-22) - Не даем писать комментарии неавторизованным пользователям.
    - [tndt-23](http://tasks.demiusvosp.ru/p/tndt-23) - Ошибка при открытии редактирования профиля.
    - [tndt-24](http://tasks.demiusvosp.ru/p/tndt-24) - При создании задачи асайнером становится не создающий, а выбранный пользователь проекта
    - [tndt-31](http://tasks.demiusvosp.ru/p/tndt-31) - В виджете проекта исправлена ссылка на проект. Скрытые проекты видно тем, кому они доступны.


## [v0.1.1] - 2021-11-10
- **New feature**
- [tndt-12](http://tasks.demiusvosp.ru/p/tndt-12) - Базовая простая система комментариев, чтобы так же можно было точнее описать процесс проработки задачи.
- [tndt-7](http://tasks.demiusvosp.ru/p/tndt-7) - Расширяем размеры полей в формах, ибо в описании задачи уперлись в него


## [v0.1.0] - 2021-11-08
- [tndt-1](http://tasks.demiusvosp.ru/p/tndt-1) - Minimal Viable Product