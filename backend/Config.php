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
	public static function getConfig() : array
	{
		$configPath = __DIR__ . '/config.yml';
		return Yaml::parse(file_get_contents($configPath));
	}
}
