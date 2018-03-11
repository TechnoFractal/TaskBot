<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace exceptions;

/**
 * Description of PostAlreadyDone
 *
 * @author Olga Pshenichnikova <olga@technofractal.org>
 */
class PostAlreadyDone extends KoshkaException
{
	public function __construct(\orm\Post $post) 
	{
		$message = sprintf("Task \"%s\" already done", $post->getTitle());
		parent::__construct($message);
	}
}
