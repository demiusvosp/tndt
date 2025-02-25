## Списки
Markdown поддерживает упорядоченные (нумерованные) и неупорядоченные (ненумерованные) списки.
Для формирования неупорядоченный списков используются такие маркеры, как звездочки, плюсы и дефисы. Все перечисленные
маркеры могут использоваться взаимозаменяемо. Для формирования упорядоченных списков в качестве маркеров используются
числа с точкой. Важной особенностью в данном случае является то, что сами номера, с помощью которых формируется список,
не важны, так как они не оказывают влияния на выходной HTML код. Как бы ни нумеровал пользователь список, на выходе он
в любом случае будет иметь упорядоченный список, начинающийся с единицы (1, 2, 3…). Эту особенность стоит учитывать в
том случае, когда необходимо использовать порядковые номера элементов в списке, чтобы они соответствовали номерам,
получающимся в HTML. Упорядоченные списки всегда следует начинать с единицы. Маркеры списков обычно начинаются с
начала строки, однако они могут быть сдвинуты, но не более чем на 3 пробела. За маркером должен следовать пробел,
либо символ табуляции. При  необходимости в список можно вставить цитату. В этом случае обозначения цитирования
( «>» ) нужно писать с отступом.

Упорядоченные списки выглядят следующим образом:

    1.	Проводник
    2.	Полупроводник
    3.	Диэлектрик

Неупорядоченные списки выглядят следующим образом:

    * Проводник
    * Полупроводник
    * Диэлектрик

Или

    - Проводник
    - Полупроводник
    - Диэлектрик

Или

    + Проводник
    + Полупроводник
    + Диэлектрик
На выходе всех трех перечисленных вариантов имеется один и тот же результат.
В результате на экран выводится следующее:

1. Проводник
2. Полупроводник
3. Диэлектрик

и

+ Проводник
+ Полупроводник
+ Диэлектрик

Цитата, вставленная в список, выглядит следующим образом:

    1. Элемент списка с цитатой:

        > Это цитата
        > внутри элемента списка.

     2. Второй элемент списка

В результате на экран выводится следующее:

1. Элемент списка с цитатой:

   > Это цитата
   > внутри элемента списка.

2. Второй элемент списка
