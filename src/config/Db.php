<?php	

class Db {
	
	private $host = 'localhost';
	private $dbname = 'acontece_na_cidade';
	private $user = 'app_user';
	private $password = 'progweb3';

	public function connect(){

			$pdo = new PDO("mysql:host=".$this->host.";dbname=".$this->dbname,$this->user, $this->password);

			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    		$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);	

		



    	return $pdo;
	}
}