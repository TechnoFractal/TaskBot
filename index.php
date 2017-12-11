<?php
    include('vendor/autoload.php'); //Подключаем библиотеку

	use Telegram\Bot\Api;
	use Symfony\Component\Yaml\Yaml;

	$config = Yaml::parseFile('config.yml');
	$telegram = new Api($config['token']);
	
	echo "test";