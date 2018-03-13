<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace bot;

use \Telegram\Bot\Api;
use \Telegram\Bot\Objects\Message;
use \Telegram\Bot\Objects\Update;
use \Telegram\Bot\Objects\Sticker;
use \Telegram\Bot\Objects\User;

use \constants\Constants;

/**
 * Description of Commands
 *
 * @author Olga Pshenichnikova <olga@technofractal.org>
 */
class Commands 
{	
	/**
	 *
	 * @var Api
	 */
	private $api;
	
	/**
	 *
	 * @var int
	 */
	private $chatId;
	
	/**
	 *
	 * @var Message
	 */
	private $message;
	
	/**
	 *
	 * @var Config
	 */
	private $config;
	
	/**
	 *
	 * @var TasksQueue
	 */
	private $taskQueue;
	
	public function __construct(\Config $config, Api $api, Update $update)
	{
		/* @var $message Message */
		$message = $update->getMessage();
		/* @var $user User */
		$user = $message->getFrom();
		/* @var $chat Chat */
		$chat = $message->getChat();
		/* @var $data RequesterData */
		$data = new RequesterData($user, $chat);
		
		$this->config = $config;
		$this->api = $api;
		$this->message = $message;
		$this->chatId = $message->getChat()->getId();
		$this->taskQueue = new TasksQueue($data);
	}
	
	private function getUserName(User $user)
	{
		$firstname = $user->getFirstName();
		$lastname = $user->getLastName();
		$username = $user->getUsernameName();
		
		$name = "Неизвестный";
			
		if ($firstname || $lastname) {
			if ($firstname) {
				$name = $firstname;
			}

			if ($lastname) {
				$name .= " " . $lastname;
			}
		} else if ($username) {
			$name = $username;
		}
		
		return $name;
	}
	
	public function start()
	{
		$this->taskQueue->start();
		
		$keyboard = [
			[
				DataHelper::LIGHT_TASKS,
				DataHelper::MIDDLE_TASKS,
				DataHelper::HARD_TASKS
			],
			[
				DataHelper::INFO,
				DataHelper::CONTACT
			]
		];

		$reply_markup = $this->api->replyKeyboardMarkup([ 
			'keyboard' => $keyboard, 
			'resize_keyboard' => true, 
			'one_time_keyboard' => false 
		]);

		$this->api->sendMessage([ 
			'chat_id' => $this->chatId, 
			'parse_mode' => 'HTML',
			'text' => DataHelper::getStart(),
			'reply_markup' => $reply_markup 
		]);
	}
	
	public function stop()
	{
		$this->taskQueue->stop();
		
		$remove_keyboard = Api::replyKeyboardHide();
		
		$this->api->sendMessage([ 
			'chat_id' => $this->chatId, 
			'parse_mode' => 'HTML',
			'text' => DataHelper::getStop(),
			'reply_markup' => $remove_keyboard
		]);
	}
	
	public function pin()
	{
		$this->api->sendMessage([ 
			'chat_id' => $this->chatId, 
			'parse_mode' => 'HTML',
			'text' => DataHelper::getPin()
		]);
	}
	
	public function supergroup(int $newChatId)
	{
		$this->api->sendMessage([ 
			'chat_id' => $newChatId, 
			'parse_mode' => 'HTML',
			'text' => DataHelper::getSuperGroup()
		]);
	}
	
	public function hello()
	{
		if ($this->message->getNewChatParticipant()) 
		{
			/* @var $newUser User */
			$newUser = $this->message->getNewChatParticipant();
			
			/* @var $botId int */
			$botId = $this->config->getBotId();

			if ($newUser->getId() == $botId) {
				$text = DataHelper::getHello();
			} else {
				$name = $this->getUserName($newUser);
				$text = DataHelper::getHi($name);
			}
		} else {
			$text = DataHelper::getHello();
		}
		
		$this->api->sendMessage([ 
			'chat_id' => $this->chatId, 
			'parse_mode' => 'HTML',
			'text' => $text
		]);
	}
	
	public function bye()
	{
		/* @var $leftUser User */
		$leftUser = $this->message->getLeftChatParticipant();

		$name = $this->getUserName($leftUser);
		$isMale = !DataHelper::getIsFemale($leftUser);

		$this->api->sendMessage([ 
			'chat_id' => $this->chatId, 
			'parse_mode' => 'HTML',
			'text' => DataHelper::getBye($name, $isMale)
		]);
	}
	
	public function info()
	{
		$this->api->sendMessage([ 
			'chat_id' => $this->chatId, 
			'parse_mode' => 'HTML',
			'text' => DataHelper::getInfo()
		]);
	}
	
	public function contacts()
	{
		$this->api->sendMessage([ 
			'chat_id' => $this->chatId, 
			'parse_mode' => 'HTML',
			'text' => DataHelper::getContacts()
		]);
	}
	
	public function task()
	{				
		/* @var $text string */
		$text = $this->message->getText();
		
		$respText = $this->taskQueue->handleRequest($text);
		$empty = $this->taskQueue->isEmpty();
		
		if (!$empty) {
			$id = $this->taskQueue->getPost()->getId();
			
			$done = sprintf("%d:%s", $id, Constants::ACTION_DONE);
			$postpone = sprintf("%d:%s", $id, Constants::ACTION_POSTPONE);
			
			$inline = json_encode([
				'inline_keyboard' => [[
					[
						"text" => "Выполнено",
						"callback_data" => $done
					],
					[
						"text" => "Отложить",
						"callback_data" => $postpone
					]
				]] 
			]);

			$this->api->sendMessage([ 
				'chat_id' => $this->chatId, 
				'parse_mode' => 'HTML',
				'text' => $respText,
				'reply_markup' => $inline
			]);
		} else {
			$this->api->sendMessage([ 
				'chat_id' => $this->chatId, 
				'parse_mode' => 'HTML',
				'text' => $respText
			]);
		}
	}
	
	public function sticker()
	{
		/* @var $sticker Sticker */
		$sticker = $this->message->getSticker();
		
		$emoji = $sticker->get('emoji');
		
		if (!$emoji)
		{
			$emoji = "=^.^=";
		}
		
		$this->api->sendMessage([ 
			'chat_id' => $this->chatId, 
			'parse_mode' => 'HTML',
			'text' => $emoji
		]);
	}
	
	public function wtf()
	{
		$this->api->sendSticker([ 
			'chat_id' => $this->chatId, 
			'sticker' => 'CAADBAADxgMAAv4zDQY6bEeD67rtlAI'
		]);
	}
	
	public function idontunderstand()
	{
		/* @var $fromUser User */
		$fromUser = $this->message->getFrom();

		$isMale = !DataHelper::getIsFemale($fromUser);

		$this->api->sendMessage([ 
			'chat_id' => $this->chatId, 
			'parse_mode'=> 'HTML', 
			'text' => DataHelper::getNotFound($isMale) 
		]);
	}
	
	public function idontknow()
	{
		$this->api->sendMessage([ 
			'chat_id' => $this->chatId, 
			'text' => DataHelper::getDefault()
		]);
	}
}
