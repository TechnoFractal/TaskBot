> Powered by Koshka!

# Environments configuration

Further we will configure development environment for each developer  
on his computer and several or one remote "on server/pre build" environments.  
It can be for example - 4 developers environments, one remote for test and  
on remote - main production environment.  
Or it can be two developers environments and one remote production.  

## Technologies

On both we need:  

* Debian based Linux destribution
* Git
* NodeJS
* NodeJS module - react-scripts
* Apache2
* Apache2 module - curl
* Apache2 module - mysql
* PHP 7.*
* Composer
* Doctrine
* CodeIgniter
* CodeIgniterRestServer
* MySQL
* OpenSSL

On development environment we need also `nginx`

## @BotFather

For **each environment** we need one Telegramm bot.  We will create it with  
BotFather. 

1. Find @BotFather applicative account in Telegramm.  
2. Print:  /help
3. /newbot
4. Forward instructions
5. Create token, and save it 

You cat create tokens.txt at root of project and save there your tokens.  
It is ignored by git.

## Git

Clone the project some where it can be accessable for Apache2 and Nginx

## Composer

Navigate to /path/to/project/backend forlder and run `composer install`

## CodeIgniter

Run from the root of project: `./installci.sh`

Configure /ci/public/index.php  
Find and adjust follow:  

```
$application_folder = '../../backend/application';
$system_path = '../../backend/vendor/codeigniter/framework/system';
```

Configure /backend/application/config/config.php
Find and adjust follow:  

```
$config['enable_hooks'] = TRUE;
```

## DB

Create database and user:

```
mysql -uroot -p
create database telegrammbot;
create user 'telegrammbot'@'localhost' identified by 'dbpassword';
grant all privileges on telegrammbot.* to 'telegrammbot'@'localhost';
flush privileges;
```

You can use user and password as you wish, just keep it same in DB/user  
creation and bot configuration

## Bot config

Create config.yml in /backend/ folder and put:

```
token: "BOT_TOKEN"
db:
  user: "telegrammbot"
  password: "dbpassword"
  db: "telegrammbot"
  driver: "pdo_mysql"
```

The driver part is mostly problematic one. You need make your php installation  
support `pdo_msql` driver or any other.
`<? echo phpinfo();` integrated in outside page will help you to reach this  
goal.

## DB schemas:

### Create schema

Run at /backend:  
* For create: `vendor/bin/doctrine orm:schema-tool:create`  
* For drop: `vendor/bin/doctrine orm:schema-tool:drop'
* For update: `vendor/bin/doctrine orm:schema-tool:update`

You need add `--dump-sql` for print the query or `--force` for run the query.  

### Deploy schema with data if it created from scratch  

* Navigate to `/backend/db/` and run `mysql -u user -p`  
* Enter the password

```
use dbname
source script.sql
```

Create account for admin:  

```
desc users;
insert into users(`login`, `password`) values ("username", "password");
select * from users;
```

_You need choose username and password_ It is credential data for login from  
Admin-On-Rest admin panel.

# NodeJS Admin

Install nodejs, and fix nodejs on Ubuntu after install:

```
sudo ln -s /usr/bin/nodejs /usr/local/bin/node
```

Install last npm:

```
sudo npm install -g n
n latest
```

Install all necessary modules from /admin folder: `npm install`
Than use follow commands:  

1. `npm start` - For debug
2. `npm run build` - For build
3. `serve -s build` - For test of deploy package (optional)

In case of `start` demonization will occure on port 3000

## SSL Certificates

Create some folder, for example /var/keys/ and run there:  

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

*IMPORTANT*! You need to set CN exactly as your domain name of the outer  
callback for Telegramm.

Perform follow (curl) step after you will achive all the goals described  
below, and have all necessary data.  
When you will have all the data, than: upload certificate and set callback:  

```
curl \
	-F "url=https://domain.org/webhook.php" \
	-F "certificate=@/var/keys/domain.pem" \
	https://api.telegram.org/bot<YOURTOKEN>/setWebhook
```

## Apache2 hosts

### SSL configuration for TelegrammBot webhook (callback):

Create site in /etc/apache2/sites-available

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

### HTTP configuration for Admin and REST Admin api

Create configuration for api backend and admin site (see Alias)  
with prebuild react js sources.  
Here you need use appropriate port configured for the current environment.  

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

Enable it with `sudo a2ensite sitename`
And reload apache `sudo service apache2 reload`

_See substitutions for domain.org and bot.domain.org further._

## Local development environment configuration

### Assumptions:  

Let:  
* Outer ip: `7.98.98.10`
* Outer HTTP port from outside for Telegramm to connect to the bot: `443`
* Dynamic DNS name for remote connections: `telegrammbot.olga.ddns.net`
* Inner ip: `192.168.0.100`
* Inner router ip: `192.168.0.1`
* Inner HTTP ports for apache to get connections: `8080` and `443`
* Inner HTTP port for NodeJS: `3000`
* Inner HTTP port for Nginx: `80`
* Local server name for PHP admin REST service: `telegrammbotapi`
* Local server name for NodeJS admin-on-rest compilation: `telegrammadmin`

Substitutions for Apache2 servers in VHosts configurations:  

* Apache2 SSL configuration: `bot.domain.org` -> `telegrammbot.olga.ddns.net`.
* Apache2 HTTP configuration: `domain.org` -> `telegrammbotapi`

### Port forwarding and dynamic DNS

You need access your router admin panel at the web interface.  It is  
represented by 192.168.0.1 in list above. For instance it can be:  
`http://192.168.0.1`. Type it at your browser. Find password for admin  
at box of the router or reset the settings and find the default password  
in product description.
There you need find "virtual server" or "port forwarding", but not port  
triggering.  
_google for "port forwarding"_  

Configure (basing on assumptions above):  
* Outer port (must be): 443
* Inner port: 443
* Inner ip: 192.168.0.100

Than you need find option for dynamic DNS and configure it as well.  
You will need to register on the DDNS service provider.  

### /etc/hosts

Append follow lines in /etc/hosts:  

```
127.0.0.1	telegrammbotadmin
127.0.0.1	telegrammbotapi
```

### Nginx

On the dev, you will need use nginx for both apache and nodejs will be on same  
domain. Otherwise some browsers as Chromium will ose OPTIONS instead of  
POST over the HTTP. 

Configure site in /etc/nginx/sites-available and link it to  
/etc/nginx/sites-enabled with `ln -s source destination` command  

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

Restart the server:  `sudo service nginx restart`

### Apache2 ports reconfig

Set apache2 listen on port 8080 instead of 80 in `/etc/apache2/ports.conf`
You will need restart apache2: `sudo service apache2 restart`

## Remote environment configuration

TODO: to write

# Test the bot from command line

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

# Links

* https://marmelab.com/admin-on-rest/
* https://www.nginx.com/blog/websocket-nginx/
* http://docs.doctrine-project.org/en/latest/tutorials/getting-started.html
* https://reacttraining.com/react-router/web/example/basic