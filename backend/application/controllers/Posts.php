<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Posts
 *
 * @author olga
 */
class Posts extends REST_Controller 
{
	public function index_delete(int $id)
	{		
		$orm = DoctrineORM::getORM();

		/* @var $post orm\Post */
		$post = $orm
				->getRepository(orm\Post::class)
				->find($id);

		if ($post) {
			$result = $post->toResult();
			$orm->remove($post);
			$orm->flush();
			
			$this->set_response($result, REST_Controller::HTTP_OK);
		} else {
			$this->set_response(null, REST_Controller::HTTP_NOT_FOUND);
		}
		
	}
	
	public function index_put($id)
	{		
		$categoryId = $this->put("categoryId");
		$title = $this->put("title");
		$text = $this->put("text");
		$created = $this->put("created");
		
		if (!$title || 
			!$categoryId ||
			!$created ||
			!$text)
		{
			$this->set_response(null, REST_Controller::HTTP_NOT_ACCEPTABLE);
			return;
		}
		
		$orm = DoctrineORM::getORM();
			
		/* @var $post orm\Post */
		$post = $orm
			->getRepository(orm\Post::class)
			->find($id);
		
		/* @var $category orm\Category */
		$category = $orm
			->getRepository(orm\Category::class)
			->find($categoryId);
			
		if (!$category || !$post)
		{
			$this->set_response(null, REST_Controller::HTTP_NOT_FOUND);
			return;
		}

		$post->setTitle($title);
		$post->setCategory($category);
		$post->setText($text);
		$post->setCreated(new DateTime($created));
		
		$orm->flush();
		
		$result = $post->toResult();
		
		$this->set_response($result, REST_Controller::HTTP_OK);
	}

	public function index_post()
	{
		$title = $this->post("title");
		$categoryId = $this->post("categoryId");
		$text = $this->post("text");
		
		if (!$title || !$categoryId || !$text)
		{
			$this->set_response(null, REST_Controller::HTTP_NOT_ACCEPTABLE);
			return;
		}
		
		$orm = DoctrineORM::getORM();
		$category = 
			$orm
				->getRepository('orm\Category')
				->find($categoryId);
		
		if (!$categoryId)
		{
			$this->set_response(null, REST_Controller::HTTP_NOT_ACCEPTABLE);
			return;
		}
		
		$post = new orm\Post();
		$post->setCategory($category);
		$post->setCreated(new DateTime('now'));
		$post->setText($text);
		$post->setTitle($title);
		
		$orm->persist($post);
		$orm->flush();
		
		$id = $post->getId();
		
		$result = $post->toResult();
		
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
				->getRepository(orm\Post::class)
				->find($id);
			
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
			$resp = Telecriteria::getCriteria($sort, $range, $filter, $repo);
			
			$posts = $resp[0];
			$suffix = $resp[1];
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
