<?php

	use \PSR\Http\Message\ServerRequestInterface as Request;
	use \PSR\Http\Message\ResponseInterface as Response;

    require __DIR__.'/../dao/UsuarioDao.php';

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

	$app->get('/usuarios/{token}', function(Request $request, Response $response){
		$usuarioToken = $request->getAttribute('token');

        $usuarioDao = new \Dao\UsuarioDao();

		if(!isset($usuarioToken))
		    return '{error: {text: "Erro! O token do usuário deve ser numérico"}}';

		try {
			$usuario = $usuarioDao->getUsuarioByToken($usuarioToken);

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
	    $usuarioToken = $request->getParam("usuarioToken");

	    if(!$usuarioToken)
            return '{error: {text: "Id do usuário inválido:'.$request->getParam("id").'"}}';

	    $usuarioDao = new \Dao\UsuarioDao();

	    $usuario = $usuarioDao->getUsuarioByToken($usuarioToken);

	    if($usuario == null)
	        return '{erro:{text:"Não há usuário para com esse token"}}';

         try{
            $usuarioDao->deletar($usuario->id);
        } catch (PDOException $e){
            echo '{error: {text: "Não foi possível remover o usuário: '.$e->getMessage().'"}}';
        }

     });

	 $app->post("/usuarios/atualizar", function (Request $request, Response $response){

	     $usuarioToken = $request->getParam('usuarioToken');
	     $nome  = $request->getParam('nome');
	     $email = $request->getParam('email');
	     $senha = $request->getParam('senha');
	     $senha2 =$request->getParam('senha2');

	     if(!isset($usuarioToken))
             return '{error: {text: "O token do usuário deve ser informado"}}';

	     if(!isset($senha) || !isset($senha2) || ($senha != $senha2))
	         return '{error: {text: "Senhas são diferentes"}}';

	     $usuarioDao = new \Dao\UsuarioDao();

	     $usuario = $usuarioDao->getUsuarioByToken($usuarioToken);

	     if($usuario == null)
             return '{erro:{text:"Não há usuário para com esse token"}}';

	     try{
	         $usuarioDao->atualizaUsuario($usuario->id, $nome, $email, $senha);

             return '{aviso: {text: "usuario atualizado com sucesso"}}';
         } catch (PDOException $e){
             return '{error: {text: "Não foi possível remover o usuário: '.$e->getMessage().'"}}';
         }
     });