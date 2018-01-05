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

namespace adapters;

/**
 * Description of Requester
 *
 * @author Olga Pshenichnikova <olga@technofractal.org>
 */
class Requester implements JsonDBAdapter
{
	public function getDbField(string $apiField): string 
	{
		switch ($apiField) {
			case 'teleId':
				return 'tele_id';
			case 'isBot':
				return 'is_bot';
			case 'firstName':
				return 'first_name';
			case 'lastName';
				return 'last_name';
			case 'userName':
				return 'user_name';
			case 'accessDate':
				return 'access_date';
			case 'categoryId':
				return 'category';
			case 'postId':
				return 'post';
			default:
				return $apiField;
		}
	}
}
