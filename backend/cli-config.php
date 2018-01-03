<?php
// cli-config.php
require_once "vendor/autoload.php";
//require_once "DoctrineORM.php";

$orm = new DoctrineORM();

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet(
	$orm->getORM()
);