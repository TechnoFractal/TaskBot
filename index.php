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
	["Последние статьи"],
	["Кошка"]
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
	} elseif ($text == "Картинка") {
		$url = "https://68.media.tumblr.com/" . 
				"6d830b4f2c455f9cb6cd4ebe5011d2b8/" . 
				"tumblr_oj49kevkUz1v4bb1no1_500.jpg";
		$telegram->sendPhoto([ 
			'chat_id' => $chat_id, 
			'photo' => $url, 
			'caption' => "Описание." 
		]);
	} elseif ($text == "Последние статьи") {
		$html = simplexml_load_file('http://ru-an.info/RSS/tag117.xml');
		$reply = "";
		
		foreach ($html->channel->item as $item) {
			$reply .= 
				"\xE2\x9E\xA1 " . 
				$item->title . 
				" (<a href='" . 
					$item->link . 
				"'>читать</a>)\n";
		}
		
		$telegram->sendMessage([ 
			'chat_id' => $chat_id, 
			'parse_mode' => 'HTML', 
			'disable_web_page_preview' => true, 
			'text' => $reply 
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
	$debug = print_r($result, true);
	
	$telegram->sendMessage([ 
		'chat_id' => $chat_id, 
		'parse_mode' => 'Markdown', 
		'disable_web_page_preview' => true, 
		'text' => "```$debug```"
	]);
}
