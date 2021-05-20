Утсановить php7.4, php-pgsql, postgres (role: postgres, password: password)
Запустить скрипт install.sh

Запуск api
php -S localhost:8000

Пример запросов.
Создание категории. POST запрос
http://localhost:8000/web.php?action=categories

УДаление категории. DELETE запрос
http://localhost:8000/web.php?action=categories/8

Просмотр товаров по категории 2. GET запрос
http://localhost:8000/web.php?action=categories/2/products

Расчет стоимости доставки. GET запрос
http://localhost:8000/web.php?action=orders/1
