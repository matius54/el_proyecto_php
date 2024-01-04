<?php

class DBConfig {
	private static $instance = null;
	private $config;
	private function __construct(){
		$this->config = json_decode(file_get_contents("database_config.json"),true);
	}
	public static function getInstance(){
		if (self::$instance == null){
			self::$instance = new DBConfig();
		}
		return self::$instance;
	}
	public function config(){
		return $this->config;
	}
}

class DB {
	private static function loadConfig() : Array {
		return DBConfig::getInstance()->config();
	}
	private static function createDatabase(){
		$config = DB::loadConfig()["connection"];
		$servername = $config["url"];
		$username = $config["user"];
		$password = $config["pass"];
		$dbname = $config["database"];
		$db = new PDO("mysql:host=$servername", $username, $password);
		$db->exec("CREATE DATABASE IF NOT EXISTS $dbname");
	}

	private static function createTable($table) : bool {
		$conn = DB::connect();
		$sql = array_merge(DB::loadConfig()["structure"]["*"],DB::loadConfig()["structure"][$table]);
		return $conn->exec("CREATE TABLE IF NOT EXISTS $table (".join(", ",$sql).")");
	}

	private static function testForTable($table) : void {
		$conn = DB::connect();
		$conn->exec("SELECT * FROM $table LIMIT 1");
	}
	
	private static function showException($PDOException) : void {
		die("Error con la base de datos: " .$PDOException->getMessage());
	}

	private static function checkAllTables(){
		foreach(DB::loadConfig()["structure"] as $k => $_){
			if($k==="*")continue;
			try{
				DB::testForTable($k);
			}catch(PDOException $e){
				if($e->getCode() === "42S02"){
					//si no existe la tabla, la crea
					DB::createTable($k);
				}else{
					DB::showException($e);
				}
			}
		}
	}

	public static function initialize() : void {
		DB::createDatabase();
		DB::checkAllTables();
	}

	private static function connect() : PDO {
		$config = DB::loadConfig()["connection"];
		$servername = $config["url"];
		$username = $config["user"];
		$password = $config["pass"];
		$dbname = $config["database"];
		try{
			return new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
		}catch (PDOException $e){
			if($e->getCode()===1049){
				//no existe la base de datos, la crea
				DB::createDatabase();
				DB::checkAllTables();
				return DB::connect();
			}else{
				DB::showException($e);
			}
		}
	}

	private static function execute($sql, $paramList = []){
		$conn = DB::connect();
		try{
			if(is_array($paramList) && sizeof($paramList) > 0){
				$ps = $conn->prepare($sql);
				$ps->execute($paramList);
				return $ps->fetchAll();
			}else{
				return $conn->exec($sql);
			}
		} catch (PDOException $e){
			if($e->getCode() === "42S02"){
				DB::checkAllTables();
				return DB::execute($sql,$paramList);
			}else{
				DB::showException($e);
			}
		}
	}

	public static function getSecretFromUsername($username) {
		if(!isset($username) || !is_string($username)) return null;
		$result = DB::execute(DB::loadConfig()["queries"]["getPrivateFromUsername"],[$username]);
		if(sizeof($result)!==0)return $result[0][0];
	}
}

?>