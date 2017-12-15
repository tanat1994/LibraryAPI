<?php

namespace App\Controllers;

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;
use PDO;

/**
* 
*/
class loginController extends Controller
{

	public function checkLogin(ServerRequestInterface $request, ResponseInterface $response)
	{
		$postData = $request->getParsedBody();
		$username = $postData['txtUsername'];
		$password = $postData['txtPassword'];

		$user = $this->container->db->query("SELECT count(*) AS 'userCount',staff_id,status FROM staff WHERE username = :username AND password = :password");
		$user->bindParam(':username',$username); // ช่วยในเรื่อง ' และ " ได้
		$user->bindParam(':password',$password);
		$user->execute();
		$user1 = $user->fetchAll(PDO::FETCH_OBJ);
		//$response = $this->response->withJson($user1);
		$userStatus = 'FAIL';
		$userMsg    = '';
		$userData   =	[
							'userID'          => '',
							'userName'        => '',
							'userPosition'    => '',
							'userEmail'       => '',
							'userUsername'    => '',
							'userType'        => '',
							'userPermControl' => '',
							'userPermReport'  => ''
						];

		if($user1[0]->userCount == 0){
			$userMsg = 'Plaese,Try Agian.';

		}elseif ($user1[0]->status == 'disable') {
			$userMsg = 'This member has been suspended.';
		}elseif ($user1[0]->status == 'expire') {
			$userMsg = 'This member has expired.';
		}else{
			$userStatus = 'SUCCESS';
			$userMsg = 'Login success.';

			$staff_id1 = $user1[0]->staff_id;
			$userCorrect = $this->container->db->query("
								SELECT staff.name, staff.position, staff.email, staff.type, staff_permission.control_list , staff_permission.report_list
								FROM staff 
								LEFT JOIN staff_permission 
								ON staff.staff_id = staff_permission.staff_id  
								WHERE staff.staff_id = '$staff_id1'
							");
			$userCorrect->execute();
			$userCorrect1 = $userCorrect->fetchAll(PDO::FETCH_OBJ);
			$userPermControl = 0;
			$userPermReport = 0;

			if(strlen($userCorrect1[0]->control_list) > 1){ $userPermControl = 1; }
			if(strlen($userCorrect1[0]->report_list) > 1){ $userPermReport = 1; }

			$userData =	[
							'userID'          => $staff_id1,
							'userName'        => $userCorrect1[0]->name,
							'userPosition'    => $userCorrect1[0]->position,
							'userEmail'       => $userCorrect1[0]->email,
							'userUsername'    => $username,
							'userType'        => $userCorrect1[0]->type,
							'userPermControl' => $userPermControl,
							'userPermReport'  => $userPermReport
						];
			
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