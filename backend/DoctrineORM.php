<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Yaml\Yaml;

class DoctrineORM
{
	// Create a simple "default" Doctrine ORM configuration for Annotations
	const IS_DEV_MODE = true;
	
	/**
	 * 
	 * @return Doctrine\ORM\EntityManager
	 */
	public static function getORM() : Doctrine\ORM\EntityManager
	{
		$config = Setup::createAnnotationMetadataConfiguration(
			[__DIR__ . "/orm"], 
			self::IS_DEV_MODE
		);

		$configPath = __DIR__ . '/config.yml';
		$dbconfig = Yaml::parse(file_get_contents($configPath))["db"];

		//print_r($dbconfig); die();
		//print_r($config); die();

		$dbParams = array(
			'driver'	=> $dbconfig["driver"],
			'user'		=> $dbconfig["user"],
			'password'	=> $dbconfig["password"],
			'dbname'	=> $dbconfig["db"],
			'charset'	=> 'UTF8mb4',
			'options'	=> [ 
				1002 => "SET NAMES 'UTF8mb4'"
			]
		);

		// obtaining the entity manager
		return EntityManager::create($dbParams, $config);
	}
}