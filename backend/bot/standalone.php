<?php

define("ROOT", dirname(__FILE__) . '/..');

//die(ROOT . '/vendor/autoload.php');

require ROOT . '/vendor/autoload.php';

use Telegram\Bot\Api;
use Symfony\Component\Yaml\Yaml;

$config = Yaml::parse(file_get_contents(ROOT . '/config.yml'));

print_r($config); die();

$telegram = new Api($config['token']);

$response = $telegram->getMe();

$botId = $response->getId();
$firstName = $response->getFirstName();
$username = $response->getUsername();

echo "$botId - $firstName - $username\r\n";