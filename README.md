<p align="center"><img src="https://res.cloudinary.com/dtfbvvkyp/image/upload/v1566331377/laravel-logolockup-cmyk-red.svg" width="400"></p>

## Розыгрыш призов

Для запуска проекта следует выполнить следующие действия:

- Скачать библиотеки php командой `composer install`
- Создать базу данных MySQL `prize`.
- Скопировать файл `.env.example` и создать его с именем  `.env`
- Указать настройки для работы с базой данных в файле `.env` `DB_DATABASE=laravel DB_USERNAME=root DB_PASSWORD=`
- Выполнить команду по созданию приватного ключа `php artisan  key:generate`
- Выполнить команду для наката миграции `php artisan migrate:fresh`
- Выполнить команду для наката тестовых данных  `artisan db:seed`
- Выполнить команду запуска воркера для работы приложения и   `php artisan queue:work`
- Для отправки денег   `php artisan money:send` Адрес отправки указана в файле `.env`  параметр `URL_WEB_HOOK`. 
Стандартный вариант можно проверить `https://webhook.site/#!/0ad5899d-cac7-4685-bf49-9b299e29d06f/aa0a3f2b-20ae-4a97-a7bd-f1af8b800c05/1`
**В приложении не учитывается получение денег из других источников.**
- Запус тестов `./prize/vendor/phpunit/phpunit/phpunit --configuration ./prize/phpunit.xml Tests\\Unit\\RaffleResultServicesTest`
