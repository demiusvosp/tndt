## Заголовки
Заголовки первого и второго уровней, выполненные с помощью подчеркивания, выглядят следующим образом:

    Заголовок первого уровня
    ========================
    Заголовок второго уровня
    -------------------------
Заголовки первого, третьего и шестого уровней, выполненные с помощью символа («#»), выглядят следующим образом:

    #  Заголовок первого уровня
    ### Заголовок третьего уровня
    ###### Заголовок шестого уровня


## Горизонтальные линии (разделители)

Для того чтобы создать горизонтальную линию с использованием синтаксиса языка Markdown, необходимо поместить три (или более)дефиса или звездочки на отдельной строке текста. Между ними возможно располагать пробелы.
Горизонтальные линии в Markdown выглядят следующим образом:

    Первая часть текста, который необходимо разделить
    ***
    Вторая часть текста, который необходимо разделить

Или

    Первая часть текста, который необходимо разделить

    ---

    Вторая часть текста, который необходимо разделить
В результате на экран выводится следующее:

Первая часть текста, который необходимо разделить
***
Вторая часть текста, который необходимо разделить

При использовании данного инструмента важно помнить, что после первой части текста и перед второй необходимо оставлять пустую строку. Данное правило необходимо соблюдать только при использовании дефисов. Если его не соблюдать, на экран будет выведен заголовок второго уровня и строка обычного текста.  При использовании символа звездочки данным правилом можно пренебречь.



## Цитаты
Для обозначения цитат в языке Markdown используется знак «больше» («>»). Его можно вставлять как перед каждой строкой цитаты, так и только перед первой строкой параграфа.
Также синтаксис Markdown позволяет создавать вложенные цитаты (цитаты внутри цитат). Для их разметки используются дополнительные уровни знаков цитирования («>»).
Цитаты в Markdown могут содержать всевозможные элементы разметки.
Цитаты в языке Markdown выглядят следующим образом:

    >Это пример цитаты,
    >в которой перед каждой строкой
    >ставится угловая скобка.

    >Это пример цитаты,
    в которой угловая скобка
    ставится только перед началом нового параграфа.
    >Второй параграф.

Вложение цитаты в цитату выглядит следующим образом:

    > Первый уровень цитирования
    >> Второй уровень цитирования
    >>> Третий уровень цитирования
    >
    >Первый уровень цитирования
В результате на экран выводится следующее:

>Это пример цитаты,
>в которой перед каждой строкой
>ставится угловая скобка.

>Это пример цитаты,
в которой угловая скобка
ставится только перед началом нового параграфа.

>Второй параграф.

Вложенная цитата:

> Первый уровень цитирования
>> Второй уровень цитирования
>>> Третий уровень цитирования
>
>Первый уровень цитирования

