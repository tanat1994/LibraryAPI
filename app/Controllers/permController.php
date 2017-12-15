<?php

namespace App\Controllers;

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;
use PDO;

/**
* 
*/
class permController extends Controller
{

	public function permControl(ServerRequestInterface $request, ResponseInterface $response)
	{
		$userID = $request->getAttribute('userID');

		$userStatus = 'SUCCESS';
		$userMsg    = 'Premission list for user id : '.$userID;
		$userData   =	[
							'sc' => 0,
							'ss' => 0,
							'sg' => 0,
							'fg' => 0,
							'bd' => 0,
							'as' => 0,
							'mc' => 0,
						];
		$sqlControl = $this->container->db->query("SELECT control_list FROM staff_permission WHERE staff_id = '$userID' ");
		$sqlControl->execute();
		$result = $sqlControl->fetchAll(PDO::FETCH_OBJ);

		$arrControl = explode(',', $result[0]->control_list);
		foreach ($arrControl as $key => $value) {
			if     ($value == 'sc') { $userData['sc'] = 1; }
			elseif ($value == 'ss') { $userData['ss'] = 1; }
			elseif ($value == 'sg') { $userData['sg'] = 1; }
			elseif ($value == 'fg') { $userData['fg'] = 1; }
			elseif ($value == 'bd') { $userData['bd'] = 1; }
			elseif ($value == 'as') { $userData['as'] = 1; }
			elseif ($value == 'mc') { $userData['mc'] = 1; }
		}
		
		$status = 	[
						'status' => $userStatus,
						'data'   => $userData,
						'msg'    => $userMsg
					];
		$response = $this->response->withJson($status);
		return $response;
	}

	public function permReport(ServerRequestInterface $request, ResponseInterface $response)
	{
		$userID = $request->getAttribute('userID');

		$userStatus = 'SUCCESS';
		$userMsg    = 'Premission list for user id : '.$userID;
		$userData   =	[
							'sc' => 0,
							'ss' => 0,
							'sg' => 0,
							'fg' => 0,
							'bd' => 0,
							'as' => 0,
							'mc' => 0,
						];
		$sqlReport = $this->container->db->query("SELECT report_list FROM staff_permission WHERE staff_id = '$userID' ");
		$sqlReport->execute();
		$result = $sqlReport->fetchAll(PDO::FETCH_OBJ);

		$arrReport = explode(',', $result[0]->report_list);
		foreach ($arrReport as $key => $value) {
			if     ($value == 'sc') { $userData['sc'] = 1; }
			elseif ($value == 'ss') { $userData['ss'] = 1; }
			elseif ($value == 'sg') { $userData['sg'] = 1; }
			elseif ($value == 'fg') { $userData['fg'] = 1; }
			elseif ($value == 'bd') { $userData['bd'] = 1; }
			elseif ($value == 'as') { $userData['as'] = 1; }
			elseif ($value == 'mc') { $userData['mc'] = 1; }
		}
		
		$status = 	[
						'status' => $userStatus,
						'data'   => $userData,
						'msg'    => $userMsg
					];
		$response = $this->response->withJson($status);
		return $response;
	}
}