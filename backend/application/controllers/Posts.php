<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Posts
 *
 * @author olga
 */
class Posts extends REST_Controller 
{
	public function index_get($id = null)
	{
		$orm = DoctrineORM::getORM();
		
		if ($id)
		{
			
		} else {
			$sort = $this->get("sort");
			$range = $this->get("range");
			$filter = $this->get("filter");
			
			$repo = $orm->getRepository('orm\Post');
			
			$criteria = Criteria::create()
				/*->where(Criteria::expr()->eq("birthday", "1982-02-17"))
				->orderBy(array("username" => Criteria::ASC))
				->setFirstResult(0)
				->setMaxResults(20)*/;

			$posts = $repo->matching($criteria);
			
			$this->output->set_header('Content-Range: posts 0-0/0');
			$this->set_response([$sort, $range, $filter], REST_Controller::HTTP_OK);
		}		
	}
}
