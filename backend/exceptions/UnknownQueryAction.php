<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace exceptions;

/**
 * Description of UnknownQueryAction
 *
 * @author Olga Pshenichnikova <olga@technofractal.org>
 */
class UnknownQueryAction extends KoshkaException
{
	public function __construct(string $action) 
	{
		parent::__construct(sprintf("UnknownQueryAction: %s", $action));
	}
}
