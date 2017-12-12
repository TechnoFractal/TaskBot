> Powered by Koshka!

# SSl

```
openssl req
-newkey rsa:2048
-sha256 
-nodes 
-keyout YOURPRIVATE.key
-x509
-days 365
-out YOURPUBLIC.pem
-subj "/C=US/ST=City/L=Brooklyn/O=Company/CN=YOURDOMAIN.EXAMPLE"
```

Test:

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