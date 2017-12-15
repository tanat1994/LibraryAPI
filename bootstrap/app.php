<?php

session_start();

require __DIR__ . '/../vendor/autoload.php';

/**
* 
*/

class Database
{
	private $pdo;
	
	public function __construct(PDO $pdo)
	{
		$this->pdo = $pdo;
	}

	public function query($sql)
	{
		return $this->pdo->prepare($sql);
	}
}

$app = new \Slim\App([
	'settings' => [
		'displayErrorDetails' => true,
	],
	
]);

$container = $app->getContainer();

//Access customer DB
$container['pdo'] = function () {

	$dbhost = "localhost";
	$dbusername = "root";
	$dbpassword = "";
	$dbname = "customer";

	$pdo = new PDO("mysql:host=". $dbhost .";dbname=". $dbname, $dbusername, $dbpassword);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $pdo;
};

//Connect to Cutomer DB
$container['db'] = function ($container) {
	return new Database($container->pdo);
}; 

//$pdo = new PDO("mysql:host=". $dbhost .";dbname=". $dbname.";charset=utf8", $dbusername, $dbpassword);


//Access hongkhai DB
$container['pdo2'] = function () {

	$dbhost = "localhost";
	$dbusername = "root";
	$dbpassword = "";
	$dbname = "hongkhai"; //hongkhai

	$pdo = new PDO("mysql:host=". $dbhost .";dbname=". $dbname.";charset=utf8", $dbusername, $dbpassword);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $pdo;
};

//Connect to HongKhai DB
$container['db2'] = function ($container) {
	return new Database($container->pdo2);
}; 

$container['HomeController'] = function ($container) {
	return new \App\Controllers\HomeController($container);
};

$container['loginController'] = function ($container) {
	return new \App\Controllers\loginController($container);
};

$container['permController'] = function ($container) {
	return new \App\Controllers\permController($container);
};

$container['recomBookController'] = function ($container) {
	return new \App\Controllers\recomBookController($container);
};

$container['scReportController'] = function ($container) {
	return new \App\Controllers\scReportController($container);
};

$container['mediaController'] = function ($container){
	return new \App\Controllers\mediaController($container);
};

$container['playlistManagement'] = function ($container){
	return new \App\Controllers\playlistManagement($container);
};

$container['bdReportController'] = function ($container){
	return new \App\Controllers\bdReportController($container);
};

$container['ssReportController'] = function ($container){
	return new \App\Controllers\ssReportController($container);
};

$container['sgReportController'] = function ($container){
	return new \App\Controllers\sgReportController($container);
};

$container['fgReportController'] = function ($container){
	return new \App\Controllers\fgReportController($container);
};

$container['memberController'] = function ($container){
	return new \App\Controllers\memberController($container);
};

$container['groupController'] = function ($container){
	return new \App\Controllers\groupController($container);
};


require __DIR__ . '/../app/routes.php';