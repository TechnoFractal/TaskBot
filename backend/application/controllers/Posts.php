<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use Doctrine\Common\Collections\Criteria;
use libraries\Postinformer;
/**
 * Description of Posts
 *
 * @author olga
 */
class Posts extends REST_Controller 
{
	private function getCriteria(int $id) : Criteria
	{
		return Criteria::create()
			->where(Criteria::create()->expr()->eq('deleted', false))
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
			$post->delete();
			$orm->flush();
			$result = $post->toResult();			
			
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
			->matching($this->getCriteria($id))
			->first();
		
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
		
		/* @var $config Config */
		$config = new Config();
		
		/* @var $postinformer Postinformer */
		$postinformer = new Postinformer($config);
		
		$postinformer->informRequesters($orm, $post);
		
		$id = $post->getId();
		$result = $post->toResult();
		
		$respHeader = "Location: /posts/$id";

		$this->output->set_header($respHeader);
		$this->set_response($result, REST_Controller::HTTP_CREATED);
	}
	
	public function index_get($id = null)
	{
		//die("test");
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
			//var_dump($repo); die();
			$telecriteria = new libraries\Telecriteria($repo, new adapters\Post());
			$telecriteria->setSort($sort);
			$telecriteria->setRange($range);
			$telecriteria->setFilter($filter);
			$telecriteria->compile();
			
			/* @var $criteria Criteria */
			$criteria = $telecriteria->getCriteria();
			$criteria->andWhere(Criteria::expr()->eq('deleted', false));
			
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