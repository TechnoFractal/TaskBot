<?php

/**
 * Description of Auth
 *
 * @author olga
 */
class Authhook
{
	private function checkToken() : bool
	{
		$router =& load_class('Router', 'core');
        $controller = $router->fetch_class();

        if($controller == 'auth' || $controller == 'main')
        {
			return true;
		}
		
		$headers = getallheaders();

		if (!isset($headers['token']))
		{
			return false;
		}
		
		$token = $headers['token'];
		$doctrine = new DoctrineORM();
		$orm = $doctrine->getORM();

		$session = $orm
				->getRepository('orm\Session')
				->findOneBy(['token' => $token]);

		if (!$session) {
			return false;
		} else {
			return true;
		}
		
	}
	
	public function authToken()
	{
		if (!$this->checkToken())
		{
			show_error("Unauthorized", REST_Controller::HTTP_UNAUTHORIZED);
		}
	}
}
