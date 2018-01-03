<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Yaml\Yaml;

class DoctrineORM
{
	// Create a simple "default" Doctrine ORM configuration for Annotations
	private $isDevMode = true;
	private $entityManager;
	
	public function __construct() 
	{
		$config = Setup::createAnnotationMetadataConfiguration(
			[__DIR__ . "/orm"], 
			$this->isDevMode
		);

		$configPath = __DIR__ . '/config.yml';
		$dbconfig = Yaml::parse(file_get_contents($configPath))["db"];

		//print_r($dbconfig); die();
		//print_r($config); die();

		$dbParams = array(
			'driver'   => $dbconfig["driver"],
			'user'     => $dbconfig["user"],
			'password' => $dbconfig["password"],
			'dbname'   => $dbconfig["db"]
		);

		// obtaining the entity manager
		$this->entityManager = EntityManager::create($dbParams, $config);		
	}
	
	/**
	 * 
	 * @return Doctrine\ORM\EntityManager
	 */
	public function getORM() : Doctrine\ORM\EntityManager
	{
		return $this->entityManager;
	}
}