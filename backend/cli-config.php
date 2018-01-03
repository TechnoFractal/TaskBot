<?php
// cli-config.php
require_once "vendor/autoload.php";
//require_once "DoctrineORM.php";

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet(
	DoctrineORM::getORM()
);