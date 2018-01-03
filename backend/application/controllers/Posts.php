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
		if ($id)
		{
			
		} else {
			$sort = $this->get("sort");
			$range = $this->get("range");
			$filter = $this->get("filter");
			
			$this->output->set_header('Content-Range: posts 0-0/0');
			$this->set_response([$sort, $range, $filter], REST_Controller::HTTP_OK);
		}		
	}
}
