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