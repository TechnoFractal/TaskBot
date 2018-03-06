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

namespace libraries;

use \Doctrine\Common\Collections\Criteria;
use \Doctrine\Common\Collections\Expr\Expression;
use \Doctrine\ORM\EntityRepository;
use \adapters\JsonDBAdapter;

/**
 * Description of TeleCategory
 *
 * @author Olga Pshenichnikova <olga@technofractal.org>
 */
class Telecriteria
{	
	/**
	 *
	 * @var array
	 */
	private $sort;
	
	/**
	 *
	 * @var array
	 */
	private $range;
	
	/**
	 *
	 * @var Expression
	 */
	private $filter;
	
	/**
	 *
	 * @var EntityRepository
	 */	
	private $repo;
	
	/**
	 *
	 * @var \adapters\JsonDBAdapter
	 */
	private $adapter;
	
	/**
	 *
	 * @var Criteria
	 */
	private $criteria;
	
	/**
	 *
	 * @var string
	 */
	private $suffix;
	
	public function __construct(
		EntityRepository $repo,
		JsonDBAdapter $adapter
	) {
		$this->repo = $repo;
		$this->adapter = $adapter;
		$this->sort = null;
		$this->range = null;
		$this->filter = null;
	}
	
	public function setSort(array $sort) 
	{
		$this->sort = $sort;
	}

	public function setRange(array $range) 
	{
		$this->range = $range;
	}

	public function setFilter(array $filter) 
	{
		if ($filter)
		{
			//$filter->where($filter);
		}
	}
	
	public function getCriteria() : Criteria
	{
		return $this->criteria;
	}
	
	public function compile() 
	{
		/* @var $criteria Criteria */
		$criteria = Criteria::create();
		
		if ($this->filter)
		{
			//$criteria->where($filter);
		}
		
		if ($this->sort)
		{
			$sortBy = $this->sort[0];
			$sortOrder = $this->sort[1];
			
			$sorting = [
				$this->adapter->getDbField($sortBy) => $sortOrder
			];
			
			//echo $sortBy; die();
			$criteria->orderBy($sorting);
		}
		
		$this->criteria = $criteria;
	}
	
	public function getData() : array
	{		
		$from = 0;
		$count = $this->repo->matching($this->criteria)->count();
		
		if ($this->range)
		{
			$from = $this->range[0];
			$to = $this->range[1];
			$this->criteria
				->setFirstResult($from)
				->setMaxResults($to - $from);
		}
		
		$resp = $this->repo->matching($this->criteria)->toArray();
		
		$to = $from + $count;		
		$this->suffix = "$from-$to/$count";
		
		return $resp;
	}
	
	public function getSuffix() : string
	{
		return $this->suffix;
	}
}
