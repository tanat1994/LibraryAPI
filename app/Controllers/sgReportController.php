<?php

namespace App\Controllers;

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;
use PDO;

class sgReportController extends Controller
{
    //From report_security_gate_count table
    public function sgReportCountStatistic(ServerRequestInterface $request, ResponseInterface $response){
        $postData = $request->getParsedBody();
		$sortType = $postData['sort_type'];
		$dateRange = $postData['daterange'];
		$dateRangeArry = explode(" to ",$dateRange);
        $sqlCondition = "";

        if(strtolower($sortType) == "date"){//date variable return date
			$sqlControl = $this->container->db2->query("SELECT station_id, DATE(date), count
			FROM hongkhai.report_security_gate_count
			where DATE(date) between DATE(:dateRangeArry1) and DATE(:dateRangeArry2)
			GROUP BY  station_id, date
			ORDER BY date DESC");

			$sqlControl->bindParam('dateRangeArry1', $dateRangeArry[0]);
			$sqlControl->bindParam('dateRangeArry2', $dateRangeArry[1]);
        }

        if(strtolower($sortType) == "month"){//date variable return date
			$sqlControl = $this->container->db2->query("SELECT station_id, MONTH(date), count
			FROM hongkhai.report_security_gate_count
			where MONTH(date) between MONTH(:dateRangeArry1) and MONTH(:dateRangeArry2)
			GROUP BY  station_id, date
			ORDER BY date DESC");

			$sqlControl->bindParam('dateRangeArry1', $dateRangeArry[0]);
			$sqlControl->bindParam('dateRangeArry2', $dateRangeArry[1]);
        }

        if(strtolower($sortType) == "year"){//date variable return date
			$sqlControl = $this->container->db2->query("SELECT station_id, YEAR(date), count
			FROM hongkhai.report_security_gate_count
			where YEAR(date) between YEAR(:dateRangeArry1) and YEAR(:dateRangeArry2)
			GROUP BY  station_id, date
			ORDER BY date DESC");

			$sqlControl->bindParam('dateRangeArry1', $dateRangeArry[0]);
			$sqlControl->bindParam('dateRangeArry2', $dateRangeArry[1]);
        }

        $sqlControl->execute();
        $result = $sqlControl->fetchAll(PDO::FETCH_OBJ);	
        
        $reportStatus = 'FAIL';
        $reportMsg = 'No statistic report';

        if(!empty($result)){
            $reportStatus = 'SUCCESS';
            $reportMsg    = 'Statistic report for Security gate count.';	
        }
		
		$status = 	[
						'status' => $reportStatus,
						'data'   => $result,
						'msg'    => $reportMsg
					];
		$response = $this->response->withJson($status);
		return $response;
    }

    //From report_security_gate_count table
    public function sgReportCountToday(ServerRequestInterface $request, ResponseInterface $response){
        $sqlControl = $this->container->db2->query("SELECT * FROM  report_security_gate_count WHERE DATE(date) = DATE(CURDATE())");
		$sqlControl->execute();
        $result = $sqlControl->fetchAll(PDO::FETCH_OBJ);	
        
        $reportStatus = 'FAIL';
        $reportMsg = 'No Daily report';

        if(!empty($result)){
            $reportStatus = 'SUCCESS';
            $reportMsg    = 'Daily report for Security gate.';	
        }
		
		$status = 	[
						'status' => $reportStatus,
						'data'   => $result,
						'msg'    => $reportMsg
					];
		$response = $this->response->withJson($status);
		return $response;
    }

    //From report_security_gate table
    public function sgReportSearchbyKeyword(ServerRequestInterface $request, ResponseInterface $response){
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
            $type = $postData['type'];
            $sqlCondition = "";
            $dateRangeArry = explode(" to ",$dateRange);

            if(!(strtolower($stationId) == "all")){
                $sqlCondition = " AND station_id = '".$stationId."'";
            }

            if(!(strtolower($type) == "all")){
                $sqlCondition = " AND type = '".$type."'";
            }

            $sqlControl = $this->container->db2->query("SELECT station_id, datetime, type, book_id, book_name
            FROM (SELECT * FROM report_security_gate WHERE book_id LIKE :searchKeyword OR book_name LIKE :searchKeyword OR call_no LIKE :searchKeyword) AS REPORT
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

    //From report_security_gate table
    public function sgReportStatistic(ServerRequestInterface $request, ResponseInterface $response){
        $postData = $request->getParsedBody();
		$sortType = $postData['sort_type'];
		$dateRange = $postData['daterange'];
		$dateRangeArry = explode(" to ",$dateRange);
        $sqlCondition = "";
        
        if(strtolower($sortType) == "date"){//date variable return date
            $sqlControl = $this->container->db2->query("SELECT station_id, date(datetime) as date, count(type) as CountSum,
			SUM(CASE WHEN type = 'borrow' THEN 1 ELSE 0 END) AS borrowSg,
            SUM(CASE WHEN type = 'not_borrow' THEN 1 ELSE 0 END) AS NotBorrowSg
			FROM hongkhai.report_security_gate
			where DATE(datetime) between DATE(:dateRangeArry1) and DATE(:dateRangeArry2)
			GROUP BY  station_id, DATE(datetime)
			ORDER BY datetime DESC");

			$sqlControl->bindParam('dateRangeArry1', $dateRangeArry[0]);
			$sqlControl->bindParam('dateRangeArry2', $dateRangeArry[1]);
        }

        if(strtolower($sortType) == "month"){//date variable return month
            $sqlControl = $this->container->db2->query("SELECT station_id, MONTH(datetime) as date, count(type) as CountSum,
			SUM(CASE WHEN type = 'borrow' THEN 1 ELSE 0 END) AS borrowSg,
            SUM(CASE WHEN type = 'not_borrow' THEN 1 ELSE 0 END) AS NotBorrowSg
			FROM hongkhai.report_security_gate
			where MONTH(datetime) between MONTH(:dateRangeArry1) and MONTH(:dateRangeArry2)
			GROUP BY  station_id, DATE(datetime)
			ORDER BY datetime DESC");

			$sqlControl->bindParam('dateRangeArry1', $dateRangeArry[0]);
			$sqlControl->bindParam('dateRangeArry2', $dateRangeArry[1]);
        }

        if(strtolower($sortType) == "year"){//date variable return year
            $sqlControl = $this->container->db2->query("SELECT station_id, YEAR(datetime) as date, count(type) as CountSum,
			SUM(CASE WHEN type = 'borrow' THEN 1 ELSE 0 END) AS borrowSg,
            SUM(CASE WHEN type = 'not_borrow' THEN 1 ELSE 0 END) AS NotBorrowSg
			FROM hongkhai.report_security_gate
			where MONTH(datetime) between MONTH(:dateRangeArry1) and MONTH(:dateRangeArry2)
			GROUP BY  station_id, DATE(datetime)
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

    //From report_security_gate table
    public function sgReportToday(ServerRequestInterface $request, ResponseInterface $response){
        $sqlControl = $this->container->db2->query("SELECT * FROM  report_security_gate WHERE DATE(datetime) = CURDATE()");
		$sqlControl->execute();
        $result = $sqlControl->fetchAll(PDO::FETCH_OBJ);	
        
        $reportStatus = 'FAIL';
        $reportMsg = 'No Daily report';

        if(!empty($result)){
            $reportStatus = 'SUCCESS';
            $reportMsg    = 'Daily report for Security gate.';	
        }
		
		$status = 	[
						'status' => $reportStatus,
						'data'   => $result,
						'msg'    => $reportMsg
					];
		$response = $this->response->withJson($status);
		return $response;
    }
}
    