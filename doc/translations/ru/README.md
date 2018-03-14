> Продвигается Кошкой!!!

# Настройка среды разработки

Необходимо настроить среду разработки для каждого разработчика на его личном компьютере и по крайней мере одну среду на удалённом сервере ("on server/pre build").

К примеру, 4 локальные среды для программистов, + 1 на удаленном сервере для тестирования, + 1 на удалённом сервере для практического использования бота. Или 2 среды программистов и 1 на удаленном сервере.  

## Технологии

Необходимо:  

* Debian дистрибутив Linux
* Git - распределённая система управления версиями
* NodeJS (Node.js) - программная платформа для использования JavaScript на сервере
* NodeJS модуль react-scripts
* Apache2 - ПО для веб-сервера
* Apache2 модуль curl
* Apache2 модуль mysql
* PHP 7.* - скриптовый язык
* PECL - репозиторий модулей для PHP
* Composer - пакетный менеджер для PHP
* Doctrine - набор PHP библиотек, позволяющий работать с даннными как с объектами
* CodeIgniter - PHP фреймворк
* CodeIgniter Rest Server - реализация сервера для CodeIgniter
* MySQL - реляционная система управления базами данных
* OpenSSL - криптографический пакет и инструмент работы с протоколами передачи данных

_Для всех сред понадобится `nginx` (для разработки и производства), так как в CodeIgniter есть баг с настройкой `base_url`, вследствие которого невозможно установить `homepage` для скриптов react-js в режиме `npm start` ._  

## @BotFather

Для **каждой среды** нужен свой бот Telegramm.  Мы его будем создавать с помощью BotFather. 

1. Найти аккаунт приложения @BotFather в Telegramm.  
2. Ввести:  /help
3. /newbot
4. Следовать инструкциям
5. Создать токен, и сохранить его 

Можете создать tokens.txt в корне проекта и сохранять там свои токены.  
Git это позволяет.

## Git

Сохраните клон проекта там, где он будет доступен для Apache2 и Nginx

## Composer

Зайдите в /path/to/project/backend и запустите оттуда `composer install`

## CodeIgniter

Запускать из корня проекта: `./installci.sh`

Настройте /ci/public/index.php  
Найдите и настройте следующее:  

```
$application_folder = '../../backend/application';
$system_path = '../../backend/vendor/codeigniter/framework/system';
```

Настройте /backend/application/config/config.php
Найдите и настройте следующее:  

```
$config['enable_hooks'] = TRUE;
```

## DB

## Настройка набора символов:

Найдите `/etc/mysql/mysql.conf.d/mysqld.cnf`

Отредактируйте:

```
[mysqld]
...
character-set-server= utf8mb4
collation-server=utf8mb4_general_ci
``` 

### Создать базу данных и пользователя:

```
mysql -u root -p
create database telegrammbot 
	DEFAULT CHARACTER SET utf8mb4 
	DEFAULT COLLATE utf8mb4_general_ci;
create user 'telegrammbot'@'localhost' identified by 'dbpassword';
grant all privileges on telegrammbot.* to 'telegrammbot'@'localhost';
flush privileges;
```

Вы можете использовать следующие команды для настройки наборов символов:

```
show variables like "character_set_database";
# -
SELECT character_set_name FROM information_schema.`COLUMNS` 
	WHERE table_schema = "telegrammbot"
	AND table_name = "posts"
	AND column_name = "text";
# -
SELECT default_character_set_name 
	FROM information_schema.SCHEMATA 
	WHERE schema_name = "telegrammbot";
# -
SELECT CCSA.character_set_name FROM information_schema.`TABLES` T,
	information_schema.`COLLATION_CHARACTER_SET_APPLICABILITY` CCSA
	WHERE CCSA.collation_name = T.table_collation
	AND T.table_schema = "telegrammbot"
	AND T.table_name = "posts";
#-
alter database telegrammbot 
	DEFAULT CHARACTER SET utf8mb4 
	DEFAULT COLLATE utf8mb4_general_ci;
# -
ALTER TABLE posts CONVERT TO CHARACTER SET utf8mb4;
```

Для настройки набора символов.

Можете использовать пользователя и пароль какой хотите, но они должны совпадать с хранимыми в DB/user  
creation and bot configuration

### Ошибка "Socket error"

Если возникает следующая ошибка, то используйте нижеследующий алгоритм:

```
SQLSTATE[HY000] [2002] No such file or directory
```

1. Запустите команду в mysql prompt: `show variables like '%sock%'`
2. Найдите php.ini с помощью: `php -i | grep php.ini`
3. Добавьте правильный путь к файлу соккета (socket file) в файл настроек `php.ini`:

```
pdo_mysql.default_socket=/var/run/mysqld/mysqld.sock
```

## Настройка бота

Создайте config.yml в каталоге /backend/ и запишите в него следующие строки:

```
token: "BOT_TOKEN"
db:
  user: "telegrammbot"
  password: "dbpassword"
  db: "telegrammbot"
  driver: "pdo_mysql"
```

Теперь про наиболее проблемную часть - установку драйвера. Сделайте установку драйвера php для подключения к sql, например, `pdo_msql` или любого другого.
строчка `<? echo phpinfo();` интегрированная во внешнюю страницу поможет вам в этом.

## Создание структуры данных БД:

### Создание структуры данных

Запустите на сервере:  
* Для создания: `vendor/bin/doctrine orm:schema-tool:create`  
* Для удаления: `vendor/bin/doctrine orm:schema-tool:drop`
* Для обновления: `vendor/bin/doctrine orm:schema-tool:update`

Нужно добавить `--dump-sql` чтобы запрос печатался или `--force` чтобы запрос исполнялся.  

### Как развернуть структуру данных, если она создавалась в scratch  

* Из `/backend/db/` запустите `mysql -u user -p`  
* Введите пароль

```
use dbname
source script.sql
```

Создание аккаунта аднимистратора:  

```
desc users;
insert into users(`login`, `password`) values ("username", "password");
select * from users;
```

_Выберите логин и пароль_ С этими учетными данными можно заходить с панели управления  
Admin-On-Rest.

# Администрирование NodeJS

Установите nodejs, и исправьте nodejs после установки на Ubuntu:

```
sudo ln -s /usr/bin/nodejs /usr/local/bin/node
```

Установите последнюю версию npm:

```
sudo npm install -g n
sudo n latest
```

Установите все необходимые модули из /admin folder: `npm install`
Затем выполните следующие команды:  

1. `npm start` - Для проверки
2. `npm run build` - Для сборки
3. `serve -s build` - Для проверки развернутого пакета (не обязательно)

В случае использования команды `start` демон будет запущен на порте 3000

## SSL сертификаты

Создайте какой-нибудь каталог, например /var/keys/ и выполните из него команду:  

```
openssl req
-newkey rsa:2048
-sha256 
-nodes 
-keyout domain.key
-x509
-days 365
-out domain.pem
-subj "/C=RU/ST=Moscow/L=Pushkino/O=KoshkaSoft/CN=domain.org"
```

Команду записываем ОДНОЙ строкой.  

*ВАЖНО*! Значение CN (имя домена) прописывайте для точной адресации внешнего вызова домена из Telegramm.

Выпоняйте последующие действия (curl) после того, как выполнили все предыдущие шаги  
и у вас есть наполнение базы данных всеми данными.  
Когда у вас будут все данные в БД тогда: загрузите сертификат и настройте адрес для связи с вашим доменом:  

```
curl \
-F "url=https://domain.org/webhook.php" \
-F "certificate=@/var/keys/domain.pem" \
https://api.telegram.org/bot<YOURTOKEN>/setWebhook
```

## Хосты Apache2

### Настройка SSL для связи TelegrammBot с доменом (callback):

Создайте сайт в /etc/apache2/sites-available

```
<IfModule mod_ssl.c>
	<VirtualHost *:443>
		ServerAdmin admin@domain.org
		ServerName bot.domain.org
		DocumentRoot /path/to/project/backend/bot
# -
		ErrorLog ${APACHE_LOG_DIR}/talegrammbot.log
		CustomLog ${APACHE_LOG_DIR}/talegrammbot-access.log combined
# -
		SSLEngine on
# -
		SSLCertificateFile	/var/keys/domain.pem
		SSLCertificateKeyFile /var/keys/domain.key
# -
		<FilesMatch "\.(cgi|shtml|phtml|php)$">
				SSLOptions +StdEnvVars
		</FilesMatch>
# -
		<Directory /path/to/project/backend/bot>
			Options Indexes FollowSymLinks
			AllowOverride All
			Require all granted
		</Directory>
	</VirtualHost>
</IfModule>
```

### Настройка HTTP для Admin и REST Admin api

Создайте конфигурацию для серверной части api и сайта управления (admin site) (см. Alias) с настроенным react js sources.  
Вам понадобится использовать правильно настроенный порт, подходящий для текущей конфигурации системы.  

```
<VirtualHost *:[port]>
	ServerName domain.org
# -
	ServerAdmin admin@domain.org
	DocumentRoot /path/to/project/ci/public
# -
	Alias /admin /path/to/project/admin/build/
# -
	<Directory /path/to/project/ci/public>
		AllowOverride all
		Require all granted
	</Directory>
    <Directory /path/to/project/backend/vendor/>
        AllowOverride all
        Require all granted
    </Directory>
	<Directory /path/to/project/admin/build/>
		AllowOverride all
		Require all granted
	</Directory>
# -
	ErrorLog ${APACHE_LOG_DIR}/botapi_error.log
	CustomLog ${APACHE_LOG_DIR}/botapi_access.log combined
</VirtualHost>
``` 

Исполните этот код командой `sudo a2ensite sitename`
И перезагрузите apache `sudo service apache2 reload`

_Далее смотрите чем заменять domain.org и bot.domain.org._

## Настройка локальной среды разработки

### Допущения:  

Пусть:  
* Внешний (Outer) ip: `7.98.98.10`
* Внешний HTTP порт для внешнего подключения из Telegramm к боту: `443`
* Динамический DNS для удаленных подключений: `telegrammbot.olga.ddns.net`
* Внутренний ip: `192.168.0.100`
* Внутренний ip роутера: `192.168.0.1`
* Внутренние HTTP порты для соединения с apache: `8080` и `443`
* Внутренние HTTP порты для NodeJS: `3000`
* Внутренний HTTP порт для Nginx: `80`
* Локальное имя сервера для PHP сервиса admin REST service: `telegrammbotapi`
* Локальное имя сервера для NodeJS компиляции admin-on-rest: `telegrammadmin`

Установка имен для серверов Apache2 в настройках VHosts:  

* Apache2 SSL настройки: `bot.domain.org` -> `telegrammbot.olga.ddns.net`.
* Apache2 HTTP настройки: `domain.org` -> `telegrammbotapi`

### Настройка роутера

Нужно настроить:

* Перенаправление портов (Port forwarding)
* Динамический DNS 
* Привязку адреса MAC к IP

Вам нужен доступ к панели управления роутера через веб-интерфейс. Доступ к ней можно получить по адресу  
192.168.0.1 (см. список сверху). Например пишите в строку браузера:  
`http://192.168.0.1`. Пароль администратора написан на коробке роутера   
если не подойдет сбрасываете настройки и вводите пароль по умолчанию  
в описании роутера.
В настройках находите "виртуальный сервер" ("virtual server") или "перенаправление портов" ("port forwarding"), но не "запуск портов" ("port triggering".  
_погуглите "port forwarding"_  

Настройка (основана на вышеобозначенных допущениях):  
* Внешний порт (должен быть): 443
* Внутренний порт: 443
* Внутренний ip: 192.168.0.100

Затем находите закладку "динамический DNS" и также настраиваете.  
Вам нужно будет зарегистрироваться у провайдера сервиса динамического DNS (DDNS).  

Затем в настройках связи адреса MAC к IP для локального сервера DHCP привязываете   
локальный MAC адрес к статическому IP адресу.  
MAC адрес вы можете получить командой `ifconfig` из поля выдачи результата `HWaddr`, потом этот адрес используйте для настройки перенаправления портов.

### /etc/hosts

Добавьте следующие строки в /etc/hosts:  

```
127.0.0.1	telegrammbotadmin
127.0.0.1	telegrammbotapi
127.0.0.1	telegrammbotcb
```

* telegrammbotadmin - для _develop_ ReactJS управляющей панели
* telegrammbotapi - для _develop_ серверной части панели ReactJS
* telegrammbotcb - для _test connection_ для вызова бота в Telegramm

### Nginx

На машине разработчика нужно чтобы nginx и для apache и для nodejs will располагался на одном  домене  
Иначе некоторые браузеры типа Chromium будут использовать OPTIONS вместо   
POST для передачи по HTTP. 

Настройте сайт а /etc/nginx/sites-available и подключите его к   
/etc/nginx/sites-enabled командой `ln -s source destination`   

```
map $http_upgrade $connection_upgrade {
	default upgrade;
	'' close;
}
# -
upstream websocket {
	server localhost:3000;
}
# -
upstream api {
	server telegrammbotapi:8080;
}
# -
server {
	listen 80;
	server_name telegrammbotapi;
# -
	location / {
		proxy_pass http://websocket;
		proxy_http_version 1.1;
		proxy_set_header Upgrade $http_upgrade;
		proxy_set_header Connection $connection_upgrade;
	}
# -
	location /api {
		rewrite /api/(.*) /$1  break;
		proxy_pass http://api;
		proxy_set_header Host telegrammbotapi;
	}
}
```

Перезапустите сервер:  `sudo service nginx restart`

### Apache2 перенастройка портов

Настройте apache2 на порт 8080 вместо 80 в `/etc/apache2/ports.conf`
Перезагрузите apache2: `sudo service apache2 restart`

Создайте ещё один SSL VHost в каталоге `/etc/hosts` для локального тестирования соединения:  

```
<IfModule mod_ssl.c>
	<VirtualHost *:443>
		...
		ServerName telegrammbotcb
		...
	</VirtualHost>
</IfModule>
```

Всегда подтверждайте сообщения от системы безопасности об отсутствующих сертификатах (missing certificate), до тех пор  
пока вы не получите реально одобренный сертификат безопасности CA.

## Настройка удаленной среды

Вам также нужно настроить `apache2` на `8080` порт для протокола `http`.  
Вам нужно следующим образом настроить nginx. Таким же образом вам нужно  
настроить его для проверки `npm run build` пакета в среде разработки.  

Пусть наш домен будет `antibot.technofractal.org`.

```
map $http_upgrade $connection_upgrade {
	default upgrade;
	'' close;
}
# -
upstream api {
	server antibot.technofractal.org:8080;
}
# -
upstream admin {
	server antibot.technofractal.org:8080;
}
# -
server {
	listen 80;
	server_name antibot.technofractal.org;
# -
	location /admin {
		proxy_pass http://admin;
		proxy_http_version 1.1;
		proxy_set_header Upgrade $http_upgrade;
		proxy_set_header Connection $connection_upgrade;
	}
# -
	location /api {
		rewrite /api/(.*) /$1  break;
		proxy_pass http://api;
	}
}
```

Запускайте `npm run build` в каталоге `/admin` каждый раз, когда меняется клиентская часть (frontend) admin NodeJs  
 чтобы пересобрать её. 

# Проверка бота из командной строки

```
curl \
	--tlsv1 -v -k \
	-X POST \
	-H "Content-Type: application/json" \
	-H "Cache-Control: no-cache"  \
	-d '{
"update_id":10000,
"message":{
  "date":1441645532,
  "chat":{
     "last_name":"Test Lastname",
     "id":1111111,
     "first_name":"Test",
     "username":"Test"
  },
  "message_id":1365,
  "from":{
     "last_name":"Test Lastname",
     "id":1111111,
     "first_name":"Test",
     "username":"Test"
  },
  "text":"/start"
}
}' "https://url.org"

```

# Важная информация

Когда вы напишете тексты для бота, положите их в каталог `/backend/bot/data`. Там вы можете разделить строки с помощью CRLF, иначе приложение их   
будет рассматривать как одну.
Добавить новую строку (типа тэг br): - "\r".  
Также можете использовать шаблоны.  
Например:  

```
<b>Some header<b>\r
Some text
will not split here\nbut here!\n
Good evening: {name}
```

# Ссылки

* https://marmelab.com/admin-on-rest/
* https://www.nginx.com/blog/websocket-nginx/
* http://docs.doctrine-project.org/en/latest/tutorials/getting-started.html
* https://reacttraining.com/react-router/web/example/basic
* https://code.tutsplus.com/tutorials/working-with-restful-services-in-codeigniter--net-8814
* https://core.telegram.org/bots/api
* https://telegram-bot-sdk.readme.io/docs
* https://dev.mysql.com/doc/refman/5.7/en/charset-applications.html
* https://scottlinux.com/2011/03/04/rotate-mysql-backups-with-logrotate/
* https://rclone.org/
