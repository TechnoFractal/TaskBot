> Powered by Koshka!

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