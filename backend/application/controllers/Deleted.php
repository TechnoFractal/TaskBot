<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

defined('BASEPATH') OR exit('No direct script access allowed');

use Doctrine\Common\Collections\Criteria;

/**
 * Description of Deleted
 *
 * @author Olga Pshenichnikova <olga@technofractal.org>
 */
class Deleted extends REST_Controller 
{
	private function getCriteria(int $id) : Criteria
	{
		return Criteria::create()
			->where(Criteria::create()->expr()->eq('deleted', true))
			->andWhere(Criteria::create()->expr()->eq('id', $id));	
	}
	
	public function index_delete(int $id)
	{		
		$orm = DoctrineORM::getORM();

		/* @var $post orm\Post */
		$post = $orm
			->getRepository(orm\Post::class)
			->matching($this->getCriteria($id))
			->first();

		if ($post) {
			$post->restore();
			$orm->flush();
			$result = $post->toResult();			
			
			$this->set_response($result, REST_Controller::HTTP_OK);
		} else {
			$this->set_response(null, REST_Controller::HTTP_NOT_FOUND);
		}
	}	
	
	public function index_get($id = null)
	{
		$orm = DoctrineORM::getORM();
		
		if ($id)
		{
			/* @var $post orm\Post */
			$post = $orm
				->getRepository(orm\Post::class)
				->matching($this->getCriteria($id))
				->first();
			
			$result = [];
			
			if ($post)
			{
				$result = $post->toResult();
				$this->set_response($result, REST_Controller::HTTP_OK);
			} else {
				$this->set_response(null, REST_Controller::HTTP_NOT_FOUND);
			}	
		} else {
			$sort = (array)json_decode($this->get("sort"), true);
			$range = (array)json_decode($this->get("range"), true);
			$filter = (array)json_decode($this->get("filter"), true);
			
			//print_r($filter); die();
			
			$repo = $orm->getRepository(orm\Post::class);
			$telecriteria = new libraries\Telecriteria(
				$repo, 
				new adapters\Post()
			);
			
			$telecriteria->setFilter($filter);
			$telecriteria->setRange($range);
			$telecriteria->setSort($sort);
			$telecriteria->compile();
			
			/* @var $criteria Criteria */
			$criteria = $telecriteria->getCriteria();
			$criteria->andWhere(Criteria::expr()->eq('deleted', true));
			
			$posts = $telecriteria->getData();
			$suffix = $telecriteria->getSuffix();
			$respHeader = "Content-Range: posts $suffix";
			
			$result = [];
			
			/* @var $post orm\Post */
			foreach ($posts as $post)
			{
				$result[] = $post->toResult();
			}
			
			$this->output->set_header($respHeader);
			$this->set_response($result, REST_Controller::HTTP_OK);
		}		
	}
}
