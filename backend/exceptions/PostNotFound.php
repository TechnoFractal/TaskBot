<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace exceptions;

/**
 * Description of PostNotFound
 *
 * @author Olga Pshenichnikova <olga@technofractal.org>
 */
class PostNotFound extends KoshkaException
{
	public function __construct(int $postid) 
	{
		parent::__construct(sprintf("Post %d not found", $postid));
	}
}
