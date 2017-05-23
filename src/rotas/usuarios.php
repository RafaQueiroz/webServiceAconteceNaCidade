<?php

	use \PSR\Http\Message\ServerRequestInterface as Request;
	use \PSR\Http\Message\ResponseInterface as Response;

	require '../vendor/autoload.php';

	$app = new \Slim\App;


	$app->get('/usuarios', function(Request $request, Response $response){
	   $sql = "SELECT * FROM usuario";

	   try{
	       $db = new Db();
	       $pdo = $db->connect();
	       $stmt = $pdo->query($sql);

	       $usuarios = $stmt->fetchAll();

	       echo json_encode($usuarios);

       } catch (PDOException $exception){
	       echo $exception->getMessage();
       }

    });

	$app->get('/usuarios/{id}', function(Request $request, Response $response){
		$id = $request->getAttribute('id');
		$sql = "SELECT * FROM usuario WHERE id = :usuarioId";

		try{
			$db = new Db();
			$pdo = $db->connect();
			$stmt = $pdo->prepare($sql);

			$stmt->bindParam(':usuarioId', $id);
            $stmt->execute();

            $db = null;
			$usuario = $stmt->fetchAll(PDO::FETCH_OBJ);

			echo json_encode($usuario);

		} catch(PDOException $e){
			echo '{error: {text: "Não foi possível cadastrar o usuário: '.$e->getMessage().'"}}';
		}
	});

	 //Adiciona usuários
	 $app->post("/usuario/adiciona", function(Request $request, Response $response){

	 	$name = $request->getParam('name');
	 	$email = $request->getParam('email');
	 	$password = $request->getParam('password');
	 	$password2 = $request->getParam('password2');

	 	if($password != $password2)
	 		echo '{error: {text: "senhas diferem"}}';


	 	// // if(emailAlreadyExists($email))
	 	// 	// echo '{error: {text: "E-mail já foi cadastrado"}}';

	 	$sql = "INSERT INTO usuario(nome, email, senha) VALUES (:nome, :email, :senha)";
	 	try{
	 		$db = new Db();
	 		$pdo = $db->connect();
	 		$stmt = $pdo->prepare($sql);
	 		$stmt->execute(array(
	 			':nome' => $name,
	 			':email' => $email,
	 			':senha' => $password
	 		));

	 		$db  = null;

            echo '{aviso: {text: "Usuário cadastrado com sucesso"}}';

	 	} catch(PDOException $e) {
	 		echo '{error: {text: "Não foi possível cadastrar o usuário: '.$e->getMessage().'"}}';
	 	}

	 	/*
	 	* Avaliar como salvar a imagem do usuário
	 	*/
	 	//$imagem = $request->getParam('imagem');
	 });

	 $app->post("/usuarios/deletar", function(Request $request, Response $response){
	    $usuarioId = $request->getParam("id");

	    if(!$usuarioId)
            return '{error: {text: "Id do usuário inválido:'.$request->getParam("id").'"}}';

	    $sql = "DELETE FROM usuario WHERE id = :usuario_id";

         try{
	        $db = new Db();
	        $pdo = $db->connect();

	        $stmt = $pdo->prepare($sql);

	        $stmt->execute(array(
	           ':usuario_id' => $usuarioId
            ));

            echo '{aviso: {text: "Usuário removido com sucesso"}}';

        } catch (PDOException $e){
            echo '{error: {text: "Não foi possível remover o usuário: '.$e->getMessage().'"}}';
        }

     });

	 $app->post("/usuarios/atualiza")