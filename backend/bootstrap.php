<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once "vendor/autoload.php";

// Create a simple "default" Doctrine ORM configuration for Annotations
$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration(
	[__DIR__ . "/orm"], 
	$isDevMode
);

//var_dump($config); die();

$dbParams = array(
    'driver'   => 'pdo_mysql',
    'user'     => 'telegrammbot',
    'password' => '123456',
    'dbname'   => 'telegrammbot'
);

// obtaining the entity manager
$entityManager = EntityManager::create($dbParams, $config);

//var_dump($entityManager); die();