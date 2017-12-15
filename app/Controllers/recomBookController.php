<?php

namespace App\Controllers;

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;
use PDO;

/**
* 
*/
class recomBookController extends Controller
{

	public function recomBook(ServerRequestInterface $request, ResponseInterface $response)
	{

		$rec = $this->container->db2->query("SELECT book_id as bookID, book_name as bookName, book_image as bookImg, user_create as userCreate, datetime_create as bookUpdate FROM recommended_book");
		$rec->execute();
		$rec1 = $rec->fetchAll(PDO::FETCH_OBJ);


		
		$recStatus = 'SUCCESS';
		$recMsg    = '';

		$status = 	[
						'status' => $recStatus,
						'data'   => $rec1,
						'msg'    => $recMsg
					];

		$response = $this->response->withJson($status);

		return $response;
	}

	public function recomBookAdd(ServerRequestInterface $request, ResponseInterface $response)
	{
		$postData = $request->getParsedBody();
		$id = $postData['bookID'];
		$name = $postData['bookName'];
		$img = $postData['bookImg'];
		$userCreate = $postData['userCreate'];

		$checkBook = $this->container->db2->query("SELECT COUNT(book_id) AS count_book FROM recommended_book WHERE book_id = :id");
		$checkBook->bindParam(':id', $id);
		$checkBook->execute();
		$checkBook1 = $checkBook->fetchAll(PDO::FETCH_OBJ);
		if ($checkBook1[0]->count_book == 0) {
			$addBook = $this->container->db2->query("INSERT INTO recommended_book(book_id, book_name, book_image, user_create) VALUES(:id, :name, :img, :userCreate)");
			$addBook->bindParam(':id', $id);
			$addBook->bindParam(':name', $name);
			$addBook->bindParam(':img', $img);
			$addBook->bindParam(':userCreate', $userCreate);
			$addBook->execute();
			$status = 	[
							'status' => "SUCCESS",
							'data'   => "",
							'msg'    => "New records created successfully"
						];
		}else{
			$status = 	[
							'status' => "FAIL",
							'data'   => "",
							'msg'    => "DUPLICATED BOOK ID"
						];
		}

		

		
		$response = $this->response->withJson($status);
		return $response;

	}

	public function recomBookDelete(ServerRequestInterface $request, ResponseInterface $response)
	{
		$bookID = $request->getAttribute('bookID');

		$deleteBook = $this->container->db2->query("DELETE FROM recommended_book WHERE book_id = :id");
		$deleteBook->bindParam(':id', $bookID);
		$deleteBook->execute();

		$status = 	[
						'status' => "SUCCESS",
						'data'   => "",
						'msg'    => "Record deleted successfully"
					];
		//$response = $this->response->withJson($status);
		$response = $this->response->withJson($deleteBook);
		return $response;

	}

}