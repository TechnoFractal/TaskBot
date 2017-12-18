<?php

include('vendor/autoload.php'); //Подключаем библиотеку

use Telegram\Bot\Api;
use Symfony\Component\Yaml\Yaml;

$config = Yaml::parseFile('config.yml');
$telegram = new Api($config['token']);

$result = $telegram->getWebhookUpdates();

//Текст сообщения
$text = $result["message"]["text"];
//Уникальный идентификатор пользователя
$chat_id = $result["message"]["chat"]["id"];
//Юзернейм пользователя
$name = $result["message"]["from"]["username"];
//Клавиатура
$keyboard = [
	["Кошке нужна валерьянка"],
	["Мишка )"]
];

if($text) {
	 if ($text == "/start") {
		$reply = "Добро пожаловать в КошкинБот!";
		$reply_markup = $telegram->replyKeyboardMarkup([ 
			'keyboard' => $keyboard, 
			'resize_keyboard' => true, 
			'one_time_keyboard' => false 
		]);
		
		$telegram->sendMessage([ 
			'chat_id' => $chat_id, 
			'text' => $reply, 
			'reply_markup' => $reply_markup 
		]);
	} elseif ($text == "/help") {
		$reply = "Слава Котам!!!";
		$telegram->sendMessage([ 
			'chat_id' => $chat_id, 
			'text' => $reply 
		]);
	} elseif ($text == "Кошке нужна валерьянка") {
		$telegram->sendSticker([ 
			'chat_id' => $chat_id, 
			'sticker' => 'CAADBAADxgMAAv4zDQY6bEeD67rtlAI'
		]);
	} elseif ($text == "Мишка )") {
		$telegram->sendSticker([ 
			'chat_id' => $chat_id, 
			'sticker' => 'CAADAgADnQQAAmvEygrzEw25pNCS5wI'
		]);
	} else {
		$reply = "По запросу \"<b>".$text."</b>\" ничего не найдено.";
		$telegram->sendMessage([ 
			'chat_id' => $chat_id, 
			'parse_mode'=> 'HTML', 
			'text' => $reply 
		]);
	}
}else{
	$telegram->sendMessage([ 
		'chat_id' => $chat_id, 
		'text' => "Отправьте текстовое сообщение." 
	]);
	
	/*$debug = print_r($result, true);
	
	$telegram->sendMessage([ 
		'chat_id' => $chat_id, 
		'parse_mode' => 'Markdown', 
		'disable_web_page_preview' => true, 
		'text' => "```$debug```"
	]);*/
}
