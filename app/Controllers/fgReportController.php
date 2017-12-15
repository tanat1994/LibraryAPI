<?php

namespace App\Controllers;

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;
use PDO;

class fgReportController extends Controller
{
    //From report_flapgate table
    public function fgReportToday(ServerRequestInterface $request, ResponseInterface $response){
        $sqlControl = $this->container->db2->query("SELECT * FROM  report_flapgate WHERE DATE(datetime) = CURDATE()");
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

    //From report_flapgate table
    public function fgReportSearchbyKeyword(ServerRequestInterface $request, ResponseInterface $response){
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
            $direction = $postData['direction'];
            $status = $postData['status'];
            $memberType = $postData['member_type'];
            $sqlCondition = "";
            $dateRangeArry = explode(" to ",$dateRange);

            if(!(strtolower($stationId) == "all")){
                $sqlCondition = " AND station_id = '".$stationId."'";
            }

            if(!(strtolower($status) == "all")){
                $sqlCondition = " AND status = '".$status."'";
            }

            if(!(strtolower($memberType) == "all")){
                $sqlCondition = " AND member_type = '".$memberType."'";
            }

            if(!(strtolower($direction) == "all")){
                $sqlCondition = " AND direction LIKE '%".$direction."%'";
            }

            $sqlControl = $this->container->db2->query("SELECT station_id, datetime, direction, member_id, member_name, member_type, status 
            FROM (SELECT * FROM report_flapgate WHERE member_id LIKE :searchKeyword OR member_name LIKE :searchKeyword OR member_type LIKE :searchKeyword) AS REPORT
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

    //From report_flapgate table
    public function fgReportStatistic(ServerRequestInterface $request, ResponseInterface $response){
        $postData = $request->getParsedBody();
		$sortType = $postData['sort_type'];
        $dateRange = $postData['daterange'];
        $dateRangeArry = explode(" to ",$dateRange);
        $status = "FAILED";
        $msg = "No Record from FlapGate";


		if(strtolower($sortType) == "date"){//date variable return date
			$sqlControl = $this->container->db2->query("SELECT station_id, DATE(datetime), 
            SUM(CASE WHEN direction like '%IN%' THEN 1 ELSE 0 END) AS direction_IN,
            SUM(CASE WHEN direction like '%OUT%' THEN 1 ELSE 0 END) AS direction_OUT,
            COUNT(station_id) as CountSum
			FROM hongkhai.report_flapgate
			WHERE DATE(datetime) between DATE(:dateRangeArry1) AND DATE(:dateRangeArry2)
			GROUP BY  station_id, DATE(datetime)
			ORDER BY datetime DESC");

			$sqlControl->bindParam('dateRangeArry1', $dateRangeArry[0]);
            $sqlControl->bindParam('dateRangeArry2', $dateRangeArry[1]);
        }

        if(strtolower($sortType) == "month"){//date variable return month
			$sqlControl = $this->container->db2->query("SELECT station_id, MONTH(datetime), 
            SUM(CASE WHEN direction like '%IN%' THEN 1 ELSE 0 END) AS direction_IN,
            SUM(CASE WHEN direction like '%OUT%' THEN 1 ELSE 0 END) AS direction_OUT,
            COUNT(station_id) as CountSum
			FROM hongkhai.report_flapgate
			WHERE MONTH(datetime) between MONTH(:dateRangeArry1) AND MONTH(:dateRangeArry2)
			GROUP BY  station_id, MONTH(datetime)
			ORDER BY datetime DESC");

			$sqlControl->bindParam('dateRangeArry1', $dateRangeArry[0]);
            $sqlControl->bindParam('dateRangeArry2', $dateRangeArry[1]);
        }

        if(strtolower($sortType) == "year"){//date variable return year
			$sqlControl = $this->container->db2->query("SELECT station_id, YEAR(datetime), 
            SUM(CASE WHEN direction like '%IN%' THEN 1 ELSE 0 END) AS direction_IN,
            SUM(CASE WHEN direction like '%OUT%' THEN 1 ELSE 0 END) AS direction_OUT,
            COUNT(station_id) as CountSum
			FROM hongkhai.report_flapgate
			WHERE YEAR(datetime) between YEAR(:dateRangeArry1) AND YEAR(:dateRangeArry2)
			GROUP BY  station_id, DATE(datetime)
			ORDER BY datetime DESC");

			$sqlControl->bindParam('dateRangeArry1', $dateRangeArry[0]);
            $sqlControl->bindParam('dateRangeArry2', $dateRangeArry[1]);

        }

        $sqlControl->execute();
        $sqlControlArray = $sqlControl->fetchAll(PDO::FETCH_OBJ);

        if(!empty($sqlControlArray)){
            $status = "SUCCESS";
            $msg = "Statistic Report from Flapgate";
        }

        $response = [
            'status' => $status,
            'data' => $sqlControlArray,
            'msg' => $msg
            ];
                
        $response = $this->response->withJson($response);
        return $response;
    }

    //From report_flapgate table
    public function fgReturnMemberType(ServerRequestInterface $request, ResponseInterface $response){
        $sqlControl = $this->container->db2->query("SELECT DISTINCT member_type FROM  report_flapgate");
		$sqlControl->execute();
        $result = $sqlControl->fetchAll(PDO::FETCH_OBJ);	

        $status = "FAILED";
        $msg = "Fail to Return member type";

        if(!empty($result)){
            $status = "SUCCESS";
            $msg = "Success to Return member type";
        }

        $response = [
                        'status' => $status,
                        'data' => $result,
                        'msg' => $msg
                    ];

        $response = $this->response->withJson($response);
        return $response;
    }
    
    //From report_flapgate_count table
    public function fgReportCountStatistic(ServerRequestInterface $request, ResponseInterface $response){
        $postData = $request->getParsedBody();
		$sortType = $postData['sort_type'];
		$dateRange = $postData['daterange'];
		$dateRangeArry = explode(" to ",$dateRange);
        $sqlCondition = "";

        if(strtolower($sortType) == "date"){//date variable return date
			$sqlControl = $this->container->db2->query("SELECT station_id, DATE(datetime), count
			FROM hongkhai.report_flapgate_count
			where DATE(datetime) between DATE(:dateRangeArry1) and DATE(:dateRangeArry2)
			GROUP BY  station_id, datetime
			ORDER BY datetime DESC");

			$sqlControl->bindParam('dateRangeArry1', $dateRangeArry[0]);
			$sqlControl->bindParam('dateRangeArry2', $dateRangeArry[1]);
        }

        if(strtolower($sortType) == "month"){//date variable return date
			$sqlControl = $this->container->db2->query("SELECT station_id, MONTH(datetime), count
			FROM hongkhai.report_flapgate_count
			where MONTH(date) between MONTH(:dateRangeArry1) and MONTH(:dateRangeArry2)
			GROUP BY  station_id, datetime
			ORDER BY datetime DESC");

			$sqlControl->bindParam('dateRangeArry1', $dateRangeArry[0]);
			$sqlControl->bindParam('dateRangeArry2', $dateRangeArry[1]);
        }

        if(strtolower($sortType) == "year"){//date variable return date
			$sqlControl = $this->container->db2->query("SELECT station_id, YEAR(datetime), count
			FROM hongkhai.report_flapgate_count
			where YEAR(date) between YEAR(:dateRangeArry1) and YEAR(:dateRangeArry2)
			GROUP BY  station_id, datetime
			ORDER BY datetime DESC");

			$sqlControl->bindParam('dateRangeArry1', $dateRangeArry[0]);
			$sqlControl->bindParam('dateRangeArry2', $dateRangeArry[1]);
        }

        $sqlControl->execute();
        $result = $sqlControl->fetchAll(PDO::FETCH_OBJ);	
        
        $reportStatus = 'FAIL';
        $reportMsg = 'No statistic report for Flapgate Count';

        if(!empty($result)){
            $reportStatus = 'SUCCESS';
            $reportMsg    = 'Statistic report for Flapgate Count.';	
        }
		
		$status = 	[
						'status' => $reportStatus,
						'data'   => $result,
						'msg'    => $reportMsg
					];
		$response = $this->response->withJson($status);
		return $response;
    }

    //From report_flapgate_count table
    public function fgReportCountToday(ServerRequestInterface $request, ResponseInterface $response){
        $sqlControl = $this->container->db2->query("SELECT * FROM  report_flapgate_count WHERE DATE(datetime) = DATE(CURDATE())");
		$sqlControl->execute();
        $result = $sqlControl->fetchAll(PDO::FETCH_OBJ);	
        
        $reportStatus = 'FAIL';
        $reportMsg = 'No Daily report for FlapGate';

        if(!empty($result)){
            $reportStatus = 'SUCCESS';
            $reportMsg    = 'Daily report for FlapGate.';	
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