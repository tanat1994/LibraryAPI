<?php

namespace App\Controllers;

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;
use PDO;

/**
* 
*/
class scReportController extends Controller
{

	public function scReportToday(ServerRequestInterface $request, ResponseInterface $response){
		$sqlControl = $this->container->db2->query("SELECT * FROM report_selfcheck WHERE DATE(datetime) = CURDATE()");
		$sqlControl->execute();
		$result = $sqlControl->fetchAll(PDO::FETCH_OBJ);	

		$userStatus = 'SUCCESS';
		$userMsg    = 'Show report Selfcheck.';	
		
		$status = 	[
						'status' => $userStatus,
						'data'   => $result,
						'msg'    => $userMsg
					];
		$response = $this->response->withJson($status);
		return $response;
	}

	public function scReportSearchbyKeyword(ServerRequestInterface $request, ResponseInterface $response){
		$postData = $request->getParsedBody();
		$searchKeyword = "%".$postData['keyword']."%";
		if($postData['keyword'] == ''){
            $response = [
                'status' => 'FAILED',
                'data' => '{}',
                'msg' => 'Please insert value.'
            ];
			$response = $this->response->withJson($response);
			return $response;
        }else{
			$dateRange = $postData['daterange'];
			$stationId = $postData['station_id'];
			$type = $postData['type'];
			$status = $postData['status'];

			$sqlCondition = "";
			$dateRangeArry = explode(" to ",$dateRange);
			
			if(!(strtolower($stationId) == "all")){
                $sqlCondition = " AND station_id = '".$stationId."'";
            }
    
            if(!(strtolower($type) == "all")){
                $sqlCondition = " ".$sqlCondition." AND type = '".$type."'";
            }
    
            if(!(strtolower($status) == "all")){
                $sqlCondition = " ".$sqlCondition." AND status = '".$status."'";
			}

			$sqlControl = $this->container->db2->query
            ("SELECT datetime,station_id,type,status,book_id,book_name,member_id,member_name,call_no,due_date FROM
            (SELECT * FROM report_selfcheck WHERE member_id LIKE :searchKeyword OR member_name LIKE :searchKeyword OR book_id LIKE :searchKeyword OR book_name LIKE :searchKeyword OR call_no LIKE :searchKeyword) 
            AS REPORT WHERE DATE(datetime) BETWEEN DATE(:dateRangeArry1) AND DATE(:dateRangeArry2) $sqlCondition
            ORDER BY datetime DESC");
            $sqlControl->bindParam(':searchKeyword', $searchKeyword);
            $sqlControl->bindParam(':dateRangeArry1', $dateRangeArry[0]);
			$sqlControl->bindParam(':dateRangeArry2', $dateRangeArry[1]);
			$sqlControl->execute();
            $sqlControlArray = $sqlControl->fetchAll(PDO::FETCH_OBJ);
    
            if(empty($sqlControlArray)){
                $response = [
                    'status' => 'FAILED',
                    'data' => '{}',
                    'msg' => 'Record does not exist with the criteria'
                ];
            }else{
                $response = [
                    'status' => 'SUCCESS',
                    'data' => $sqlControlArray,
                    'msg' => 'Record founded'
                ];
            }

			$response = $this->response->withJson($response);
            return $response;
		}
	}

	public function scReportImage(ServerRequestInterface $request, ResponseInterface $response){
		$postData = $request->getParsedBody();
		$dateRange = $postData['daterange'];
		$stationId = $postData['station_id'];
		$type = $postData['type'];
		$status = $postData['status'];

		$dateRangeArry = explode(" to ",$dateRange);
		$sqlCondition = "";
		
		if(!(strtolower($stationId) == "all")){
            $sqlCondition = " ".$sqlCondition." AND station_id = '".$stationId."'";
		}
		
		if(!(strtolower($type) == "all")){
			$sqlCondition = " ".$sqlCondition." AND type = '".$type."'";
		}

        if(!(strtolower($status) == "all")){
            $sqlCondition = " ".$sqlCondition." AND status = '".$status."'";
		}
		
		$sqlControl = $this->container->db2->query("SELECT datetime,due_date,station_id,type,book_id,book_name,status,user_image,status FROM report_selfcheck WHERE DATE(datetime) BETWEEN DATE(:dateRangeArry1) AND DATE(:dateRangeArry2) $sqlCondition ORDER BY datetime DESC");
        $sqlControl->bindParam(":dateRangeArry1", $dateRangeArry[0]);
        $sqlControl->bindParam(":dateRangeArry2", $dateRangeArry[1]);
        $sqlControl->execute();
		$sqlControlArray = $sqlControl->fetchAll(PDO::FETCH_OBJ);
		
		if(empty($sqlControlArray)){
			$response = [
				'status' => 'FAILED',
				'data' => '{}',
				'msg' => 'Record does not exist with the criteria'
			];
		}else{
			$response = [
				'status' => 'SUCCESS',
				'data' => $sqlControlArray,
				'msg' => 'Record founded'
			];
		}

		$response = $this->response->withJson($response);
		return $response;
	}

	public function scReportBookStatistic(ServerRequestInterface $request, ResponseInterface $response){
		$postData = $request->getParsedBody();
		$sortType = $postData['sort_type'];
		$dateRange = $postData['daterange'];
		$dateRangeArry = explode(" to ",$dateRange);
		$sqlCondition = "";

		if(strtolower($sortType) == "date"){//date variable return date
			$sqlControl = $this->container->db2->query("SELECT station_id, date(datetime) as date, count(type) as CountSum,
			SUM(CASE WHEN type = 'return' THEN 1 ELSE 0 END) AS returnSc,
			SUM(CASE WHEN type = 'renew' THEN 1 ELSE 0 END) AS renewSC,
			SUM(CASE WHEN type = 'borrow' THEN 1 ELSE 0 END) AS borrowSC
			FROM hongkhai.report_selfcheck
			where DATE(datetime) between DATE(:dateRangeArry1) and DATE(:dateRangeArry2)
			GROUP BY  station_id, datetime
			ORDER BY datetime DESC");

			$sqlControl->bindParam('dateRangeArry1', $dateRangeArry[0]);
			$sqlControl->bindParam('dateRangeArry2', $dateRangeArry[1]);
		}
		if(strtolower($sortType) == "month"){//date variable return month
			$sqlControl = $this->container->db2->query("SELECT station_id, MONTH(datetime) as date, count(type) as CountSum,
			SUM(CASE WHEN type = 'return' THEN 1 ELSE 0 END) AS returnSc,
			SUM(CASE WHEN type = 'renew' THEN 1 ELSE 0 END) AS renewSC,
			SUM(CASE WHEN type = 'borrow' THEN 1 ELSE 0 END) AS borrowSC
			FROM hongkhai.report_selfcheck
			where MONTH(datetime) between MONTH(:dateRangeArry1) and MONTH(:dateRangeArry2)
			GROUP BY  station_id, datetime
			ORDER BY datetime DESC");
			
			$sqlControl->bindParam('dateRangeArry1', $dateRangeArry[0]);
			$sqlControl->bindParam('dateRangeArry2', $dateRangeArry[1]);
		}
		if(strtolower($sortType) == "year"){ //date variable return year
			$sqlControl = $this->container->db2->query("SELECT station_id, YEAR(datetime) as date, count(type) as CountSum,
			SUM(CASE WHEN type = 'return' THEN 1 ELSE 0 END) AS returnSc,
			SUM(CASE WHEN type = 'renew' THEN 1 ELSE 0 END) AS renewSC,
			SUM(CASE WHEN type = 'borrow' THEN 1 ELSE 0 END) AS borrowSC
			FROM hongkhai.report_selfcheck
			where YEAR(datetime) between YEAR(:dateRangeArry1) and YEAR(:dateRangeArry2)
			GROUP BY  station_id, datetime
			ORDER BY datetime DESC");
			
			$sqlControl->bindParam('dateRangeArry1', $dateRangeArry[0]);
			$sqlControl->bindParam('dateRangeArry2', $dateRangeArry[1]);
		}
		
		$sqlControl->execute();
		$sqlControlArray = $sqlControl->fetchAll(PDO::FETCH_OBJ);

		$statusMsg = "FAIL";
		$msg = "No data";
		if(!empty($sqlControlArray)){
			$statusMsg = "SUCCESS";
			$msg = "Count all status(success|fail) transaction";
		}

		$response = [
						'status' => $statusMsg,
						'data' => $sqlControlArray,
						'msg' => $msg
					];

		$response = $this->response->withJson($response);
		return $response;
	}


}