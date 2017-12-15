<?php

namespace App\Controllers;

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;
use PDO;

/**
* 
*/
class HomeController extends Controller
{
	public function indexQuery(ServerRequestInterface $request, ResponseInterface $response)
	{
		//$user = $this->container->db->query('SELECT * FROM staff ');

		$user = 'Hello Hello.';

		/*
		----------for array--------------

		$user = ['status' => 'success','data' => '','msg' => 'Login success.'];
		-----or-------
		$user['status'] = 'success';
		$user['msg'] = 'Login success';

		$response = $this->response->getBody()->write(print_r($status, true));
		return $response;
		*/

		return $this->response->withJson($user);
	}

	public function indexGet(ServerRequestInterface $request, ResponseInterface $response)
	{
		$name = $request->getAttribute('name');
    	$response->getBody()->write("Hello, $name");

    	return $response;
	}
}