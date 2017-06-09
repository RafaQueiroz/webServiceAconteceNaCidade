<?php

	use \PSR\Http\Message\ServerRequestInterface as Request;
	use \PSR\Http\Message\ResponseInterface as Response;

    require __DIR__.'/../dao/EventoDao.php';

    $app->post('/eventos/adicionar', function(Request $request, Response $response){

        $eventoNome = $request->getParam('nome');
        $eventoDesc = $request->getParam('descricao');
        $eventoEndereco = $request->getParam('endereco');
        $sDataInicio = $request->getParam('dataInicio');
        $sDataFim = $request->getParam('dataFim');
        $usuarioToken = $request->getParam('token');

        if(!isset($usuarioToken))
            return '{error: {text:"O token do usuário logado deve ser informado"}';

        if(!isset($eventoNome))
            return '{error: {text:"O nome é um campo obrigatório. Informe-o e tente novamente"}';

        if(!isset($sDataInicio) || !isset($sDataFim))
            return '{error: {text:"Data início e fim são campos obrigatórios. Informe-os e tente novamente"}';

        $dataInicio = DateTime::createFromFormat('d/m/Y H:i:s', $sDataInicio);
        $dataFim = DateTime::createFromFormat('d/m/Y H:i:s', $sDataFim);

        if($dataInicio > $dataFim)
            return '{error: {text:"A data inicio deve ser menor que a data de fim do evento."}';

        $usuarioDao = new \Dao\UsuarioDao();

        $proprietario = $usuarioDao->getUsuarioByToken($usuarioToken);

        if(!isset($proprietario))
            return '{error: {text:"Nenhum usuário foi encotrado com esse token."}';

        try{
            $eventoDao = new \Dao\EventoDao();
            $eventoDao->createEvento($eventoNome, $eventoDesc, $eventoEndereco, $proprietario->id, $dataInicio, $dataFim);

        } catch (\Exception $e){
            return '{error: {text:"'.$e->getMessage().'""}';
        }

        return '{aviso: {text:"Evento cadastrado com sucesso"}';
    });

    $app->get('/eventos/{token}/{eventoId}', function(Request $request, Response $response){

        $usuarioToken = $request->getAttribute('token');
        $eventoId = $request->getAttribute('eventoId');

        if(!isset($usuarioToken))
            return '{error: {text:"o token de acesso do usuário deve ser informado"}}';

        if(!isset($eventoId))
            return '{error: {text:"o id do evento deve ser informado"}}';

        $usuarioDao = new \Dao\UsuarioDao();
        $eventoDao = new \Dao\EventoDao();

        $usuario = $usuarioDao->getUsuarioByToken($usuarioToken);

        if($usuario == null)
            return '{error: {text:"O token não pertence a nenhum usuário cadastrado"}}';

        try{
            $evento = $eventoDao->getEventoById($eventoId);

            if($evento == null)
                return '{error: {text:"Nenhum evento foi encotrado"}';

            return json_encode($evento);
        } catch (PDOException $e){
            return '{error: {text:"'.$e->getMessage().'""}';
        }

    });

    $app->post('/eventos/atualizar', function(Request $request, Response $response){

        $eventoId = $request->getParam('eventoId');
        $eventoNome = $request->getParam('nome');
        $eventoDesc = $request->getParam('descricao');
        $eventoEndereco = $request->getParam('endereco');
        $sDataInicio = $request->getParam('dataInicio');
        $sDataFim = $request->getParam('dataFim');
        $usuarioToken = $request->getParam('token');

        if(!isset($eventoId))
            return '{error: {text:"O id do evento deve ser informado"}';

        if(!isset($usuarioToken))
            return '{error: {text:"O token do usuário logado deve ser informado"}';

        if(!isset($eventoNome))
            return '{error: {text:"O nome é um campo obrigatório. Informe-o e tente novamente"}';

        if(!isset($sDataInicio) || !isset($sDataFim))
            return '{error: {text:"Data início e fim são campos obrigatórios. Informe-os e tente novamente"}';

        $dataInicio = DateTime::createFromFormat('d/m/Y H:i:s', $sDataInicio);
        $dataFim = DateTime::createFromFormat('d/m/Y H:i:s', $sDataFim);

        if($dataInicio > $dataFim)
            return '{error: {text:"A data inicio deve ser menor que a data de fim do evento."}';

        $usuarioDao = new \Dao\UsuarioDao();

        $proprietario = $usuarioDao->getUsuarioByToken($usuarioToken);

        if(!isset($proprietario))
            return '{error: {text:"Nenhum usuário foi encotrado com esse token."}';

        $eventoDao = new \Dao\EventoDao();

        $evento = $eventoDao->getEventoById($eventoId);
        if($evento == null)
            return '{error: {text:"Nenhum envento foi encotrado com esse token."}';

        try{
            $eventoDao->atualizaEvento($eventoId, $eventoNome, $eventoDesc, $eventoEndereco,
                $proprietario->id, $dataInicio, $dataFim);
        } catch (\Exception $e){
            return '{error: {text:"'.$e->getMessage().'"}';
        }

        return '{aviso: {text:"Evento atualizado com sucesso"}';
    });

    $app->get('/eventos/busca/{token}/{nomeEvento}', function(Request $request, Response $response){

        $usuarioToken = $request->getAttribute('token');
        $nomeEvento = $request->getAttribute('nomeEvento');

        if(!isset($usuarioToken))
            return '{error: {text:"o token de acesso do usuário deve ser informado"}}';

        if(!isset($nomeEvento))
            return '{}';

        $usuarioDao = new \Dao\UsuarioDao();
        $eventoDao = new \Dao\EventoDao();

        $usuario = $usuarioDao->getUsuarioByToken($usuarioToken);

        if($usuario == null)
            return '{error: {text:"O token não pertence a nenhum usuário cadastrado"}}';

        try{
            $evento = $eventoDao->getEventosByNome($nomeEvento);

            if($evento == null)
                return '{}';

            return json_encode($evento);
        } catch (PDOException $e){
            return '{error: {text:"'.$e->getMessage().'""}';
        }
    });

    $app->get('/eventos/proprietario/{usuarioToken}', function(Request $request, Response $response){

        $usuarioToken = $request->getAttribute('usuarioToken');

        if(!isset($usuarioToken))
            return '{error: {text:"o token de acesso do usuário deve ser informado"}}';

        $usuarioDao = new \Dao\UsuarioDao();
        $eventoDao = new \Dao\EventoDao();

        $usuario = $usuarioDao->getUsuarioByToken($usuarioToken);

        if($usuario == null)
            return '{error: {text:"O token não pertence a nenhum usuário cadastrado"}}';

        try{
            $eventos = $eventoDao->getEventosByNome($usuario->id);

            if($eventos == null)
                return '{}';

            return json_encode($eventos);
        } catch (PDOException $e){
            return '{error: {text:"'.$e->getMessage().'""}';
        }
    });


