<?php

	use \PSR\Http\Message\ServerRequestInterface as Request;
	use \PSR\Http\Message\ResponseInterface as Response;


	require '../vendor/autoload.php';

    require '../src/dao/UsuarioDao.php';

	$app = new \Slim\App;

    $app->post('/usuarios/login', function (Request $request, Response $response){

        $email = $request->getParam('email');
        $senha = $request->getParam('senha');

        if($email == null || $email == '' || $senha == null || $senha == '')
            return '{error: {text: "E-mail ou senha inválidos."}}';

        $usuarioDao = new \Dao\UsuarioDao();

        try{
            $usuario = $usuarioDao->getUsuarioByEmailSenha($email, $senha);

            if($usuario == null)
                return '{error: {text: "E-mail ou senha incorreto."}}';

            $token = md5($email+$senha);

            return " {token: {'$token'}";

        } catch (PDOException $e) {
            return '{error: {text: "Não foi possivel completar a requisição: '.$e->getMessage().'"}}';
        }

    });

	$app->get('/usuarios/{id}', function(Request $request, Response $response){
		$usuarioId = $request->getAttribute('id');

        $usuarioDao = new \Dao\UsuarioDao();

		if(is_null($usuarioId) || !is_numeric($usuarioId))
		    return '{error: {text: "Erro! O id do usuário deve ser numérico"}}';

		try {
			$usuario = $usuarioDao->getUsuarioById($usuarioId);

			if($usuario == null)
			    return '{error: {text: "Nenhum usuário foi encotrado"}}';

			return json_encode($usuario);
        } catch(Exception $e){
			return '{error: {text: "Não foi possível cadastrar o usuário: '.$e->getMessage().'"}}';
		}
	});

	 //Adiciona usuários
	 $app->post("/usuarios/adicionar", function(Request $request, Response $response){

	 	$nome = $request->getParam('nome');
	 	$email = $request->getParam('email');
	 	$senha = $request->getParam('senha');
	 	$senha2 = $request->getParam('senha2');

	 	if($senha != $senha2)
	 		return '{error: {text: "senhas diferem"}}';

	 	$usuarioDao = new \Dao\UsuarioDao();

	 	$usuarioJaCadastrado = $usuarioDao->getUsuarioByEmail($email);

        if($usuarioJaCadastrado)
            return '{error: {text: "Este e-mail já está cadastrado. "}}';

        try{
            $usuarioDao = new \Dao\UsuarioDao();
            $usuarioDao->insereUsuario($nome, $email, $senha);
        } catch (PDOException $e){
            return '{error: {text: "Não foi possível cadastrar o usuário: '.$e->getMessage().'"}}';
        }

         return '{aviso: {text: "Usuário cadastrado com sucesso"}}';
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

	 $app->post("/usuarios/atualizar", function (Request $request, Response $response){

	     $usuarioId = $request->getParam('id');
	     $nome  = $request->getParam('nome');
	     $email = $request->getParam('email');
	     $senha = $request->getParam('senha');
	     $senha2 =$request->getParam('senha2');

	     if(!isset($senha) || !isset($senha2) || ($senha != $senha2))
	         return '{aviso: {text: "Senhas são diferentes"}}';

	     $sql = " \n".
            "UPDATE \n".
            "  usuario \n".
            "SET \n".
            "  nome = :nome, email = :email, senha = :senha \n".
            "WHERE \n".
            "  id = :usuario_id";

	     try{
	         $db = new Db();
	         $pdo = $db->connect();
	         $stmt = $pdo->prepare($sql);

	         $stmt->execute(array(
	            ':nome' => $nome,
                ':email' => $email,
                ':senha' => $senha,
                ':usuario_id' => $usuarioId
             ));

             return '{aviso: {text: "usuario atualizado"}}';

         } catch (PDOException $e){
             return '{error: {text: "Não foi possível remover o usuário: '.$e->getMessage().'"}}';
         }
     });