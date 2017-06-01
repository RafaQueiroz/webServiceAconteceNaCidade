<?php
	use \PSR\Http\Message\ServerRequestInterface as Request;
	use \PSR\Http\Message\ResponseInterface as Response;


	require '../vendor/autoload.php';

    require '../src/config/Db.php';



$app = new \Slim\App([
		'settings' => [
			'displayErrorDetails' => true
		]
	]);

	$app->get('/hello/{name}', function (Request $request, Response $response) {
	    $name = $request->getAttribute('name');
	    $response->getBody()->write("Hello, $name");
	    return $response;
	});

	//ROTAS
	require '../src/rotas/usuarios.php';

	require '../src/rotas/eventos.php';

	$app->run();