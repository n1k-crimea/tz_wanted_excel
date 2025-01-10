<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

### Laravel тестовое задание

Создать laravel-проект в git-репозитории (подойдет любой публичный сервис, например github). Первым коммитом залить чистый фреймворк, следом — реализацию задания.

- Реализовать контроллер с валидацией и загрузкой excel файла (xlsx).
- Доступ к контроллеру загрузки закрыть basic-авторизацией.
- Поля excel:
  - id
  - name
  - date (d.m.Y)

- Загруженный файл через jobs поэтапно (по 1000 строк) парсить в БД (таблица rows). Шапку файла (названия столбцов) пропустить. Разные строки с одинаковым id не заменять, оставлять строку, импортированную первой.
- Реализовать базовую валидацию данных из excel: id - unsigned big integer, name - строчные и прописные буквы английского алфавита и пробел, date - дата в формате d.m.Y (дата должна существовать). Строки excel, не прошедшие валидацию, пропускать.
  В конце импорта вывести в текстовый файл все сообщения о ошибках валидации строк и возможных дубликатах id, в виде:
  <номер строки> - <ошибка1>, <ошибка2>, …
  и запушить сгенерированный файл result.txt с отчётом о ошибках в репозиторий с тестовым заданием, отдельным коммитом.

- Прогресс парсинга файла хранить в redis (уникальный ключ + количество обработанных строк).
- Для парсинга excel можете использовать любой пакет composer, но процесс парсинга через jobs необходимо реализовать самостоятельно.
- Реализовать контроллер для вывода импортированных данных (rows) с группировкой по date - двумерный массив.
- Будет плюсом если вы реализуете через laravel echo передачу event-а на создание записи в rows
- Будет плюсом написание тестов

Файл для импорта в xlsx формате можно скачать здесь: https://docs.google.com/spreadsheets/d/1ocBRhoGkzeKRHgepf07V0UCUAon2NFPl6UwMzeoXK0E/edit?usp=sharing
