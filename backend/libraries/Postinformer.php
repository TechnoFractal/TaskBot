<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace libraries;

use \Doctrine\ORM\EntityManager;
use \Telegram\Bot\Api;
use \Telegram\Bot\Exceptions\TelegramResponseException;
use \Doctrine\Common\Collections\Criteria;

/**
 * Description of Postinformer
 *
 * @author Olga Pshenichnikova <olga@technofractal.org>
 */
class Postinformer 
{
	/**
	 *
	 * @var Api
	 */
	private $api;
	
	public function __construct(\Config $config) 
	{
		$token = $config->getToken();
		$this->api = new Api($token);
	}
	
	private function sendMessage(int $chatId, string $text)
	{
		try {
			$this->api->sendMessage([ 
				'chat_id' => $chatId,
				'parse_mode' => 'HTML',
				'text' => $text
			]);
		} catch (TelegramResponseException $e) {
			error_log($e->getMessage());
		}
	}
	
	public function informRequesters(EntityManager $orm, \orm\Post $post)
	{
		$expr = Criteria::expr();		
		
		$criteria = Criteria::create()->where($expr->eq("enabled", true));
		
		/* @var $queuepointers array */
		$requesters = $orm
			->getRepository(\orm\Requester::class)
			->matching($criteria)
			->toArray();
			
		/* @var $requester orm\Requester */
		foreach ($requesters as $requester)
		{
			$categoryId = $post->getCategory()->getId();
			$categoryName = \bot\DataHelper::getCategoryName($categoryId);
			$text = \bot\DataHelper::getNewTasks($categoryName);
			$chatId = $requester->getChatId();
			
			if ($chatId)
			{			
				$this->sendMessage($chatId, $text);
			}
		}
	}
}
