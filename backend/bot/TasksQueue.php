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
	private function getRequester(
		int $teleId,
		EntityManager $orm
	) : \orm\Requester
	{
		$expr = Criteria::expr();		
		
		$criteria = Criteria::create();
		$criteria->where($expr->eq("tele_id", $teleId));
		
		$requester = $orm
			->getRepository(\orm\Requester::class)
			->matching($criteria)
			->first();
		
		if (!$requester)
		{
			return new \orm\Requester();
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
	
	public function handleRequest(
		string $request, 
		RequesterData $data) : string
	{
		//error_log('here ' . $request); die();
		
		$orm = \DoctrineORM::getORM();
		
		$category = $this->getCategory($request, $orm);
		
		/* @var $requester \orm\Requester */
		$requester = $this->getRequester($data->getId(), $orm);
		
		$requester->setIsBot($data->getIsBot());
		$requester->setTeleId($data->getId());
		$requester->setChatId($data->getChatId());
		$requester->setFirstName($data->getFirstName());
		$requester->setLastName($data->getLastName());
		$requester->setUserName($data->getUserName());
			
		if (!$requester->isLoaded())
		{
			$orm->persist($requester);
		}
		
		$orm->flush();
		
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
				$queuepointer = new \orm\Queuepointer();
				$queuepointer->setRequester($requester);
				$queuepointer->setCategory($category);
				$queuepointer->setPost($post);
				$queuepointer->setDate(new \DateTime('now'));
				
				$orm->persist($queuepointer);
				$orm->flush();
				
				return $post->getText();
			} else {
				return DataHelper::getNoTasks($request);
			}
		} else {
			$criteria = Criteria::create();
			$expr = Criteria::expr();
			$lastPostId = $queuepointer->getPost()->getId();
			
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
				$queuepointer->setIsNotLast();
				$queuepointer->setPost($post);
				$orm->flush();
				
				return $post->getText();
			} else {
				$queuepointer->setIsLast();
				$orm->flush();
				
				//error_log($request);
				return DataHelper::getNoTasks($request);
			}
		}
	}
}
