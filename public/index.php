<?php
	use \PSR\Http\Message\ServerRequestInterface as Request;
	use \PSR\Http\Message\ResponseInterface as Response;


	require __DIR__.'/../vendor/autoload.php';

    require __DIR__.'/../src/config/Db.php';

    $app = new \Slim\App([
		'settings' => [
			'displayErrorDetails' => true
		]
	]);

    $app->get('/', function(Request $request, Response $response){
       return 'hello!';
    });

	$app->get('/hello/{name}', function (Request $request, Response $response) {
	    $name = $request->getAttribute('name');
	    $response->getBody()->write("Hello, $name");
	    return $response;
	});

	//ROTAS
	require __DIR__.'/../src/rotas/usuarios.php';
	require __DIR__.'/../src/rotas/eventos.php';

	$app->run();