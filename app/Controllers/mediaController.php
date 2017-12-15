<?php

namespace App\Controllers;

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;
use PDO;

class mediaController extends Controller{

    public function pictureUpload(ServerRequestInterface $request, ResponseInterface $response)
	{
        $postData = $request->getParsedBody();
        $pictureFileName = $postData['pictureFileName'];
        
        // $pictureFileName = $request->getAttribute('pictureFileName');
        $uploadMsg = "Failed to upload";
        $uploadStatus = "FAILED";
        $cover = $pictureFileName;

        $sqlControl = $this->container->db2->query("INSERT INTO media_item(filename,cover) VALUES(:pictureFileName,:cover)");
        $sqlControl->bindParam(':pictureFileName',$pictureFileName);
        $sqlControl->bindParam(':cover',$cover);

        if($sqlControl->execute()){
            $uploadStatus = "SUCCESS";
            $uploadMsg = "Success to upload";
        }

        $response = 	[
                            'status' => $uploadStatus,
                            'data'   => $pictureFileName,
                            'msg'    => $uploadMsg
                         ];

        return $this->response->withJson($response);
    }
    
    public function videoUpload(ServerRequestInterface $request, ResponseInterface $response){

        $postData = $request->getParsedBody();
        $videoFileName = $postData['videoFileName'];
        $videoCoverPicture = $postData['videoCoverPicture'];

        // $videoFileName = $request->getAttribute('videoFileName');
        // $videoCoverPicture = $request->getAttribute('videoCoverPicture');
        $uploadStatus = "FAILED";
        $uploadMsg = "Failed to upload";

        $sqlControl = $this->container->db2->query("INSERT INTO media_item(filename,cover) VALUES(:videoFileName,:videoCoverPicture)");
        $sqlControl->bindParam(':videoFileName', $videoFileName);
        $sqlControl->bindParam(':videoCoverPicture', $videoCoverPicture);

        if($sqlControl->execute()){
            $uploadStatus = "SUCCESS";
            $uploadMsg = "Success to upload";
        }

        $data_response = "[{".$videoFileName.",".$videoCoverPicture."}]";
        $response = [
                        'status' => $uploadStatus,
                        'data'   => $data_response,
                        'msg'    => $uploadMsg
                    ];

        return $this->response->withJson($response);
    }

    public function mediaDisplay(ServerRequestInterface $request, ResponseInterface $response){

        $playlist = $this->container->db2->query("SELECT no as playlist_id, filename as FileName, cover as CoverImage FROM media_item");
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

    public function mediaDelete(ServerRequestInterface $request, ResponseInterface $response){
        $mediaId = $request->getAttribute('mediaId');
        $mediaDelete = $this->container->db2->query("DELETE FROM media_item WHERE no = :mediaId");
        $mediaDelete->bindParam(':mediaId', $mediaId);
        $deleteStatus = "FAILED";

        $mediaCheck = $this->container->db2->query("SELECT * FROM media_item WHERE no = :mediaId");
        $mediaCheck->bindParam(':mediaId', $mediaId);
        $mediaCheck->execute();
        $mediaArray = $mediaCheck->fetchall(PDO::FETCH_OBJ);

        if(empty($mediaArray)){
            $deleteStatus = "FAILED";
            $deleteMsg = "MediaId: {$mediaId} does not exist.";
        }else{
            if($mediaDelete->execute()){
                $deleteStatus = "SUCCESS";
                $deleteMsg = "MediaId: {$mediaId} has been deleted";
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
}

 