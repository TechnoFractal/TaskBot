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

use \Doctrine\Common\Collections\Criteria;
use \Doctrine\ORM\EntityManager;

/**
 * Description of TasksQueue
 *
 * @author Olga Pshenichnikova <olga@technofractal.org>
 */
class TasksQueue 
{
	const LIGHT_TASKS = "Легкие задания";
	const MIDDLE_TASKS = "Средние задания";
	const HARD_TASKS = "Сложные задания";
	const INFO = "Инфа";
	const CONTACT = "Связь";
	
	const FILE_START = "start";
	const FILE_INFO = "info";
	const FILE_CONTACTS = "contacts";
	const FILE_NOTASKS = "notasks";
	
	private static function getData(string $fileName) : string
	{
		$text = file_get_contents(ROOT. "/bot/data/${fileName}.html");
		$text = str_replace("\n", "", $text);
		return str_replace("\\n", "\r\n", $text);
	}

	public static function getStart() : string
	{
		return self::getData(self::FILE_START);
	}
	
	public static function getInfo() : string
	{
		return self::getData(self::FILE_INFO);
	}
	
	public static function getContacts() : string
	{
		return self::getData(self::FILE_CONTACTS);
	}
	
	public static function getNoTasks(string $title) : string
	{
		$text = self::getData(self::FILE_NOTASKS);
		return str_replace('{type}', $title, $text);
	}
	
	private function getCategoryId(string $request) : int
	{
		switch ($request) {
			case self::LIGHT_TASKS:
				return 1;
			case self::MIDDLE_TASKS:
				return 2;
			case self::HARD_TASKS:
				return 3;
			default:
				throw new \Exception("Unhandled category: " . $request);
		}
	}
	
	private function getCategory(
		string $request,
		EntityManager $orm
	) : \orm\Category
	{
		$id = $this->getCategoryId($request);

		$category = $orm
			->getRepository(\orm\Category::class)
			->find($id);
		
		if (!$category)
		{
			throw new \Exception("Category not found");
		}
		
		return $category;
	}
	
	public function handleRequest(
		string $request, 
		\bot\RequesterData $data) : string
	{
		//error_log('here ' . $request); die();
		
		$orm = \DoctrineORM::getORM();
		
		$category = $this->getCategory($request, $orm);
		
		$expr = Criteria::expr();		
		
		$criteria = Criteria::create();
		$criteria
			->where($expr->eq("category", $category))
			->andWhere($expr->eq("tele_id", $data->getId()));
			//->orderBy(["post_id" => Criteria::DESC])
			//->setMaxResults(1);
		
		/* @var $requester \orm\Requester */
		$requester = $orm
			->getRepository(\orm\Requester::class)
			->matching($criteria)
			->first();
		
		if (!$requester)
		{
			$criteria = Criteria::create();
			$expr = Criteria::expr();
			$criteria
				->where($expr->eq("category", $category))
				->orderBy(["id" => Criteria::ASC])
				->setMaxResults(1);
			
			/* @var $post \orm\Post */
			$post = $orm
				->getRepository(\orm\Post::class)
				->matching($criteria)
				->first();
			
			if ($post)
			{
				//error_log();
				
				$requester = new \orm\Requester();
				$requester->setCategory($category);
				$requester->setDate(new \DateTime('now'));
				$requester->setFirstName($data->getFirstName());
				$requester->setIsBot($data->getIsBot());
				$requester->setLastName($data->getLastName());
				$requester->setPost($post);
				$requester->setTeleId($data->getId());
				$requester->setUserName($data->getUserName());
				
				$orm->persist($requester);
				$orm->flush();
				
				return $post->getText();
			} else {
				return self::getNoTasks($request);
			}
		} else {
			$criteria = Criteria::create();
			$expr = Criteria::expr();
			$lastPostId = $requester->getPost()->getId();
			$criteria
				->where($expr->eq("category", $category))
				->andWhere($expr->gt("id", $lastPostId))
				->orderBy(["id" => Criteria::ASC])
				->setMaxResults(1);
			
			/* @var $post \orm\Post */
			$post = $orm
				->getRepository(\orm\Post::class)
				->matching($criteria)
				->first();
			
			if ($post)
			{
				$requester->setPost($post);
				$orm->flush();
				
				return $post->getText();
			} else {
				//error_log($request);
				return self::getNoTasks($request);
			}
		}
	}
}
