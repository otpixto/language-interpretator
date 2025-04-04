Интерпретатор функционального языка

Данный функционал получает на вход строку на функциональном языке со следующим синтаксисом:

<программа> ::= <выражение>

<выражение> ::= <вызов_функции> | <константа>

<вызов_функции> ::= '(' <имя_функции> ')' | '(' <имя_функции> ',' <параметры_функции> ')'

<имя_функции> ::= символы

<параметры_функции> ::= <выражение> | <выражение> ',' <параметры_функции>

<константа> ::= 'true' | 'false' | 'null' | <строка> | <число>

<строка> ::= '""' | '"' символы '"'

<число> ::= <целое_число> | <вещественное_число>

<целое_число> ::= цифра | цифра <целое_число>

<вещественное_число> ::= <целое_число> '.' <целое_число>

Далее строка преобразуется в массив с типом данных и значением, затем данные интерпретируются и выдается результат.

Пример: если на вход приходит строка
(json,
    (map,
        (array, "message"),
            (array,
                (concat, "Hello, ",
                (getArg, 0)
            )
        )
    )
)

, то на выходе будет
{"message":"Hello, world"}

Также предусмотрено добавление новых функций.

Пример запуска:
php run.php world

