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
	["Отладка"],
	["Пиксель"],
	["Последние статьи"],
	["Картинка"],
	["Гифка"]
];

if($text){
	 if ($text == "/start") {
		$reply = "Добро пожаловать в бота!";
		$reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
		$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]);
	} elseif ($text == "/help") {
		$reply = "Информация с помощью.";
		$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
	} elseif ($text == "Картинка") {
		$url = "https://68.media.tumblr.com/6d830b4f2c455f9cb6cd4ebe5011d2b8/tumblr_oj49kevkUz1v4bb1no1_500.jpg";
		$telegram->sendPhoto([ 'chat_id' => $chat_id, 'photo' => $url, 'caption' => "Описание." ]);
	} elseif ($text == "Кошка") {
		$url = "https://68.media.tumblr.com/bd08f2aa85a6eb8b7a9f4b07c0807d71/tumblr_ofrc94sG1e1sjmm5ao1_400.gif";
		$telegram->sendDocument([ 'chat_id' => $chat_id, 'document' => $url, 'caption' => "Описание." ]);
	} elseif ($text == "Последние статьи") {
		$html = simplexml_load_file('http://netology.ru/blog/rss.xml');
		foreach ($html->channel->item as $item) {
			$reply .= "\xE2\x9E\xA1 ".$item->title." (<a href='".$item->link."'>читать</a>)\n";
		}
		$telegram->sendMessage([ 'chat_id' => $chat_id, 'parse_mode' => 'HTML', 'disable_web_page_preview' => true, 'text' => $reply ]);
	} else if ($text == 'Отладка') {
		$debug = print_r($result, true);
		$telegram->sendMessage([ 
			'chat_id' => $chat_id, 
			'parse_mode' => 'HTML', 
			'disable_web_page_preview' => true, 
			'text' => $debug 
		]);
	} else if ($text == 'Пиксель') {
		$reply = "Пиксель <img src=\"http://nescafe2016.act4.ias.su/020b5cacb0e0a4de879f/\"/>";
		$telegram->sendMessage([ 'chat_id' => $chat_id, 'parse_mode'=> 'HTML', 'text' => $reply ]);
	} else {
		$reply = "По запросу \"<b>".$text."</b>\" ничего не найдено.";
		$telegram->sendMessage([ 'chat_id' => $chat_id, 'parse_mode'=> 'HTML', 'text' => $reply ]);
	}
}else{
	$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => "Отправьте текстовое сообщение." ]);
}
