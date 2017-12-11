<?php
    include('vendor/autoload.php'); //Подключаем библиотеку
    use Telegram\Bot\Api; 

	$token = '';
	
    $telegram = new Api($token);