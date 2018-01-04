<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use Doctrine\Common\Collections\Criteria;

/**
 * Description of Posts
 *
 * @author olga
 */
class Posts extends REST_Controller 
{
	public function index_post()
	{
		$title = $this->post("title");
		$categoryId = $this->post("categoryId");
		$text = $this->post("text");
		
		if (!$title || !$categoryId || !$text)
		{
			$this->set_response(null, REST_Controller::HTTP_NOT_ACCEPTABLE);
		}
		
		$orm = DoctrineORM::getORM();
		$category = 
			$orm
				->getRepository('orm\Category')
				->find($categoryId);
		
		if (!$categoryId)
		{
			$this->set_response(null, REST_Controller::HTTP_NOT_ACCEPTABLE);
		}
		
		$post = new orm\Post();
		$post->setCategory($category);
		$post->setCreated(new DateTime('now'));
		$post->setText($text);
		$post->setTitle($title);
		
		$orm->persist($post);
		$orm->flush();
		
		$id = $post->getId();
		
		$result = [
			'id' => $id,
			'title' => $post->getTitle(),
			'text' => $post->getText(),
			'created' => $post->getCreated(),
			'categoryId' => $post->getCategory()->getId()
		];
		
		$respHeader = "Location: /posts/$id";
			
		//print_r((array)$result); die();

		$this->output->set_header($respHeader);
		$this->set_response($result, REST_Controller::HTTP_CREATED);
	}
	
	public function index_get($id = null)
	{
		$orm = DoctrineORM::getORM();
		
		if ($id)
		{
			/* @var $category orm\Post */
			$post = $orm
				->getRepository('orm\Post')
				->find($id);
			
			$result = [];
			
			if ($post)
			{
				$result = [
					'id' => $post->getId(),
					'title' => $post->getTitle(),
					'categoryId' => $post->getCategory()->getId(),
					'text' => $post->getText(),
					'created' => $post->getCreated()->format('Y-m-d')
				];
			}
			
			$this->set_response($result, REST_Controller::HTTP_OK);			
		} else {
			$sort = (array)json_decode($this->get("sort"), true);
			$range = (array)json_decode($this->get("range"), true);
			$filter = (array)json_decode($this->get("filter"), true);
			
			//print_r($filter); die();
			
			$repo = $orm->getRepository(orm\Post::class);
			$resp = Telecriteria::getCriteria($sort, $range, $filter, $repo);
			
			$posts = $resp[0];
			$suffix = $resp[1];
			$respHeader = "Content-Range: posts $suffix";
			
			$result = [];
			
			/* @var $post orm\Post */
			foreach ($posts as $post)
			{
				$result[] = [
					'id' => $post->getId(),
					'categoryId' => $post->getCategory()->getId(),
					'created' => $post->getCreated()->format('Y-m-d'),
					'title' => $post->getTitle(),
					'text' => $post->getText()
				];
			}
			
			$this->output->set_header($respHeader);
			$this->set_response($result, REST_Controller::HTTP_OK);
		}		
	}
}
