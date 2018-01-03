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

use Doctrine\Common\Collections\Criteria;

/**
 * Description of Categories
 *
 * @author Olga Pshenichnikova <olga@technofractal.org>
 */
class Categories extends REST_Controller 
{
	public function index_get($id = null)
	{
		$orm = DoctrineORM::getORM();
		
		if ($id)
		{
			
		} else {
			$sort = json_decode($this->get("sort"));
			$range = json_decode($this->get("range"));
			$from = $range[0];
			$to = $range[1];
			$sortBy = $sort[0];
			$sortOrder = $sort[1];
			$sorting = [$sortBy => $sortOrder];
			
			//print_r($sort);print_r($range); die();
			
			$repo = $orm->getRepository('orm\Category');
			
			$criteria = Criteria::create()
				->orderBy($sorting)
				->setFirstResult($from)
				->setMaxResults($to - $from);

			$categories = $repo->matching($criteria)->toArray();
			
			$result = [];
			
			/* @var $category orm\Category */
			foreach ($categories as $category)
			{
				$result[] = [
					'id' => $category->getId(),
					'title' => $category->getTitle()
				];
			}
			
			$count = count($result);
			$respHeader = "Content-Range: posts $from-$to/$count";
			
			//print_r((array)$result); die();
			
			$this->output->set_header($respHeader);
			$this->set_response($result, REST_Controller::HTTP_OK);
		}		
	}
}
