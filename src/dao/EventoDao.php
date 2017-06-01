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

    public function atualizaEvento(int $eventoId, string $nome, string $descricao, string $endereco,
                                 int $proprietarioId,\DateTime $dataInicio, \DateTime $dataFim){

        $sql = "UPDATE 
                    evento 
                SET
                    nome = :nome, descricao = :descricao, endereco = :endereco,
                    proprietario_id = :proprietarioId, data_inicio = FROM_UNIXTIME(:dataInicio),
                    data_fim = FROM_UNIXTIME(:dataFim)
                WHERE 
                    id = :eventoId";

        try{
            $pdo = $this->db->connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                ':eventoId'       => $eventoId,
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

    public function getEventosByNome(string $eventoNome){
        $sql = "SELECT 
                  *
                FROM 
                  evento
                WHERE
                  UPPER (nome) 
                LIKE
                  UPPER (:eventoNome)";

        $pdo = $this->db->connect();

        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':eventoNome' => "%$eventoNome%"
        ));

        $eventos =$stmt-> fetchAll(PDO::FETCH_OBJ);

        return $eventos;
    }

    public function getEventosByProprietario(int $proprietarioId){
        $sql = "SELECT 
                  *
                FROM 
                  evento
                WHERE
                  proprietario_id 
                LIKE
                  :proprietarioId";

        $pdo = $this->db->connect();

        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':proprietarioId' => $proprietarioId
        ));

        $eventos =$stmt-> fetchALl(PDO::FETCH_OBJ);

        return $eventos;
    }

}