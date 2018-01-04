<?php

/*
 * Copyright (C) 2018 Olga Pshenichnikova <olga@technofractal.org>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace bot;

use \Telegram\Bot\Api;
use \Telegram\Bot\Objects\Message;
use \Telegram\Bot\Objects\Update;

/**
 * Description of KoshkaBot
 *
 * @author Olga Pshenichnikova <olga@technofractal.org>
 */
class KoshkaBot 
{
	public function handleRequest(Api $api) : bool
	{
		/* @var $result Update */
		$result = $api->getWebhookUpdates();
		
		/* @var $result Message */
		$message = $result->getMessage();
		
		if (!$message)
		{
			error_log("Invalid request");
			return false;
		}
		
		//Текст сообщения
		$text = $message->getText();
		//Уникальный идентификатор пользователя
		$chat_id = $message->getChat()->getId();
		
		
		//error_log($text); die();
		
		//Клавиатура
		$keyboard = [
			[TasksQueue::LIGHT_TASKS],
			[TasksQueue::MIDDLE_TASKS],
			[TasksQueue::HARD_TASKS],
			[TasksQueue::INFO],
			[TasksQueue::CONTACT]
		];

		//error_log('here');
		
		if($text) {
			 if ($text == "/start") {
				$reply = "Добро пожаловать в АнтиБот!";
				$reply_markup = $api->replyKeyboardMarkup([ 
					'keyboard' => $keyboard, 
					'resize_keyboard' => true, 
					'one_time_keyboard' => false 
				]);

				$api->sendMessage([ 
					'chat_id' => $chat_id, 
					'text' => $reply, 
					'reply_markup' => $reply_markup 
				]);
			} elseif ($text == "/help" || $text == TasksQueue::INFO) {
				$api->sendMessage([ 
					'chat_id' => $chat_id, 
					'parse_mode' => 'HTML',
					'text' => TasksQueue::getInfo()
				]);
			} elseif ($text == TasksQueue::CONTACT) {
				$api->sendMessage([ 
					'chat_id' => $chat_id, 
					'parse_mode' => 'HTML',
					'text' => TasksQueue::getContacts()
				]);
			} else if (
				$text == TasksQueue::LIGHT_TASKS ||
				$text == TasksQueue::MIDDLE_TASKS ||
				$text == TasksQueue::HARD_TASKS) {
				$tasksQueue = new TasksQueue();
				
				//error_log(print_r($result["message"], 1)); die();
				
				$requesterData = new RequesterData($message->getFrom());
				//error_log("here1"); die();
				
				$api->sendMessage([ 
					'chat_id' => $chat_id, 
					'parse_mode' => 'HTML',
					'text' => $tasksQueue->handleRequest($text, $requesterData)
				]);
			} else {
				$reply = "По запросу \"<b>".$text."</b>\" ничего не найдено.";
				$api->sendMessage([ 
					'chat_id' => $chat_id, 
					'parse_mode'=> 'HTML', 
					'text' => $reply 
				]);
			}
		}else{
			$api->sendMessage([ 
				'chat_id' => $chat_id, 
				'text' => "Отправьте текстовое сообщение." 
			]);
		}
		
		return true;
	}
}
