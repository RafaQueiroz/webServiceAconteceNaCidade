<?php

namespace Dao;
use \PDO as PDO;

class EventoDao {

    private $db;

    /**
     * EventoDao constructor.
     */
    public function __construct() {
        $this->db = new \Config\Db();
    }

    public function createEvento(string $nome, string $descricao, string $endereco,
                                 int $proprietarioId,\DateTime $dataInicio, \DateTime $dataFim){

        $sql = "INSERT INTO 
                    evento(nome, descricao, endereco, proprietario_id, data_inicio, data_fim) 
                  VALUES 
                    (:nome, :descricao, :endereco, :proprietarioId, FROM_UNIXTIME(:dataInicio), FROM_UNIXTIME(:dataFim))";

        try{
            $pdo = $this->db->connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                ':nome'           => $nome,
                ':descricao'      => $descricao,
                ':endereco'       => $endereco,
                ':proprietarioId' => $proprietarioId,
                ':dataInicio'     => $dataInicio->getTimestamp(),
                ':dataFim'        => $dataFim->getTimestamp()
            ));

            $this->db = null;
        } catch (PDOException $e){
            echo $e->getMessage();
        }

    }

    public function getEventoById(int $eventoId){
        $sql = "SELECT 
                  *
                FROM 
                  evento
                WHERE
                  id = :eventoId";

        $pdo = $this->db->connect();

        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
           ':eventoId' => $eventoId
        ));

        $evento =$stmt-> fetch(PDO::FETCH_OBJ);

        return $evento;
    }

}