<?php

/* 
 * Copyright (C) 2018 Olga Pshenichnikova <o.pshenichnikova@be-interactive.ru>
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

use \Gender\Gender;
use \Telegram\Bot\Objects\User;

class DataHelper
{
	const LIGHT_TASKS = "Легкие задания";
	const MIDDLE_TASKS = "Средние задания";
	const HARD_TASKS = "Сложные задания";
	const INFO = "Инфа";
	const CONTACT = "Связь";
	
	const FILE_DEFAULT = "default";
	const FILE_NOTFOUND = "notfound";
	const FILE_HELLO = "hello";
	const FILE_HI = "hi";
	const FILE_BYE = "bye";
	const FILE_START = "start";
	const FILE_INFO = "info";
	const FILE_CONTACTS = "contacts";
	const FILE_NOTASKS = "notasks";
	const FILE_NEWTASKS = "newtask";	
	
	private static function isMale(string $name) : bool
	{
		switch($name) {
			case Gender::IS_FEMALE:
				return false;
			case Gender::IS_MOSTLY_FEMALE:
				return false;
			case Gender::IS_MALE:
				return true;
			case Gender::IS_MOSTLY_MALE:
				return true;
			case Gender::IS_UNISEX_NAME:
				return true;
			case Gender::IS_A_COUPLE:
				return true;
			case Gender::NAME_NOT_FOUND:
				return false;
			case Gender::ERROR_IN_NAME:
				return false;
			default:
				return false;
		}
	}
	
	private static function isGenderize(string $name) 
	{
		switch($name) {
			case Gender::IS_FEMALE:
				return true;
			case Gender::IS_MOSTLY_FEMALE:
				return true;
			case Gender::IS_MALE:
				return true;
			case Gender::IS_MOSTLY_MALE:
				return true;
			case Gender::IS_UNISEX_NAME:
				return true;
			case Gender::IS_A_COUPLE:
				return true;
			case Gender::NAME_NOT_FOUND:
				return false;
			case Gender::ERROR_IN_NAME:
				return false;
			default:
				return false;
		}		
	}
	
	public static function getIsFemale(User $user) : bool {
		$name = $user->getFirstName() ? $user->getFirstName() : "";
		$family = $user->getLastName() ? $user->getLastName() : "";
		$username = $user->getUsername() ? $user->getUsername() : "";
		
		$gender = new Gender();
		$nameResult = $gender->get($name, Gender::RUSSIA);
		$familyResult = $gender->get($family, Gender::RUSSIA);
		$usernameResult = $gender->get($username, Gender::RUSSIA);
		
		if (self::isGenderize($nameResult) || 
			self::isGenderize($familyResult) || 
			self::isGenderize($usernameResult)) 
		{
			return 
				(self::isGenderize($nameResult) && 
					!self::isMale($nameResult)) ||
				(self::isGenderize($familyResult) && 
					!self::isMale($familyResult)) ||
				(self::isGenderize($usernameResult) && 
					!self::isMale($usernameResult));
		} else {
			return (
				mb_substr($name, -1) ==  'а' ||
				mb_substr($family, -1) ==  'а' ||
				mb_substr($username, -1) ==  'а'
			);
		}
	}
	
	public static function getCategoryId(string $request) : int
	{
		switch ($request) {
			case self::LIGHT_TASKS:
				return 1;
			case self::MIDDLE_TASKS:
				return 2;
			case self::HARD_TASKS:
				return 3;
			default:
				throw new \Exception("Unhandled category: " . $request);
		}
	}
	
	public static function getCategoryName(int $categoryId) : string
	{
		switch ($categoryId) {
			case 1:
				return self::LIGHT_TASKS;
			case 2:
				return self::MIDDLE_TASKS;
			case 3:
				return self::HARD_TASKS;
			default:
				throw new \Exception("Unhandled category: " . $categoryId);
		}
	}

	private static function getData(string $fileName) : string
	{
		$text = file_get_contents(ROOT. "/bot/data/${fileName}.html");
		$text = str_replace("\n", "", $text);
		return str_replace("\\n", "\r\n", $text);
	}

	public static function getDefault() : string
	{
		return self::getData(self::FILE_DEFAULT);
	}
	
	public static function getNotFound(bool $isMale) : string
	{
		$text = self::getData(self::FILE_NOTFOUND);
		$gender = $isMale ? 'Молодой человек' : 'Уважаемая дама';
		return str_replace('{gender}', $gender, $text);
	}
	
	public static function getHi(string $username) : string
	{
		$text = self::getData(self::FILE_HI);
		return str_replace('{username}', $username, $text);
	}
	
	public static function getBye(string $username, bool $isMale) : string
	{
		$text = self::getData(self::FILE_BYE);
		$pronomen = $isMale ? 'он' : 'она';
		$leave = $isMale ? 'ушёл' : 'ушла';
		
		$text = str_replace('{username}', $username, $text);
		$text = str_replace('{pronomen}', $pronomen, $text);
		return str_replace('{leave}', $leave, $text);
	}
	
	public static function getHello() : string
	{
		return self::getData(self::FILE_HELLO);
	}
	
	public static function getStart() : string
	{
		return self::getData(self::FILE_START);
	}
	
	public static function getInfo() : string
	{
		return self::getData(self::FILE_INFO);
	}
	
	public static function getContacts() : string
	{
		return self::getData(self::FILE_CONTACTS);
	}
	
	public static function getNoTasks(string $title) : string
	{
		$text = self::getData(self::FILE_NOTASKS);
		return str_replace('{type}', $title, $text);
	}
	
	public static function getNewTasks(string $title) : string
	{
		$text = self::getData(self::FILE_NEWTASKS);
		return str_replace('{type}', $title, $text);
	}
}