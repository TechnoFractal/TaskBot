<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Users
 *
 * @author Olga Pshenichnikova <olga@technofractal.org>
 */
class Users extends REST_Controller 
{
	public function index_delete(int $id)
	{		
		$orm = DoctrineORM::getORM();

		/* @var $post orm\User */
		$post = $orm
				->getRepository(orm\User::class)
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
	
	public function index_get($id = null)
	{
		$orm = DoctrineORM::getORM();
		
		if ($id)
		{
			/* @var $user orm\User */
			$user = $orm
				->getRepository(orm\User::class)
				->find($id);
			
			$result = [];
			
			if ($user)
			{
				$result = $user->toResult();
				$this->set_response($result, REST_Controller::HTTP_OK);
			} else {
				$this->set_response(null, REST_Controller::HTTP_NOT_FOUND);
			}	
		} else {
			$sort = (array)json_decode($this->get("sort"), true);
			$range = (array)json_decode($this->get("range"), true);
			$filter = (array)json_decode($this->get("filter"), true);
			
			//print_r($filter); die();
			
			$repo = $orm->getRepository(orm\User::class);
			$resp = Telecriteria::getData(
				$sort, 
				$range, 
				$filter, 
				$repo,
				new adapters\User()
			);
			
			$users = $resp[0];
			$suffix = $resp[1];
			$respHeader = "Content-Range: posts $suffix";
			
			$result = [];
			
			/* @var $user orm\User */
			foreach ($users as $user)
			{
				$result[] = $user->toResult();
			}
			
			$this->output->set_header($respHeader);
			$this->set_response($result, REST_Controller::HTTP_OK);
		}		
	}
}