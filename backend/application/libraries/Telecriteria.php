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

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;

/**
 * Description of TeleCategory
 *
 * @author Olga Pshenichnikova <olga@technofractal.org>
 */
class Telecriteria
{
	public static function getCriteria(
		array $sort, 
		array $range, 
		array $filter,
		EntityRepository $repo,
		adapters\JsonDBAdapter $adapter
	) : array
	{		
		/* @var $criteria Criteria */
		$criteria = Criteria::create();
		
		$from = 0;
		
		if ($range)
		{
			$from = $range[0];
			$to = $range[1];
			$criteria
				->setFirstResult($from)
				->setMaxResults($to - $from);
		}
	
		if ($filter)
		{
			//$criteria->where(Criteria::expr()->eq("birthday", "1982-02-17"));
		}
		
		if ($sort)
		{
			$sortBy = $sort[0];
			$sortOrder = $sort[1];
			
			$sorting = [
				$adapter->getDbField($sortBy) => $sortOrder
			];
			
			//echo $sortBy; die();
			$criteria->orderBy($sorting);
		}
		
		$resp = $repo->matching($criteria)->toArray();
		$count = count($resp);
		$to = $from + $count;
		
		$suffix = "$from-$to/$count";
		
		return [
			$resp,
			$suffix
		];
	}
}
