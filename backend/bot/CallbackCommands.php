<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace bot;

use \Telegram\Bot\Objects\Update;
use \Telegram\Bot\Objects\User;

/**
 * Description of CallbackCommands
 *
 * @author Olga Pshenichnikova <olga@technofractal.org>
 */
class CallbackCommands 
{	
	/**
	 *
	 * @var TasksQueue
	 */
	private $taskQueue;
	
	public function __construct(Update $update)
	{
		/* @var $chat Chat */
		$chat = $update->getMessage()->getChat();
		/* @var $userData Update */
		$userData = $update->get('from');
		/* @var $user User */
		$user = new User($userData);
		
		$data = new RequesterData($user, $chat);
		
		$this->taskQueue = new TasksQueue($data);
	}
	
	public function done(int $postId)
	{
		$this->taskQueue->done($postId);
	}
	
	public function postpone(int $postId)
	{
		$this->taskQueue->postpone($postId);
	}	
}
