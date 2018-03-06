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
					->getRepository(orm\Session::class)
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
		$ip = $this->input->server('HTTP_X_REAL_IP');
		
		if (!$ip) {
			$message = [
				"error" => "X-Real-IP header is mising"
			];

			$this->set_response(
				$message, 
				REST_Controller::HTTP_FAILED_DEPENDENCY
			);
			
			return;
		}
		
		if (!$login || !$password)
		{
			$this->set_response(null, REST_Controller::HTTP_BAD_REQUEST);
			return;
		}
		
		$orm = DoctrineORM::getORM();
		
		/* @var $user User */
		$user = $orm
				->getRepository(orm\User::class)
				->findOneBy(['login' => $login]);
		
		if ($user && $user->getPassword() == md5($password))
		{
			$token = bin2hex(random_bytes(self::LENGTH));			
			$session = new Session();
			$session->setCreated(new DateTime('now'));
			$session->setToken($token);
			$session->setUser($user);
			$session->setIP($ip);

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
