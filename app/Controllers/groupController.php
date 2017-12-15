<?php
namespace App\Controllers;

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;
use PDO;

class groupController extends Controller{

	public function groupInitial(ServerRequestInterface $request, ResponseInterface $response){
		$sqlControl = $this->container->db2->query("SELECT * FROM  hongkhai.group WHERE parent = 0;");
		$sqlControl->execute();
		$result = $sqlControl->fetchAll(PDO::FETCH_OBJ);	

        $reportStatus = 'FAIL';
        $reportMsg    = 'Fail to get data from groupId = 0';	

        if(!empty($result)){
            $reportStatus = 'SUCCESS';
            $reportMsg    = 'Return data from groupId = 0';	
        }
		
		$response = 	[
						'status' => $reportStatus,
						'data'   => $result,
						'msg'    => $reportMsg
					];
		$response = $this->response->withJson($response);
		return $response;
	}

	public function ChildSearching(ServerRequestInterface $request, ResponseInterface $response){
		$postData = $request->getParsedBody();
		$groupId = $postData['groupId'];

		$sqlControl = $this->container->db2->query("SELECT * FROM  hongkhai.group WHERE parent= :groupId");
		$sqlControl->bindParam(":groupId", $groupId);
		$sqlControl->execute();
		$result = $sqlControl->fetchAll(PDO::FETCH_OBJ);


        $response = [
            'status' => 'aa',
            'data' => $result,
            'msg' => 'aa'
            ];

        $response = $this->response->withJson($response);
		return $response;
	}


	public function grouphasChild(ServerRequestInterface $request, ResponseInterface $response){
		$postData = $request->getParsedBody();
		$groupId = $postData['groupId'];

		$status = "FAILED";
		$msg = "No child";
		
		$sqlControl = $this->container->db2->query("SELECT * FROM hongkhai.group WHERE parent= :groupId");
		$sqlControl->bindParam(":groupId", $groupId);
		$sqlControl->execute();
        $result = $sqlControl->fetchAll(PDO::FETCH_OBJ);

        if(!empty($result)){
            $status = "SUCCESS";
            $msg = "GroupID = ".$groupId." has child";
        }


        $response = [
            'status' => $status,
            'data' => $result,
            'msg' => $msg
            ];

        $response = $this->response->withJson($response);
		return $response;

	}

}