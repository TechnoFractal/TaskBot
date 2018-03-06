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
		
		if ($result->getMessage()) {
			/* @var $message Message */
			$message = $result->getMessage();
			$this->handleMessage($message);
		} else if ($result->get("callback_query")) {
			/* @var $query Update */
			$query = $result->get("callback_query");
			$this->handleQuery($query);
		} else {
			error_log("Invalid request");
		}
	}
	
	public function handleQuery(Update $query)
	{		
		/* @var $api BotApi */
		$api = $this->api;
		$data = explode(':', $query->get("data"));
		
		if (count($data) == 2) {
			$text = sprintf("Действие: %s, Пост: %d", $data[1], $data[0]);

			$params = [
				'callback_query_id'		=> $query->get('id'),
				'text'					=> $text
			];

			$api->answerCallbackQuery($params);
		} else {
			error_log($query->get("data"));
		}
	}
	
	public function handleMessage(Message $message)
	{
		/* @var $api Api */
		$api = $this->api;

		/* @var $config \Config */
		$config = $this->config;
		
		/* @var $commands Commands */
		$commands = new Commands($config, $api, $message);

		if ($message->getNewChatParticipant()) {		
			$commands->hello();
		} else if ($message->getLeftChatParticipant()) {
			$commands->bye();
		} else if ($message->getSticker()) {
			$commands->sticker();
		} else if ($message->get('pinned_message')) {
			$commands->pin();
		} else if ($message->get('successful_payment')) {
			$commands->wtf();
		} else if ($message->get('invoice')) {
			$commands->wtf();
		} else if ($message->get('migrate_from_chat_id')) {
			$commands->wtf();
		} else if ($message->get('migrate_to_chat_id')) {
			$commands->wtf();
		} else if ($message->get('channel_chat_created')) {
			$commands->wtf();
		} else if ($message->get('supergroup_chat_created')) {
			$commands->wtf();
		} else if ($message->get('group_chat_created')) {
			$commands->wtf();
		} else if ($message->get('delete_chat_photo')) {
			$commands->wtf();
		} else if ($message->get('new_chat_photo')) {
			$commands->wtf();
		} else if ($message->get('new_chat_title')) {
			$commands->wtf();
		} else if ($message->getText()) {
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
			$commands->idontknow();
		}
	}
}
