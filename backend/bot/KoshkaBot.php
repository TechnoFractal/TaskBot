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
use \Telegram\Bot\Objects\User;
use \Telegram\Bot\Objects\Update;
use \Telegram\Bot\Objects\Message;

/**
 * Description of KoshkaBot
 *
 * @author Olga Pshenichnikova <olga@technofractal.org>
 */
class KoshkaBot 
{
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
		
		$config = new \Config();
		
		/* @var $botId int */
		$botId = $config->getBotId();
		
		/* @var $chat_id int */
		$chat_id = $message->getChat()->getId();
		
		/* @var $newUser User */
		$newUser = $message->getNewChatParticipant();
		
		/* @var $leftUser User */
		$leftUser = $message->getLeftChatParticipant();
		
		/* @var $text string */
		$text = $message->getText();
		
		//Клавиатура
		$keyboard = [
			[DataHelper::LIGHT_TASKS],
			[DataHelper::MIDDLE_TASKS],
			[DataHelper::HARD_TASKS],
			[DataHelper::INFO],
			[DataHelper::CONTACT]
		];
		
		if ($newUser && $newUser->getId() == $botId)
		{			
			$api->sendMessage([ 
				'chat_id' => $chat_id, 
				'parse_mode' => 'HTML',
				'text' => DataHelper::getHello()
			]);
		} else if ($newUser && $newUser->getId() !== $botId) {
			$name = $this->getUserName($newUser);
			
			$api->sendMessage([ 
				'chat_id' => $chat_id, 
				'parse_mode' => 'HTML',
				'text' => DataHelper::getHi($name)
			]);
		} else if ($leftUser) {
			$name = $this->getUserName($leftUser);
			
			$api->sendMessage([ 
				'chat_id' => $chat_id, 
				'parse_mode' => 'HTML',
				'text' => DataHelper::getBye($name)
			]);
		} else if ($text) {
			 if ($text == "/start") {
				$reply_markup = $api->replyKeyboardMarkup([ 
					'keyboard' => $keyboard, 
					'resize_keyboard' => true, 
					'one_time_keyboard' => false 
				]);
				
				$api->sendMessage([ 
					'chat_id' => $chat_id, 
					'parse_mode' => 'HTML',
					'text' => DataHelper::getStart(),
					'reply_markup' => $reply_markup 
				]);
			} elseif ($text == "/help" || $text == DataHelper::INFO) {
				$api->sendMessage([ 
					'chat_id' => $chat_id, 
					'parse_mode' => 'HTML',
					'text' => DataHelper::getInfo()
				]);
			} elseif ($text == DataHelper::CONTACT) {
				$api->sendMessage([ 
					'chat_id' => $chat_id, 
					'parse_mode' => 'HTML',
					'text' => DataHelper::getContacts()
				]);
			} else if (
				$text == DataHelper::LIGHT_TASKS ||
				$text == DataHelper::MIDDLE_TASKS ||
				$text == DataHelper::HARD_TASKS) {
				$tasksQueue = new TasksQueue();
				
				$requesterData = new RequesterData($message);
				$respText = $tasksQueue->handleRequest($text, $requesterData);
				
				$api->sendMessage([ 
					'chat_id' => $chat_id, 
					'parse_mode' => 'HTML',
					'text' => $respText
				]);
			} else {				
				$api->sendMessage([ 
					'chat_id' => $chat_id, 
					'parse_mode'=> 'HTML', 
					'text' => DataHelper::getNotFound($text) 
				]);
			}
		} else {
			$api->sendMessage([ 
				'chat_id' => $chat_id, 
				'text' => DataHelper::getDefault()
			]);
		}
		
		return true;
	}
}
