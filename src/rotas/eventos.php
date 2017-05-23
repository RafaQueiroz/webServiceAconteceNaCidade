<?php

	use \PSR\Http\Message\ServerRequestInterface as Request;
	use \PSR\Http\Message\ResponseInterface as Response;

	require '../vendor/autoload.php';

	$app = new \Slim\App;

//	$app->get("/eventos", function(Request $request, Response $response){
//
//		$sql = "SELECT * FROM evento";
//
//		try{
//			$db = new Db();
//			$db->connect();
//			$stmt = $db->query($sql);
//			$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
//
//		} catch(PDOException $e) {
//			echo '{error: {text: "Erro ao executar query:'.$e->getMessage().'"}}'
//		}
//
//		return
//	});