<?php

defined('BASEPATH') OR exit('No direct script access allowed');

//require_once(APPROOT . "/bootstrap.php");

use orm\User;
use orm\Session;

/**
 * Description of Auth
 *
 * @author olga
 */
class Auth extends REST_Controller 
{
	const LENGTH = 32;
	
	public function index_delete()
	{
		$headers = getallheaders();
		
		if (isset($headers['token']))
		{
			$token = $headers['token'];
			$orm = DoctrineORM::getORM();
		
			$session = $orm
					->getRepository('orm\Session')
					->findOneBy(['token' => $token]);

			if ($session) {
				$orm->remove($session);
				$orm->flush();

				$this->set_response([
					"token" => $token
				], REST_Controller::HTTP_ACCEPTED);
			} else {
				$this->set_response([
					"token" => $token
				], REST_Controller::HTTP_OK);
			}
		} else {
			$this->set_response(null, REST_Controller::HTTP_BAD_REQUEST);
		}
	}
	
	public function index_post()
    {
		$login = $this->post('username');
		$password = $this->post('password');
		
		$orm = $this->doctrine->getORM();
		
		/* @var $user User */
		$user = $orm
				->getRepository('orm\User')
				->findOneBy(['login' => $login]);
		
		if ($user && $user->getPassword() == $password) 
		{
			$token = bin2hex(random_bytes(self::LENGTH));
			$session = new Session();
			$session->setCreated(new DateTime('now'));
			$session->setToken($token);
			$session->setUser($user);

			$orm->persist($session);
			$orm->flush();
			
			$message = [
				"token" => $token
			];
			
			$this->set_response($message, REST_Controller::HTTP_CREATED);
		} else {
			$this->set_response(null, REST_Controller::HTTP_UNAUTHORIZED);
		}
    }
}
