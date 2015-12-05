<?php
/*
 * Auther: Maher Safadi March - 2014
 */
class ActionItem {
	private $itemName;
	private $operation;
	private $value;
	function __construct($itemName, $opString, $value) {
		$this->itemName = $itemName;
		$this->operation = $opString;
		$this->value = $value;
	}
	public function getItemName() {
		return $this->itemName;
	}
	public function setItemName($itemName) {
		$this->itemName = $itemName;
	}
	public function setOperation($operation) {
		$this->operation = $operation;
	}
	public function getOperation() {
		return $this->operation;
	}
	public function setValue($value) {
		$this->$value = $value;
	}
	public function getValue() {
		return $this->value;
	}
}
class ActionResult {
	private $fieldsNames;
	private $fieldsTypes;
	private $fieldsLengths;
	private $data;
	public function __constructOracle($queryResult) {
	}
	public function __construct($queryResult) {
		$this->fieldsNames = array ();
		$this->fieldsTypes = array ();
		$this->fieldsLengths = array ();
		$this->data = null;
		// Get Meta Data
		(new ReflectionMethod ( "ActionResult", ("__construct" . Configs::$dbEngine) ))->invoke ( $this, array (
				$queryResult 
		) );
	}
	public function __constructMyMysqli($queryResult) {
		$queryResult = $queryResult [0];
		if ($queryResult != null) {
			$i = 0;
			$ff = $queryResult->fetch_fields ();
			$t = array (
					"3" => "int",
					"253" => "string",
					"10" => "date",
					"252"=> "text"
			);
			while ( $i < count ( $ff ) ) {
				$meta = $ff [$i];
				$_f1 = $meta->name;
				$_f2 = $t[$meta->type];
				$_f3 = $meta->length;
				$this->fieldsNames [$i] = $_f1;
				$this->fieldsTypes [$i] = $_f2;
				$this->fieldsLengths [$i] = $_f3;
				$i ++;
			}
			$j = 0;
			$this->data = array ();
			while ( $row = $queryResult->fetch_assoc () ) {
				$rr = array ();
				for($i = 0; $i < count ( $row ); $i ++) {
					$rr [$i] = $row [$this->fieldsNames [$i]];
				}
				$this->data [$j ++] = $rr;
			}
		}
	}
	public function getFieldsNames() {
		return $this->fieldsNames;
	}
	public function getFieldsTypes() {
		return $this->fieldsTypes;
	}
	public function getFieldsLengths() {
		return $this->fieldsLengths;
	}
	public function __constructMysql($queryResult) {
		$queryResult = $queryResult [0];
		if ($queryResult != null) {
			$i = 0;
			while ( $i < mysql_num_fields ( $queryResult ) ) {
				$meta = mysql_fetch_field ( $queryResult, $i );
				$_f1 = $meta->name;
				$_f2 = $meta->type;
				$_f3 = mysql_field_len ( $queryResult, $i );
				$this->fieldsNames [$i] = $_f1;
				$this->fieldsTypes [$i] = $_f2;
				$this->fieldsLengths [$i] = $_f3;
				$i ++;
			}
			$j = 0;
			$this->data = array ();
			while ( $row = mysql_fetch_assoc ( $queryResult ) ) {
				$rr = array ();
				for($i = 0; $i < count ( $row ); $i ++) {
					$rr [$i] = $row [$this->fieldsNames [$i]];
				}
				$this->data [$j ++] = $rr;
			}
		}
	}
	public function getData() {
		return $this->data;
	}
	public function getRowAsMap($i) {
		$ret = null;
		try {
			if ($this->data != null) {
				if ($i < count ( $this->data )) {
					$r = $this->data [$i];
					$ret = array ();
					for($j = 0; $j < count ( $this->fieldsNames ); $j ++) {
						$ret ["" . $this->fieldsNames [$j]] = $r [$j];
					}
				}
			}
		} catch ( Exception $e ) {
			Logger::LogErr ( "ActionResult", $e );
		}
		return $ret;
	}
	public function __constructSqlServer($queryResult) {
	}
}
class Action {
	public static $select = 1;
	public static $executeFreeStatement = 5;
	private $type;
	private $primaryKeyName;
	private $primaryKeyValue;
	private $foreignKeys;
	private $name;
	private $user;
	private $parent;
	private $subActions;
	private $fields;
	private $actionResult;
	private $messages;
	private $extrawhere;
	private $groupBy;
	private $orderBy;
	private $from;
	private $to;
	public static $systemUser;
	public function __construct($actionType, $actionName, $actionUser) {
		$this->type = $actionType;
		$this->name = $actionName;
		$this->user = $actionUser;
		$this->subActions = array ();
		$this->fields = array ();
		$this->actionResult = null;
		$this->messages = array ();
		$this->parent = null;
		$this->extrawhere = "";
		Action::$systemUser = null;
	}
	public static $insert = 2;
	public function execute() {
		try {
			// Do Befor Listner
			
			// =========================================
			$db = ServiceProvider::getService ( "db" );
			$act = $db->execute ( $this );
			$this->primaryKeyValue = $act->getPrimaryKeyValue ();
			if ($act->getActionResult () != null)
				$this->actionResult = $act->getActionResult ();
			else
				$this->actionResult = new ActionResult ( null );
			// =========================================
			
			// Do After Listner
		} catch ( Exception $ex ) {
			Logger::LogErr ( "Action.execute", $ex );
			// Handle message
			$this->messages [count ( $this->messages ) - 1] = "msg1";
		}
	}
	public function getName() {
		return $this->name;
	}
	public function getUser() {
		return $this->user;
	}
	public function setUser($user) {
		$this->user = $user;
	}
	public function setName($name) {
		$this->name = $name;
	}
	public function getType() {
		return $this->type;
	}
	public static $delete = 3;
	public function setType($type) {
		$this->type = $type;
	}
	public function getFields() {
		return $this->fields;
	}
	public function setFields($fields) {
		if ($fields != null)
			$this->fields = $fields;
		else {
			$this->fields = array ();
		}
	}
	public function setExtraWhere($extraWhere) {
		$this->extrawhere = $extraWhere;
	}
	public static $update = 4;
	public function getExtraWhere() {
		return $this->extrawhere;
	}
	public function getSubActions() {
		return $this->subActions;
	}
	public function addMessage($msg) {
		$this->messages [count ( $this->messages ) - 1] = $msg;
	}
	public function setSubActions($children) {
		$this->subActions = $children;
	}
	public function addSubAction($action) {
		$this->subActions [] = $action;
		$action->parent = $this;
	}
	public function getActionResult() {
		return $this->actionResult;
	}
	public function setActionResult($actionResult) {
		$this->actionResult = $actionResult;
	}
	public function getMessages() {
		return $this->messages;
	}
	public function setMessages($messages) {
		$this->messages = $messages;
	}
	public function hasMessages() {
		if ($this->messages != null && count ( $this->messages ) > 0)
			return true;
		else
			return false;
	}
	public function getPrimaryKeyValue() {
		return $this->primaryKeyValue;
	}
	public function setPrimaryKeyValue($primaryKeyValue) {
		$this->primaryKeyValue = $primaryKeyValue;
	}
	public function getForeignKeys() {
		return $this->foreignKeys;
	}
	public function setForeignKeys($foreignKeys) {
		$this->foreignKeys = $foreignKeys;
	}
	public function getParent() {
		return $this->parent;
	}
	public function setParent($parent) {
		$this->parent = $parent;
	}
	public function getExecutefreestatement() {
		return executeFreeStatement;
	}
	public function getPrimaryKeyName() {
		return $this->primaryKeyName;
	}
	public function setPrimaryKeyName($primaryKeyName) {
		$this->primaryKeyName = $primaryKeyName;
	}
	public function setGroupBy($groupBy) {
		$this->groupBy = $groupBy;
	}
	public function getGroupBy() {
		return $this->groupBy;
	}
	public function setOrderBy($orderBy) {
		$this->orderBy = $orderBy;
	}
	public function getOrderBy() {
		return $this->orderBy;
	}
	public function setFrom($from) {
		$this->from = $from;
	}
	public function getFrom() {
		return $this->from;
	}
	public function setTo($to) {
		$this->to = $to;
	}
	public function getTo() {
		return $this->to;
	}
	public function addFieldForWhere($name, $op, $value) {
		$actionItem = new ActionItem ( $name, $op, $value );
		$this->fields [] = $actionItem;
	}
	public function addField($name, $value) {
		$actionItem = new ActionItem ( $name, null, $value );
		$this->fields [] = $actionItem;
	}
	public function getFieldValueByName($name) {
		if ($name == null || "" == $name)
			return null;
		
		$res = null;
		$i = 0;
		while ( $i < count ( $this->fields ) && $res == null ) {
			if ($this->fields [i]->getItemName () == name)
				$res = $this->fields [i]->getValue ();
			$i ++;
		}
		return $res;
	}
	public function updateFieldValue($key, $value) {
		if ($key != null && $key != "") {
			$field = null;
			
			$i = 0;
			while ( $i < count ( $this->fields ) && $this->field == null ) {
				if ($this->fields [i]->getItemName == name)
					$field = $this->fields [i];
				$i ++;
			}
			if ($field != null) {
				$field . setValue ( value );
			}
		}
	}
	public function getFieldsNamesForSelect() {
		$fieldsAsSTR = "";
		if ($this->fields == null || $this->fields == "") {
			$fieldsAsSTR = "*";
		} else {
			foreach ( $this->fields as $ff ) {
				$fieldsAsSTR .= $ff->getItemName () . ",";
			}
			if (count ( $fieldsAsSTR ) > 1)
				$fieldsAsSTR = substr ( $fieldsAsSTR, 0, - 1 );
		}
		return $fieldsAsSTR;
	}
	public function getFeildsAsWhere() {
		$fieldsAsSTR = " ";
		if ($this->fields == null || $this->fields == "") {
			$fieldsAsSTR = " 1=1 ";
		} else {
			foreach ( $this->fields as $ff ) {
				$fieldsAsSTR .= "`" . $ff->getItemName () . "` " . $ff->getOperation () . " '" . $ff->getValue () . "' and ";
			}
			if (count ( $fieldsAsSTR ) > 1)
				$fieldsAsSTR = substr ( $fieldsAsSTR, 0, - 4 );
		}
		return $fieldsAsSTR;
	}
	public static $TableFieldsAsnTypes = 6;
	public function getTableFieldsNamesAndTypes($p) {
	}
}
?>