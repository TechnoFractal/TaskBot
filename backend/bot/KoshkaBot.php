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

use \Telegram\Bot\BotApi;
use \Telegram\Bot\Objects\Update;
use \Telegram\Bot\Objects\Message;

use \constants\Constants;

/**
 * Description of KoshkaBot
 *
 * @author Olga Pshenichnikova <olga@technofractal.org>
 */
class KoshkaBot 
{	
	/**
	 *
	 * @var Api
	 */
	private $api;
	
	/**
	 *
	 * @var \Config
	 */
	private $config;

	public function __construct(\Config $config) 
	{
		$this->config = $config;
		$this->api = new BotApi($config->getToken());
	}
	
	public function handleUpdate()
	{
		/* @var $api Api */
		$api = $this->api;
		
		/* @var $result Update */
		$result = $api->getWebhookUpdates();
		
		//error_log(print_r($result->getRawResponse(), 1));
		
		if ($result->getMessage()) {
			$this->handleMessage($result);
		} else if ($result->get("callback_query")) {
			$this->handleQuery($result);
		} else {
			error_log("Invalid request");
		}
	}
	
	public function handleQuery(Update $update)
	{		
		/* @var $api BotApi */
		$api = $this->api;
		
		/* @var $config \Config */
		$config = $this->config;
		
		/* @var $query Update */
		$query = $update->get("callback_query");
		
		/* @var $commands Commands */
		$commands = new CallbackCommands($query);
		
		$data = explode(':', $query->get("data"));
		
		if (count($data) == 2) 
		{
			/* @var $action string */
			$action = $data[1];
			/* @var $postId int */
			$postId = $data[0];
			
			//$text = sprintf("Действие: %s, Пост: %d", $data[1], $data[0]);
			
			try {			
				switch ($action) {
					case Constants::ACTION_DONE:
						$commands->done($postId);
						$text = "Задание помеченно выполненным";
						break;
					case Constants::ACTION_POSTPONE:
						$commands->postpone($postId);
						$text = "Задание отложенно";
						break;
					default:
						throw new \exceptions\UnknownQueryAction($action);
				}
			} catch (\exceptions\PostAlreadyDone $e) {
				$text = "Задание уже выполнено";
			} catch (\exceptions\PostAlreadyPostponed $e) {
				$text = "Задание уже отложено";
			}
			
			$params = [
				'callback_query_id'		=> $query->get('id'),
				'text'					=> $text
			];

			$api->answerCallbackQuery($params);
		} else {
			error_log($query->get("data"));
		}
	}
	
	public function handleMessage(Update $update)
	{
		/* @var $api Api */
		$api = $this->api;

		/* @var $config \Config */
		$config = $this->config;
		
		/* @var $message Message */
		$message = $update->getMessage();
		
		/* @var $commands Commands */
		$commands = new Commands($config, $api, $update);

		if ($message->getNewChatParticipant()) {
			error_log('getNewChatParticipant');
			$commands->hello();
		} else if ($message->getLeftChatParticipant()) {
			error_log('getLeftChatParticipant');
			$commands->bye();
		} else if ($message->getSticker()) {
			error_log('getSticker');
			$commands->sticker();
		} else if ($message->get('pinned_message')) {
			error_log('pinned_message');
			$commands->pin();
		} else if ($message->get('successful_payment')) {
			error_log('successful_payment');
			$commands->wtf();
		} else if ($message->get('invoice')) {
			error_log('invoice');
			$commands->wtf();
		} else if ($message->get('migrate_to_chat_id')) {
			error_log('migrate_to_chat_id');
			$newChatId = $message->get('migrate_to_chat_id');
			$commands->supergroup($newChatId);
		} else if ($message->get('channel_chat_created')) {
			error_log('channel_chat_created');
			$commands->hello();
		} else if ($message->get('group_chat_created')) {
			error_log('group_chat_created');
			$commands->hello();
		} else if ($message->get('delete_chat_photo')) {
			error_log('delete_chat_photo');
			$commands->wtf();
		} else if ($message->get('new_chat_photo')) {
			error_log('new_chat_photo');
			$commands->wtf();
		} else if ($message->get('new_chat_title')) {
			error_log('new_chat_title');
			$commands->wtf();
		} else if ($message->getText()) {
			error_log('getText');
			
			/* @var $text string */
			$text = $message->getText();

			switch ($text) {
				case "/start":
					$commands->start();
					break;
				case "/stop":
					$commands->stop();
					break;
				case "/help":
				case DataHelper::INFO:
					$commands->info();
					break;
				case DataHelper::CONTACT:
					$commands->contacts();
					break;
				case DataHelper::LIGHT_TASKS:
				case DataHelper::MIDDLE_TASKS:
				case DataHelper::HARD_TASKS:
					$commands->task();
					break;
				default:
					$commands->idontunderstand();
			}
		} else {
			error_log('else');
			$commands->idontknow();
		}
	}
}
