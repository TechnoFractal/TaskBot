<?php

include('../vendor/autoload.php');

/* @var $config Config */
$config = new Config();
$koshkaBot = new bot\KoshkaBot($config);

try {
	$koshkaBot->handleUpdate();
} catch (Exception $e) {
	error_log($e->getMessage());
	error_log($e->getTraceAsString());
	//echo $e->getMessage();
}
