<?php
namespace App\Controllers;

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;
use PDO;

class bdReportController extends Controller{


    public function bdReportSearchbyKeyword(ServerRequestInterface $request, ResponseInterface $response){
        $postData = $request->getParsedBody();
        $searchKeyword = "%".$postData['keyword']."%";
        if(is_null($postData['keyword'])){
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
            $bdStationId = $postData['bookbin_station_id'];
            $status = $postData['status'];
            
            $sqlCondition = "";
            $dateRangeArry = explode(" to ",$dateRange);
    
            if(!(strtolower($stationId) == "all")){
                $sqlCondition = " AND station_id = '".$stationId."'";
            }
    
            if(!(strtolower($bdStationId) == "all")){
                $sqlCondition = " ".$sqlCondition." AND bookbin_station_id = '".$bdStationId."'";
            }
    
            if(!(strtolower($type) == "all")){
                $sqlCondition = " ".$sqlCondition." AND type = '".$type."'";
            }
    
            if(!(strtolower($status) == "all")){
                $sqlCondition = " ".$sqlCondition." AND status = '".$status."'";
            }
    
            $sqlControl = $this->container->db2->query
            ("SELECT datetime,station_id,type,status,bookbin_station_id,book_id,book_name,member_id,member_name,call_no FROM
            (SELECT * FROM report_bookdrop WHERE member_id LIKE :searchKeyword OR member_name LIKE :searchKeyword OR book_id LIKE :searchKeyword OR book_name LIKE :searchKeyword OR call_no LIKE :searchKeyword) 
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

    public function bdReportImages(ServerRequestInterface $request, ResponseInterface $response){
        $postData = $request->getParsedBody();
        $dateRange = $postData['daterange'];
        $stationId = $postData['station_id'];
        $status = $postData['status'];
        $dateRangeArry = explode(" to ",$dateRange);
        $sqlCondition = "";

        if(!(strtolower($stationId) == "all")){
            $sqlCondition = " ".$sqlCondition." AND station_id = '".$stationId."'";
        }

        if(!(strtolower($status) == "all")){
            $sqlCondition = " ".$sqlCondition." AND status = '".$status."'";
        }

        $sqlControl = $this->container->db2->query("SELECT datetime,station_id,book_id,book_name,status,user_image,book_image FROM report_bookdrop WHERE DATE(datetime) BETWEEN DATE(:dateRangeArry1) AND DATE(:dateRangeArry2) $sqlCondition ORDER BY datetime DESC");
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

    public function bdReportToday(ServerRequestInterface $request, ResponseInterface $response){
        //GET CURRENT DATE ITEM
        $sqlControl = $this->container->db2->query("SELECT * FROM report_bookdrop WHERE DATE(datetime)=CURDATE() ORDER BY datetime DESC;");
        $sqlControl->execute();
        $sqlControlArray = $sqlControl->fetchAll(PDO::FETCH_OBJ);

        $response = $this->response->withJson($sqlControlArray);
        return $response;
    }

    public function bdReportStatistic(ServerRequestInterface $request, ResponseInterface $response){
        $postData = $request->getParsedBody();
        $sort_by = $postData['sort_by'];
        $dateRange = $postData['daterange'];
        $dateRangeArry = explode(" to ",$dateRange);
        $sortCondition = "";

        if(strtolower($sort_by) == "hour"){

            $sqlControl = $this->container->db2->query("SELECT station_id, DATE_FORMAT(datetime,'%H:00') as date, count(station_id) as NumberOfClient FROM
            report_bookdrop WHERE DAY(datetime) BETWEEN DAY(:dateRangeArry1) AND DAY(:dateRangeArry2)
            GROUP BY station_id, date");
            $sqlControl->bindParam(":dateRangeArry1", $dateRangeArry[0]);
            $sqlControl->bindParam(":dateRangeArry2", $dateRangeArry[1]);

        }else if(strtolower($sort_by) == "date"){

            $sqlControl = $this->container->db2->query("SELECT station_id, DATE(datetime) as date, count(station_id) as NumberOfClient FROM
            report_bookdrop WHERE MONTH(datetime) BETWEEN MONTH(:dateRangeArry1) AND MONTH(:dateRangeArry2)
            GROUP BY station_id, date");
            $sqlControl->bindParam(":dateRangeArry1", $dateRangeArry[0]);
            $sqlControl->bindParam(":dateRangeArry2", $dateRangeArry[1]);

        }else if(strtolower($sort_by) == "month"){

            $sqlControl = $this->container->db2->query("SELECT station_id, MONTH(datetime) as date, count(station_id) as NumberOfClient FROM
            report_bookdrop WHERE YEAR(datetime) BETWEEN YEAR(:dateRangeArry1) AND YEAR(:dateRangeArry2)
            GROUP BY station_id, date");
            $sqlControl->bindParam(":dateRangeArry1", $dateRangeArry[0]);
            $sqlControl->bindParam(":dateRangeArry2", $dateRangeArry[1]);

        }else if(strtolower($sort_by) == "year"){

            $sqlControl = $this->container->db2->query("SELECT station_id, YEAR(datetime) as date, count(station_id) as NumberOfClient FROM
            report_bookdrop WHERE YEAR(datetime) BETWEEN YEAR(:dateRangeArry1) AND YEAR(:dateRangeArry2)
            GROUP BY station_id, date");
            $sqlControl->bindParam(":dateRangeArry1", $dateRangeArry[0]);
            $sqlControl->bindParam(":dateRangeArry2", $dateRangeArry[1]);

        }else{
            
        }

        $sqlControl->execute();
        $sqlControlArray = $sqlControl->fetchAll(PDO::FETCH_OBJ);
        $sum = 0;

        foreach($sqlControlArray as $value){
            $sum = $sum + $value->NumberOfClient;
            strval($sum);
        }
        


        $response= [
                    'status' => 'SUCCESS',
                    'data' => $sqlControlArray,
                    'Total' => $sum
                   ];

        $response = $this->response->withJson($response);
        return $response;
    }

}