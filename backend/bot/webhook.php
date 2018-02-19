<?php

include('../vendor/autoload.php');

use Telegram\Bot\Api;

$config = Config::getConfig();
$api = new Api($config['token']);

$koshkaBot = new bot\KoshkaBot();

try {
	//error_log("WTF!!!?"); die();
	$koshkaBot->handleRequest($api);
} catch (Exception $e) {
	error_log($e->getMessage());
	error_log($e->getTraceAsString());
	echo $e->getMessage();
}
