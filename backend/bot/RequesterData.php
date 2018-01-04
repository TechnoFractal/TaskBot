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

namespace bot;

use \Telegram\Bot\Objects\User;

/**
 * Description of RequesterData
 *
 * @author Olga Pshenichnikova <olga@technofractal.org>
 */
class RequesterData 
{
	private $id;
	private $firstName;
	private $lastName;
	private $userName;
	private $isBot;
	
	public function __construct(User $user) 
	{
		//error_log(print_r($user, 1)); die();
		
		$this->id = $user->getId();
		$this->firstName = $user->getFirstName();
		
		$this->lastName = "";
		
		if ($user->getLastName())
		{
			$this->lastName = $user->getLastName();
		}
		
		$this->userName = "";
		
		if ($user->getUsername())
		{
			$this->userName = $user->getUsername();
		}
		
		$this->isBot = $user->get("is_bot");
	}
	
	public function getId() : int
	{
		return $this->id;
	}
	
	public function getFirstName() : string
	{
		return $this->firstName;
	}
	
	public function getLastName() : string
	{
		return $this->lastName;
	}
	
	public function getUserName() : string
	{
		return $this->userName;
	}
	
	public function getIsBot() : bool
	{
		return $this->isBot;
	}
}
