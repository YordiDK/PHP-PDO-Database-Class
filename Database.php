<?php
class Database {
		private $host 			= DB_HOST;
		private $user 			= DB_USER;
		private $pass 			= DB_PASS;
		private $dbname 		= DB_NAME;
		
		private $database_handler;
		private $error;
		
		private $statement;
		private static $instance;
		
		/**
		 * __construct function.
		 * 
		 * @access public
		 * @param mixed $host
		 * @param mixed $user
		 * @param mixed $pass
		 * @param mixed $dbname
		 * @return void
		 */
		 private function __construct($host, $user, $pass, $dbname){
		//Database server name
			$dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
			
			$options = array(
				PDO::ATTR_PERSISTENT => true,
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
			);
			
			try {
				$this->database_handler = new PDO($dsn, $this->user, $this->pass, $options);
			}
			//Catch eventuele errors
			catch (PDOException $e) {
				$this->error = $e->getMessage();
			}
		}
		public static function getInstance(){
			if(!self::$instance){
				self::$instance = new Database($host, $user, $pass, $dbname);
			}
			return self::$instance;
		}
		/**
		 * query function.
		 * 
		 * @access public
		 * @param mixed $query
		 * @return void
		 */
		public function query($query)
		{
			$this->statement = $this->database_handler->prepare($query);
		}
		
		/**
		 * bind function.
		 * 
		 * @access public
		 * @param mixed $param
		 * @param mixed $value
		 * @param mixed $type (default: null)
		 * @return void
		 */
		public function bind($param, $value, $type = null){
		
			if(is_null($type))
			{
				switch(true) {
					case is_int($value):
					$type = PDO::PARAM_INT;
					break;
					case is_bool($value):
				    $type = PDO::PARAM_BOOL;
				    break;
				    case is_null($value):
				    $type = PDO::PARAM_NULL;
				    break;
				    default:
				    $type = PDO::PARAM_STR;
				}
			}
			
			$this->statement->bindValue($param, $value, $type);
		}
		
		/**
		 * execute function.
		 * 
		 * @access public
		 * @return void
		 */
		public function execute(){
			return $this->statement->execute();
		}
		
		/**
		 * resultset function.
		 * 
		 * @access public
		 * @return void
		 */
		public function resultset(){
			$this->execute();
			return $this->statement->fetchAll(PDO::FETCH_ASSOC);
		}
		
		/**
		 * single function.
		 * 
		 * @access public
		 * @return void
		 */
		public function single(){
			$this->execute();
			return $this->statement->fetch(PDO::FETCH_ASSOC);
		}
		
		public function rowCount(){
			return $this->statement->rowCount();
		}
		
		public function lastInsertId(){
			return $this->database_handler->lastInsertId();
		}
		
		public function beginTransaction(){
			return $this->database_handler->beginTransaction();
		}
		
		public function endTransaction(){
			return $this->database_handler->commit();
		}
		
		public function cancelTransaction(){
			return $this->database_handler->rollBack();
		}
		
		public function debugDumpParams(){
			return $this->statement->debugDumpParams();
		}
}