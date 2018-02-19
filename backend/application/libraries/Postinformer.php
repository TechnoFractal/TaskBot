<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use \Doctrine\ORM\EntityManager;
use \Doctrine\Common\Collections\Criteria;
use \Telegram\Bot\Api;

/**
 * Description of Postinformer
 *
 * @author Olga Pshenichnikova <olga@technofractal.org>
 */
class Postinformer 
{
	public static function informRequesters(
		EntityManager $orm,
		\orm\Post $post
	) {
		$token = Config::getConfig()["token"];
		$api = new Api($token);		
		
		$expr = Criteria::expr();		
		
		$criteria = Criteria::create();
		$criteria
			->where($expr->eq("isLast", true))
			->andWhere($expr->eq("category", $post->getCategory()));
		
		/* @var $queuepointers array */
		$queuepointers = $orm
			->getRepository(\orm\Queuepointer::class)
			->matching($criteria)
			->toArray();
			
		/* @var $queuepointer orm\Queuepointer */
		foreach ($queuepointers as $queuepointer)
		{
			$categoryId = $post->getCategory()->getId();
			$categoryName = \bot\TasksQueue::getCategoryName($categoryId);
			$text = bot\TasksQueue::getNewTasks($categoryName);
			
			$api->sendMessage([ 
				'chat_id' => $queuepointer->getRequester()->getChatId(),
				'parse_mode' => 'HTML',
				'text' => $text
			]);
		}
	}
}
