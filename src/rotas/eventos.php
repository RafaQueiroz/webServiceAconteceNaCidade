<?php

	use \PSR\Http\Message\ServerRequestInterface as Request;
	use \PSR\Http\Message\ResponseInterface as Response;

	require '../vendor/autoload.php';

    require '../src/dao/EventoDao.php';

	$app = new \Slim\App;

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
            return $e->getMessage();
        }

        return '{aviso: {text:"Evento cadastrado com sucesso"}';
    });