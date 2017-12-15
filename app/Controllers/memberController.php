<?php
namespace App\Controllers;

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;
use PDO;

class memberController extends Controller{

	 public function memberRecord(ServerRequestInterface $request, ResponseInterface $response){
        //GET CURRENT DATE ITEM
        $sqlControl = $this->container->db2->query("SELECT * FROM member;");
        $sqlControl->execute();
        $sqlControlArray = $sqlControl->fetchAll(PDO::FETCH_OBJ);

        $response = $this->response->withJson($sqlControlArray);
        return $response;
    }
}