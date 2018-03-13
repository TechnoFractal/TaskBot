<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace exceptions;

/**
 * Description of PostAlreadyPostponed
 *
 * @author Olga Pshenichnikova <olga@technofractal.org>
 */
class PostAlreadyPostponed extends KoshkaException
{
	public function __construct(\orm\Post $post) 
	{
		$message = sprintf("Post \"%s\" already postponed", $post->getTitle());
		parent::__construct($message);
	}
}
