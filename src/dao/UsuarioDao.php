<?php

namespace Dao;
use \PDO as PDO;

class UsuarioDao {

    private $db;

    /**
     * UsuarioDao constructor.
     */
    public function __construct() {
        $this->db = new \Config\Db();
    }

    /**
     * @return retorna um usuário ou null
     * @param $usuarioId
     */
    public function getUsuarioById(int $usuarioId){

        $sql = "SELECT * FROM usuario WHERE id = :usuarioId";

        $pdo = $this->db->connect();
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':usuarioId', $usuarioId);
        $stmt->execute();

        $this->db = null;
        $usuario = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $usuario;
    }

    public function getUsuarioByEmail(string $usuarioEmail){

        $sql = "SELECT * FROM usuario WHERE email = :usuarioEmail";

        $pdo = $this->db->connect();
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam("usuarioEmail", $usuarioEmail);

        $stmt->execute();
        $this->db = null;
        $usuario = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $usuario;
    }

    public function getUsuarioByEmailSenha(string $usuarioEmail, String $senha){

        $sql = "SELECT * FROM usuario WHERE email = :usuarioEmail AND senha = :senha";

        $pdo = $this->db->connect();
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam("usuarioEmail", $usuarioEmail);
        $stmt->bindParam("senha", $senha);

        $stmt->execute();
        $this->db = null;
        $usuario = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $usuario;
    }

    public function insereUsuario(string $nome, string $email, string $senha){

        try{
            $sql = "INSERT INTO usuario(nome, email, senha) VALUES (:nome, :email, :senha)";

            $pdo = $this->db->connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                ':nome'  => $nome,
                ':email' => $email,
                ':senha' => $senha
            ));

            $this->db  = null;
        } catch (\PDOException $e){

        }

    }

    public function getUsuarioByToken(string $token){
        $sql = "SELECT 
                  u.* 
                FROM 
                  usuario_token ut 
                INNER JOIN
                  usuario u
                ON 
                  ut.usuario_id = u.id
                WHERE 
                  ut.token = :token";

        $pdo = $this->db->connect();
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":token", $token);

        $stmt->execute();
        $this->db = null;
        $usuario = $stmt->fetch(PDO::FETCH_OBJ);

        return $usuario;
    }



}