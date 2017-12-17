> Powered by Koshka!

# SSl

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

Upload certificate and set callback:

```
curl \
	-F "url=https://domain.org/webhook.php" \
	-F "certificate=@/var/keys/domain.pem" \
	https://api.telegram.org/bot<YOURTOKEN>/setWebhook
```

# VHost config

```
<IfModule mod_ssl.c>
	<VirtualHost *:443>
		ServerAdmin admin@domain.org
		ServerName domain.org
		DocumentRoot /path/to/bot

		ErrorLog ${APACHE_LOG_DIR}/koshkabot.log
		CustomLog ${APACHE_LOG_DIR}/koshkabot-access.log combined

		SSLEngine on

		SSLCertificateFile	/var/keys/domain.pem
		SSLCertificateKeyFile /var/keys/domain.key

		<FilesMatch "\.(cgi|shtml|phtml|php)$">
				SSLOptions +StdEnvVars
		</FilesMatch>

		<Directory "/path/to/bot">
			Options Indexes FollowSymLinks
			AllowOverride All
			Require all granted
		</Directory>
	</VirtualHost>
</IfModule>
```

# Install dependencies

composer install

# Configure web hook

curl \
	-F "url=https://<YOURDOMAIN.EXAMPLE>/<WEBHOOKLOCATION>" \
	-F "certificate=@<YOURCERTIFICATE>.pem" \
	https://api.telegram.org/bot<YOURTOKEN>/setWebhook

# Config

Create config.yml in root of the project and put:

```
token: "BOT_TOKEN"
```

# Test

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

# Admin

```
sudo npm install -g n
npm install pm2 -g
cd admin
npm install
npm run build
serve -s build
```

https://ygamretuta.xyz/deploy-create-react-app-with-pm2-16beb90ce52
https://react-server.io/docs/guides/production