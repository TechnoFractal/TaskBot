<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Symfony\Component\Yaml\Yaml;

/**
 * Description of Dbhelper
 *
 * @author olga
 */
class Dbhelper 
{	
	private $db;
	
	public function __construct() {
		$configPath = dirname(dirname(__DIR__)) . '/config.yml';
		$config = Yaml::parse(file_get_contents($configPath));
		$this->db = $config["db"];
	}
	
	public function getDbConfig()
	{
		return $this->db;
	}
}
