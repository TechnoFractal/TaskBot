<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Sessions
 *
 * @author Olga Pshenichnikova <olga@technofractal.org>
 */
class Sessions extends REST_Controller 
{
	public function index_delete(int $id)
	{		
		$orm = DoctrineORM::getORM();

		/* @var $session orm\Session */
		$session = $orm
				->getRepository(orm\Session::class)
				->find($id);

		if ($session) {
			$result = $session->toResult();
			$orm->remove($session);
			$orm->flush();
			
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
			/* @var $session orm\Session */
			$session = $orm
				->getRepository(orm\Session::class)
				->find($id);
			
			$result = [];
			
			if ($session)
			{
				$result = $session->toResult();
				$this->set_response($result, REST_Controller::HTTP_OK);
			} else {
				$this->set_response(null, REST_Controller::HTTP_NOT_FOUND);
			}	
		} else {
			$sort = (array)json_decode($this->get("sort"), true);
			$range = (array)json_decode($this->get("range"), true);
			$filter = (array)json_decode($this->get("filter"), true);
			
			//print_r($filter); die();
			
			$repo = $orm->getRepository(orm\Session::class);
			$resp = Telecriteria::getData(
				$sort, 
				$range, 
				$filter, 
				$repo,
				new adapters\Session()
			);
			
			$sessions = $resp[0];
			$suffix = $resp[1];
			$respHeader = "Content-Range: posts $suffix";
			
			$result = [];
			
			/* @var $session orm\Session */
			foreach ($sessions as $session)
			{
				$result[] = $session->toResult();
			}
			
			$this->output->set_header($respHeader);
			$this->set_response($result, REST_Controller::HTTP_OK);
		}		
	}
}