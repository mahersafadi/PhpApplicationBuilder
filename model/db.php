<?php
/*
 * Auther: Maher Safadi
* March - 2014
* */

interface DataBaseI {
	public function createConnection();
	public function getConnection();
	public function execute($p);
	public function closeConnection();
}

class Mysql implements DataBaseI {
	private $dbConn;

	public function __construct(){
		$v = 1;
	}

	public function createConnection(){
		if($this->dbConn == null){
			$this->dbConn = @mysql_connect(Configs::$host, Configs::$dbuser_name, Configs::$dbpassword);
			mysql_select_db(Configs::$dbname, $this->dbConn);
			mysql_set_charset('utf8');
		}
	}

	public function getConnection(){
		$this->createConnection();
		return $this->dbConn;
	}

	public function execute($p){
		$ok = true;
		$this->getConnection();
		@mysql_query("begin");
		try{
			$s = (new ReflectionMethod("MySql", ("___f".$p->getType())))->invoke($this,array($p));
			$p = $s;
		}
		catch (Exception $ex){
			$ok = false;
			mysql_query("rollback");
		}
		if($ok)
			mysql_query("commit");
		//$this->closeConnection();
		return $p;
	}

	public function ___f1($p1){
		$p1 = $p1[0];
		$mysqlString = "SELECT * FROM " . $p1->getName();
		//if (!trim($p1->getWhere()) == ''){
			$mysqlString .= " WHERE ".$p1->getFeildsAsWhere();
			if($p1->getExtraWhere() != null && $p1->getExtraWhere() != "")
				$mysqlString .=" and ".$p1->getExtraWhere();
		//}
		if (!trim($p1->getGroupBy()) == ''){
			$mysqlString .= " GROUP BY $p1->getGroupBy()";
		}
		if (!trim($p1->getOrderBy()) == ''){
			$mysqlString .= " ORDER BY $p1->getOrderBy()";
		}
		if ((int)$p1->getTo() != 0){
			$mysqlString .= " LIMIT ".$p1->getFrom().",".$p1->getTo();
		}		
		$mysqlString = ((Utils::endsWith($mysqlString, "and ") || Utils::endsWith($mysqlString, "and")))?substr($mysqlString, 0, -4):$mysqlString;
		$result = mysql_query($mysqlString);
		if (mysql_error() == ''){
			$actionResult = new ActionResult($result);
			$p1->setActionResult($actionResult);
		}
		else{
			$p1->addMessage(mysql_error());
		}
		return $p1;
	}

	public function ___f2($p){
		$p = $p[0];
		$mysqlString = "";
		if($p->getFields() != null){
			$sqlNames = "";
			$sqlValues = "";
			foreach ($p->getFields() as $f){
				$sqlNames .= "`".$f->getItemName()."`,";
				$sqlValues .= "'".$f->getValue()."',";
			}
			if(strlen($sqlNames) > 1)
				$sqlNames = substr($sqlNames, 0, -1);
			if(strlen($sqlValues) > 1)
				$sqlValues = substr($sqlValues, 0, -1);
			$mysqlString = "INSERT INTO " . $p->getName() . " (".$sqlNames.") VALUES (".$sqlValues.")";
		}
		$result = mysql_query($mysqlString);
		if (mysql_error() == ''){
			$last_id = mysql_insert_id();
			$actionResult = new ActionResult(null);
			$p->setActionResult($actionResult);
			$p->setPrimayrKeyValue($last_id);
		}
		else{
			$p->addMessage(mysql_error());
		}
		return $p;
	}

	public function ___f3($p){
		$p = $p[0];
		if($p != null && $p->getFields() != null){

			$sql = "DELETE FROM " . $p->getName() . " WHERE " ;
			foreach ( $p->getFields() as $f){
				$sql .= "`".$f->getItemName() . "` ".$f->getOperation()." '".$f->getValue()."' AND " ;
			}

			$sql = substr( $sql, 0, strlen($sql) - 4 ) ;

			mysql_query( $sql ) ;

			if ( mysql_error() == '' )
			{

			}
			else{
				$p->addMessage(mysql_error());
			}
		}
		else if($p->getPrimaryKeyValue() != null){
			$sql = "DELETE FROM " . $p->getName() . " WHERE `".$p->getPrimaryKeyName()."` = '".$p->getPrimaryKeyValue()."'" ;
			mysql_query( $sql ) ;
			if ( mysql_error() == '' )
			{

			}
			else{
				$p->addMessage(mysql_error());
			}
		}
		return $p;
	}


	public function ___f4($p){
		$p = $p[0];
		if($p != null && $p->getFields() != null){
			$sql = "UPDATE " . $p->getName() . " SET ";
			foreach ( $p->getFields() as $f){
				$sql .= "`".$f->getItemName() . "` = '".$f->getValue()."' , " ;
			}
			
			$sql = substr($sql, 0, strlen($sql) - 2);
			$sql .= " WHERE `".$p->getPrimaryKeyName()."`='".$p->getPrimaryKeyValue()."'";
			mysql_query($sql);
			if (mysql_error() == ''){
				
			}
			else{
				$p->addMessage(mysql_error());
			}
		}
		return $p;
	}

	private function ___f5($p){
		$p = $p[0];
		$mysqlString = $p->getName();
		$result = mysql_query($mysqlString);
		$actionResult = new ActionResult($result);
		$p->setActionResule($actionResult);
		return $p;
	}

	public function ___f6($p){
		$p = $p[0];
		$mysqlString = "SELECT * FROM " . $p->getName()." LIMIT 1";
		$result = mysql_query($mysqlString);
		$actionResult = new ActionResult($result);
		$p->setActionResule($actionResult);
		return $p;
	}

	public function closeConnection(){
		mysql_close($this->dbConn);
		$this->dbConn = null;
	}
}

class MyMysqli implements DataBaseI{
	private static $dbConn;
	
	public function __construct(){
		$v = 2;
	}
	
	public function createConnection(){
		if(MyMysqli::$dbConn == null){
			MyMysqli::$dbConn = new mysqli(Configs::$host, Configs::$dbuser_name, Configs::$dbpassword, Configs::$dbname);
		}
	}
	
	public function getConnection(){
		$this->createConnection();
		return MyMysqli::$dbConn;
	}
	
	public function execute($p){
		$ok = true;
		$this->getConnection();
		MyMysqli::$dbConn->query("begin");
		try{
			$s = (new ReflectionMethod("MyMysqli", ("___f".$p->getType())))->invoke($this,array($p));
			$p = $s;
		}
		catch (Exception $ex){
			$ok = false;
			MyMysqli::$dbConn->query("rollback");
		}
		if($ok)
			MyMysqli::$dbConn->query("commit");
		//$this->closeConnection();
		return $p;
	}
	
	public function ___f1($p1){
		$p1 = $p1[0];
		$mysqlString = "SELECT * FROM " . $p1->getName();
		//if (!trim($p1->getWhere()) == ''){
		$mysqlString .= " WHERE ".$p1->getFeildsAsWhere();
		if($p1->getExtraWhere() != null && $p1->getExtraWhere() != "")
			$mysqlString .=" and ".$p1->getExtraWhere();
		//}
		if (!trim($p1->getGroupBy()) == ''){
			$mysqlString .= " GROUP BY $p1->getGroupBy()";
		}
		if (!trim($p1->getOrderBy()) == ''){
			$mysqlString .= " ORDER BY $p1->getOrderBy()";
		}
		if ((int)$p1->getTo() != 0){
			$mysqlString .= " LIMIT ".$p1->getFrom().",".$p1->getTo();
		}
	
		$mysqlString = ((Utils::endsWith($mysqlString, "and ") || Utils::endsWith($mysqlString, "and")))?substr($mysqlString, 0, -4):$mysqlString;
		$result = MyMysqli::$dbConn->query($mysqlString);
		if (MyMysqli::$dbConn->error  == ''){
			$actionResult = new ActionResult($result);
			$p1->setActionResult($actionResult);
		}
		else{
			$p1->addMessage(MyMysqli::$dbConn->error);
		}
		return $p1;
	}
	
	public function ___f2($p){
		$p = $p[0];
		$mysqlString = "";
		if($p->getFields() != null){
			$sqlNames = "";
			$sqlValues = "";
			foreach ($p->getFields() as $f){
				$sqlNames .= "`".$f->getItemName()."`,";
				$sqlValues .= "'".$f->getValue()."',";
			}
			if(strlen($sqlNames) > 1)
				$sqlNames = substr($sqlNames, 0, -1);
			if(strlen($sqlValues) > 1)
				$sqlValues = substr($sqlValues, 0, -1);
			$mysqlString = "INSERT INTO " . $p->getName() . " (".$sqlNames.") VALUES (".$sqlValues.")";
		}
		$result = MyMysqli::$dbConn->query($mysqlString);
		if (MyMysqli::$dbConn->error == ''){
			$last_id = mysql_insert_id();
			$actionResult = new ActionResult(null);
			$p->setActionResult($actionResult);
			$p->setPrimaryKeyValue($last_id);
		}
		else{
			$p->addMessage(MyMysqli::$dbConn->error);
		}
		return $p;
	}
	
	public function ___f3($p){
		$p = $p[0];
		$ok = false;
		if($p != null){
			$f = $p->getFields();
			if(isset($f) && count($f) > 0){
				$ok = true;
				$sql = "DELETE FROM " . $p->getName() . " WHERE ";
				foreach ( $p->getFields() as $f){
					$sql .= "`".$f->getItemName() . "` ".$f->getOperation()." '".$f->getValue()."' AND " ;
				}
				
				$sql = substr( $sql, 0, strlen($sql) - 4 ) ;
				
				MyMysqli::$dbConn->query( $sql ) ;
				
				if ( MyMysqli::$dbConn->error == '' ){
					
				}
				else{
					$p->addMessage(MyMysqli::$dbConn->error);
				}
			}
		}
		if(!$ok){
			$pkVal =  $p->getPrimaryKeyValue();
			if($pkVal != null){
				$sql = "DELETE FROM " . $p->getName() . " WHERE `".$p->getPrimaryKeyName()."` = '".$p->getPrimaryKeyValue()."'" ;
				MyMysqli::$dbConn->query( $sql ) ;
				if ( MyMysqli::$dbConn->error == '' )
				{
			
				}
				else{
					$p->addMessage(MyMysqli::$dbConn->error);
				}
			}
		}
		return $p;
	}
	
	public function ___f4($p){
		$p = $p[0];
		if($p != null && $p->getFields() != null){
			$sql = "UPDATE " . $p->getName() . " SET ";
			foreach ( $p->getFields() as $f){
				$sql .= "`".$f->getItemName() . "` = '".$f->getValue()."' , " ;
			}
				
			$sql = substr($sql, 0, strlen($sql) - 2);
			$sql .= " WHERE `".$p->getPrimaryKeyName()."`='".$p->getPrimaryKeyValue()."'";
			MyMysqli::$dbConn->query($sql);
			if (MyMysqli::$dbConn->error == ''){
	
			}
			else{
				$p->addMessage(MyMysqli::$dbConn->error);
			}
		}
		return $p;
	}
	
	private function ___f5($p){
		$p = $p[0];
		$mysqlString = $p->getName();
		$result = MyMysqli::$dbConn->query($mysqlString);
		$actionResult = new ActionResult($result);
		$p->setActionResule($actionResult);
		return $p;
	}
	
	public function ___f6($p){
		$p = $p[0];
		$mysqlString = "SELECT * FROM " . $p->getName()." LIMIT 1";
		$result = MyMysqli::$dbConn->query($mysqlString);
		$actionResult = new ActionResult($result);
		$p->setActionResule($actionResult);
		return $p;
	}
	
	public function closeConnection(){
		if(MyMysqli::$dbConn != null){
			MyMysqli::$dbConn->close();
		}
		MyMysqli::$dbConn = null;
	}
}

// class Oracle implements DataBaseI{
// 	public function createConnection(){}
// 	public function getConnection(){}
// 	public function execute($p){}
// 	public function closeConnection(){}
// }

// class SQLServer implements DataBaseI{
// 	public function createConnection(){}
// 	public function getConnection(){}
// 	public function execute($p){}
// 	public function closeConnection(){}
// }

?>