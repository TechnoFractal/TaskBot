<?php

require 'vendor/autoload.php';

use Telegram\Bot\Api;
use Symfony\Component\Yaml\Yaml;

$config = Yaml::parseFile('config.yml');

//print_r($config); die();

$telegram = new Api($config['token']);

$response = $telegram->getMe();

$botId = $response->getId();
$firstName = $response->getFirstName();
$username = $response->getUsername();

echo "$botId - $firstName - $username\r\n";