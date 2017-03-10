# ratecb

## Курсы ЦБ

В тестовом приложении "Курсы ЦБ" использована библиотека
[sergechurkin/cform](https://github.com/sergechurkin/cform),
которая подключена как расширение. При установке с помощью
[composer](http://getcomposer.org/download/)
формируется ватозагрузчик библиотеки. Приложение зарегистрировано на
[packagist](https://packagist.org/packages/sergechurkin/ratecb).

## Описание

Приложение обращается к [WEB сервису ЦБ](http://www.cbr.ru/scripts/Root.asp?PrtId=DWS)
и скачивает таблицу курсов за заданную дату.

## Установка

```
composer create-project sergechurkin/ratecb  path "1.1.x-dev"
```

Запустить приложение можно [по ссылке](http://sergechurkin.vacau.com/).
