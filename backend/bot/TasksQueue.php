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
	/**
	 *
	 * @var RequesterData 
	 */
	private $data;
	
	/**
	 *
	 * @var bool
	 */
	private $queueEmpty = false;
	
	/**
	 *
	 * @var \orm\Post
	 */
	private $post;
	
	public function __construct(RequesterData $data) 
	{
		$this->data = $data;
	}
	
	private function setEmpty()
	{
		$this->queueEmpty = true;
	}
	
	private function setPost(\orm\Post $post)
	{
		$this->queueEmpty = false;
		$this->post = $post;
	}
	
	public function isEmpty() : bool
	{
		return $this->queueEmpty;
	}
	
	private function checkRequester(EntityManager $orm) 
		: \orm\Requester
	{
		$expr = Criteria::expr();		
		
		/* @var $data RequesterData */
		$data = $this->data;
		
		$teleId = $data->getId();
		$criteria = Criteria::create();
		$criteria->where($expr->eq("tele_id", $teleId));
		
		$requester = $orm
			->getRepository(\orm\Requester::class)
			->matching($criteria)
			->first();
		
		if (!$requester)
		{
			$requester = new \orm\Requester();
			
			$requester->setIsBot($data->getIsBot());
			$requester->setTeleId($data->getId());
			$requester->setChatId($data->getChatId());
			$requester->setFirstName($data->getFirstName());
			$requester->setLastName($data->getLastName());
			$requester->setUserName($data->getUserName());

			$orm->persist($requester);
			$orm->flush();
		}
		
		return $requester;
	}
	
	private function getCategory(
		string $request,
		EntityManager $orm
	) : \orm\Category
	{
		$id = DataHelper::getCategoryId($request);

		$category = $orm
			->getRepository(\orm\Category::class)
			->find($id);
		
		if (!$category)
		{
			throw new \Exception("Category not found by id: " . $id);
		}
		
		return $category;
	}
	
	public function getPost() : \orm\Post
	{
		return $this->post;
	}
	
	public function start()
	{
		/* @var $requester \orm\Requester */
		$orm = \DoctrineORM::getORM();

		/* @var $requester \orm\Requester */
		$requester = $this->checkRequester($orm);
		$requester->enable();
		$orm->flush();
	}
	
	public function stop()
	{
		/* @var $requester \orm\Requester */
		$orm = \DoctrineORM::getORM();
		
		/* @var $requester \orm\Requester */
		$requester = $this->checkRequester($orm);
		$requester->disable();
		$orm->flush();
	}
	
	public function handleRequest(string $request) : string
	{		
		/* @var $requester \orm\Requester */
		$orm = \DoctrineORM::getORM();
		
		$category = $this->getCategory($request, $orm);
		
		/* @var $requester \orm\Requester */
		$requester = $this->checkRequester($orm);
		
		$expr = Criteria::expr();
		$criteria = Criteria::create();
		$criteria
			->where($expr->eq("category", $category))
			->andWhere($expr->eq("requester", $requester));
		
		/* @var $queuepointer \orm\Queuepointer */
		$queuepointer = $orm
			->getRepository(\orm\Queuepointer::class)
			->matching($criteria)
			->first();
		
		if (!$queuepointer)
		{
			$criteria = Criteria::create();
			$expr = Criteria::expr();
			$criteria
				->where(Criteria::expr()->eq("category", $category))
				->andWhere(Criteria::expr()->eq("deleted", false))
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
				$queuepointer = new \orm\Queuepointer();
				$queuepointer->setRequester($requester);
				$queuepointer->setCategory($category);
				$queuepointer->setPost($post);
				$queuepointer->setDate(new \DateTime('now'));
				
				$orm->persist($queuepointer);
				$orm->flush();
				
				$this->setPost($post);
				return $post->getText();
			} else {
				$this->setEmpty();
				return DataHelper::getNoTasks($request);
			}
		} else {
			$criteria = Criteria::create();
			$lastPostId = $queuepointer->getPost()->getId();
			
			$criteria
				->where(Criteria::expr()->eq("category", $category))
				->andWhere(Criteria::expr()->gt("id", $lastPostId))
				->andWhere(Criteria::expr()->eq("deleted", false))
				->orderBy(["id" => Criteria::ASC])
				->setMaxResults(1);
			
			/* @var $post \orm\Post */
			$post = $orm
				->getRepository(\orm\Post::class)
				->matching($criteria)
				->first();
			
			if ($post)
			{
				$queuepointer->setIsNotLast();
				$queuepointer->setPost($post);
				$orm->flush();
				
				$this->setPost($post);
				return $post->getText();
			} else {
				$queuepointer->setIsLast();
				$orm->flush();
				
				//error_log($request);
				$this->setEmpty();
				return DataHelper::getNoTasks($request);
			}
		}
	}
}
