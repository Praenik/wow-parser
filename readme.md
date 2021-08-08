# Парсер гильдий wowprogress.com и raider.io

Настройка и запуск
---------
1. Скопировать файл `.env.example` в `.env` вписать свои настройки
```dotenv
DB_NAME=parser
DB_USER=parser
DB_HOST=mysql
DB_PORT=3306
DB_PASS=1234
DB_ROOT_PASS=1234
DB_DRIVER=pdo_mysql
```
2. Скопировать файл `guilds.php.example` в `guilds.php`, вписать нужные гильдии:
```php
<?php

return [
    'Гильдия 1',
    'Гильдия 2',
];
```
3. Перейти в директорию проекта и выполнить последовательность команд:
```bash
composer install
php vendor/bin/doctrine migrate
php parse.php
# или
php generate.php
```

Запуск с использованием docker'а
----------
Необходимо выполнить следующие команды
```bash
docker-compose up -d
docker-compose exec app composer install
docker-compose exec app php vendor/bin/doctrine migrate
docker-compose exec app php parse.php
```