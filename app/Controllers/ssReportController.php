<?php

namespace App\Controllers;

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;
use PDO;

class ssReportController extends Controller
{
    public function ssReportSearchbyKeyword(ServerRequestInterface $request, ResponseInterface $response){
        $postData = $request->getParsedBody();
        $searchKeyword = "%".$postData['keyword']."%";
        if($postData['keyword'] == ''){
            $response = [
                'status' => 'FAILED',
                'data' => '{}',
                'msg' => 'Please insert value.'
            ];
        }else{
            $dateRange = $postData['daterange'];
            $stationId = $postData['station_id'];
            $sqlCondition = "";
            $dateRangeArry = explode(" to ",$dateRange);

            if(!(strtolower($stationId) == "all")){
                $sqlCondition = " AND station_id = '".$stationId."'";
            }

            $sqlControl = $this->container->db2->query("SELECT station_id, datetime, book_id, book_name, book_call_no, user_create 
            FROM (SELECT * FROM report_staff_station WHERE book_id LIKE :searchKeyword OR book_name LIKE :searchKeyword OR book_call_no LIKE :searchKeyword OR user_create LIKE :searchKeyword) AS REPORT
            WHERE DATE(datetime) BETWEEN DATE(:dateRangeArry1) AND DATE(:dateRangeArry2) $sqlCondition;");   
            $sqlControl->bindParam(':dateRangeArry1', $dateRangeArry[0]);
            $sqlControl->bindParam(':dateRangeArry2', $dateRangeArry[1]);
            $sqlControl->bindParam(':searchKeyword', $searchKeyword);   
            $sqlControl->execute();
            $sqlControlArray = $sqlControl->fetchAll(PDO::FETCH_OBJ);
            $response = [
                        'status' => 'SUCCESS',
                        'data' => $sqlControlArray,
                        'msg' => ''
                        ];
        }

        $response = $this->response->withJson($response);
        return $response;
    }

    public function ssReportToday(ServerRequestInterface $request, ResponseInterface $response){
        $sqlControl = $this->container->db2->query("SELECT * FROM  report_staff_station WHERE DATE(datetime) = CURDATE()");
		$sqlControl->execute();
		$result = $sqlControl->fetchAll(PDO::FETCH_OBJ);	

        $reportStatus = 'FAIL';
        $reportMsg    = 'No Daily report for Staff Station';	

        if(!empty($result)){
            $reportStatus = 'SUCCESS';
            $reportMsg    = 'Daily report for Staff Station.';	
        }
		
		$status = 	[
						'status' => $reportStatus,
						'data'   => $result,
						'msg'    => $reportMsg
					];
		$response = $this->response->withJson($status);
		return $response;
    }

    public function ssReportStatistic(ServerRequestInterface $request, ResponseInterface $response){
        $postData = $request->getParsedBody();
		$sortType = $postData['sort_type'];
		$dateRange = $postData['daterange'];
		$dateRangeArry = explode(" to ",$dateRange);
		$sqlCondition = "";

		if(strtolower($sortType) == "date"){//date variable return date
			$sqlControl = $this->container->db2->query("SELECT station_id, date(datetime) as date, count(book_id) as CountSum
			FROM hongkhai.report_staff_station
			where DATE(datetime) between DATE(:dateRangeArry1) and DATE(:dateRangeArry2)
			GROUP BY  station_id, datetime
			ORDER BY datetime DESC");

			$sqlControl->bindParam('dateRangeArry1', $dateRangeArry[0]);
			$sqlControl->bindParam('dateRangeArry2', $dateRangeArry[1]);
        }
        
        if(strtolower($sortType) == "month"){//date variable return month
			$sqlControl = $this->container->db2->query("SELECT station_id, MONTH(datetime) as date, count(book_id) as CountSum
			FROM hongkhai.report_staff_station
			where MONTH(datetime) between MONTH(:dateRangeArry1) and MONTH(:dateRangeArry2)
			GROUP BY  station_id, datetime
			ORDER BY datetime DESC");

			$sqlControl->bindParam('dateRangeArry1', $dateRangeArry[0]);
			$sqlControl->bindParam('dateRangeArry2', $dateRangeArry[1]);
        }
        
        if(strtolower($sortType) == "year"){//date variable return year
			$sqlControl = $this->container->db2->query("SELECT station_id, YEAR(datetime) as date, count(book_id) as CountSum
			FROM hongkhai.report_staff_station
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
