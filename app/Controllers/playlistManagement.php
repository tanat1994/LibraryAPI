<?php

namespace App\Controllers;

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;
use PDO;

class playlistManagement extends Controller{

    public function playlistDisplay(ServerRequestInterface $request, ResponseInterface $response){
        $playlist = $this->container->db2->query("SELECT no as playlist_id, name as playlistName, item_no as playlistItem FROM media_playlist");
        $playlist->execute();
        $playlistArray = $playlist->fetchAll(PDO::FETCH_OBJ);
        $displayStatus = "FAILED";

        if(!empty($playlistArray)) {
            $displayStatus = "SUCCESS";
        }

        $response = [
                    'status' => 'SUCCESS',
                    'data' => $playlistArray,
                    'msg' => ''
                     ];
        $response = $this->response->withJson($response);
        return $response;
    }

    public function playlistDelete(ServerRequestInterface $request, ResponseInterface $response){
        $playlistId = $request->getAttribute('playlistId');
        $playlistDel = $this->container->db2->query("DELETE FROM media_playlist WHERE no = :playlistId");
        $playlistDel->bindParam(':playlistId', $playlistId);
        $deleteStatus = "FAILED";


        $playlistCheck = $this->container->db2->query("SELECT * FROM media_playlist WHERE no = :playlistId");
        $playlistCheck->bindParam(':playlistId', $playlistId);
        $playlistCheck->execute();
        $playlistArray = $playlistCheck->fetchall(PDO::FETCH_OBJ); 

        if(empty($playlistArray)){
            $deleteStatus = "FAILED";
            $deleteMsg = "PlaylistId: {$playlistId} does not exist.";
        }else{
            if($playlistDel->execute()){
                $deleteStatus = "SUCCESS";
                $deleteMsg = "PlaylistId: {$playlistId} has been deleted";
            }
        }

        $response = [
            'status' => $deleteStatus,
            'data' => '',
            'msg' => $deleteMsg
        ];

        $response = $this->response->withJson($response);
        return $response; 
    }

    public function createPlaylist(ServerRequestInterface $request, ResponseInterface $response){
        $postData = $request->getParsedBody();
        $playlistName = $postData['name'];
        $playlistItem = $postData['arrItem'];
        $createMsg = "Failed to create the playlist";
        $createStatus = "FAILED";

        $sqlControl = $this->container->db2->query("INSERT INTO media_playlist(name,item_no) VALUES(:playlistName,:playlistItem)");
        $sqlControl->bindParam(':playlistName',$playlistName);
        $sqlControl->bindParam(':playlistItem',$playlistItem);

        if($sqlControl->execute()){
            $createStatus = "SUCCESS";
            $createMsg = "Success to create the playlist";
        }

        $data_response = "{".$playlistName." : ".$playlistItem."}";
        $response = 	[
                            'status' => $createStatus,
                            'data'   => $data_response,
                            'msg'    => $createMsg
                         ];

        return $this->response->withJson($response);
    }

    public function previewPlaylistItem(ServerRequestInterface $request, ResponseInterface $response){
        $playlistId = $request->getAttribute('playlistId');
        $playlistCheck = $this->container->db2->query("SELECT * FROM media_playlist WHERE no = :playlistId");
        $playlistCheck->bindParam(':playlistId', $playlistId);
        $playlistCheck->execute();
        $playlistArray = $playlistCheck->fetchColumn(2); //select only "item_no" column

        $statusMsg = "FAILED";
        $queryString = "";
        $itemArray = explode(",",$playlistArray);

        //Concat string with " OR no = "
        if(count($itemArray) > 0){
            foreach($itemArray as $item){
                $queryString = $queryString."".$item;
                
                if(next($itemArray)){
                    $queryString = $queryString. " OR no = ";
                }
            }
        }

        $itemDisplay = $this->container->db2->query("SELECT * FROM media_item WHERE no = $queryString");
        $itemDisplay->execute();
        $itemDisplayArray = $itemDisplay->fetchAll(PDO::FETCH_OBJ);
        if(!empty($itemDisplayArray)){
            $statusMsg = "SUCCESS";
        }

        $response = [
                       'status' => $statusMsg,
                       'data' => $itemDisplayArray,
                       'msg' => '',     
                    ];
   
        return $this->response->withJson($response);
    }

    public function updatePlaylist(ServerRequestInterface $request, ResponseInterface $response){
        $postData = $request->getParsedBody();
        $playlistId = $postData['playlistId'];
        $newplaylistItem = $postData['arrItem'];

        $updateMsg = "Failed to update the new arrItem on playlist no: ".$playlistId;
        $updateStatus = "FAILED";

        $sqlControl = $this->container->db2->query("UPDATE media_playlist SET item_no = :newplaylistItem WHERE no = :playlistId");
        $sqlControl->bindParam(':newplaylistItem', $newplaylistIte);
        $sqlControl->bindParam(':playlistId', $playlistId);
        
        if($sqlControl->execute()){
            $updateStatus = "SUCCESS";
            $updateMsg = "Success to update the new arrItem on playlist no: ".$playlistId;
        }

        $response = [
                        'status' => $updateStatus,
                        'data' => $newplaylistItem,
                        'msg' => $updateMsg
                    ];
        
        return $this->response->withJson($response);
    }
}