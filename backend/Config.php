<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define("ROOT", dirname(__FILE__));

use Symfony\Component\Yaml\Yaml;

/**
 * Description of Config
 *
 * @author Olga Pshenichnikova <olga@technofractal.org>
 */
class Config 
{
	/**
	 *
	 * @var array
	 */
	private $config;
	
	/**
	 *
	 * @var string
	 */
	private $token;
	
	/**
	 *
	 * @var int
	 */
	private $id;
	
	/**
	 *
	 * @var array
	 */
	private $db;
	
	public function __construct() 
	{
		$configPath = __DIR__ . '/config.yml';
		$config = Yaml::parse(file_get_contents($configPath));
		$token = $config["token"];
		$id = explode(':', $token)[0];
		$this->config = $config;
		$this->token = $token;
		$this->id = (int)$id;
		$this->db = $config["db"];
	}
	
	public function getConfig() : array
	{
		return $this->config;
	}
	
	public function getToken() : string
	{
		return $this->token;
	}
	
	public function getBotId() : int
	{
		return $this->id;
	}
	
	public function getDB() : array
	{
		return $this->db;
	}
}
