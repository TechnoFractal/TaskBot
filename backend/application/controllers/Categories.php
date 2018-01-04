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

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Categories
 *
 * @author Olga Pshenichnikova <olga@technofractal.org>
 */
class Categories extends REST_Controller 
{
	public function index_put($id)
	{		
		$title = $this->put("title");
		
		if (!$title)
		{
			$this->set_response(null, REST_Controller::HTTP_NOT_ACCEPTABLE);
			return;
		}
		
		$orm = DoctrineORM::getORM();
			
		/* @var $category orm\Category */
		$category = $orm
			->getRepository(orm\Category::class)
			->find($id);
			
		if (!$category)
		{
			$this->set_response(null, REST_Controller::HTTP_NOT_FOUND);
			return;
		}
		
		$category->setTitle($title);
		$orm->flush();
		
		$result = $category->toResult();
		
		$this->set_response($result, REST_Controller::HTTP_OK);
	}

	public function index_get($id = null)
	{
		$orm = DoctrineORM::getORM();
		
		if ($id)
		{
			/* @var $category orm\Category */
			$category = $orm
				->getRepository(orm\Category::class)
				->find($id);
			
			$result = [];
			
			if ($category)
			{
				$result = $category->toResult();
				$this->set_response($result, REST_Controller::HTTP_OK);
			} else {
				$this->set_response(null, REST_Controller::HTTP_NOT_FOUND);
			}
		} else {
			$sort = (array)json_decode($this->get("sort"), true);
			$range = (array)json_decode($this->get("range"), true);
			$filter = (array)json_decode($this->get("filter"), true);
			
			//print_r($filter); die();
			
			$repo = $orm->getRepository(orm\Category::class);
			$resp = Telecriteria::getCriteria($sort, $range, $filter, $repo);
			
			$categories = $resp[0];
			$suffix = $resp[1];
			$respHeader = "Content-Range: posts $suffix";
			
			$result = [];
			
			/* @var $category orm\Category */
			foreach ($categories as $category)
			{
				$result[] = $category->toResult();
			}
			
			$this->output->set_header($respHeader);
			$this->set_response($result, REST_Controller::HTTP_OK);
		}		
	}
}
