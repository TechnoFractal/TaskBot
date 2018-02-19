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

/**
 * Description of Requesters
 *
 * @author Olga Pshenichnikova <olga@technofractal.org>
 */
class Requesters extends REST_Controller 
{
	public function index_get()
	{
		$orm = DoctrineORM::getORM();
		
		$sort = (array)json_decode($this->get("sort"), true);
		$range = (array)json_decode($this->get("range"), true);
		$filter = (array)json_decode($this->get("filter"), true);

		//print_r($filter); die();

		$repo = $orm->getRepository(orm\Requester::class);
		$resp = Telecriteria::getCriteria(
			$sort, 
			$range, 
			$filter, 
			$repo,
			new adapters\Requester()
		);

		$requesters = $resp[0];
		$suffix = $resp[1];
		$respHeader = "Content-Range: posts $suffix";

		$result = [];

		/* @var $requester orm\Requester */
		foreach ($requesters as $requester)
		{
			$result[] = $requester->toResult();
		}

		$this->output->set_header($respHeader);
		$this->set_response($result, REST_Controller::HTTP_OK);
	}
}
