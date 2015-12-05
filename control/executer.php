<?php
/**
 * Auther: maher.safadi@gmail.com
 * */
class ActionBean{
	protected $templateName;
	protected $template;
	public function prepare(){
		if($this->templateName != null && $this->templateName != ""){
			//Get From Templates template based on its name
			$templatesVars = get_class_vars("templates");
			//if($expr[0] == '$')
			//	$expr = substr($expr, 1);
			$selTemp = $templatesVars[''.$this->templateName];
			if($selTemp != null){
				//$this->templateName = $expr;
				$this->template = $selTemp;
			}
		}
	}
	public function execute(){

	}
}

class DBActionBean extends ActionBean{
	protected $dbTable;
	public function prepare(){
		parent::prepare();
		if($this->dbTable == null || $this->dbTable == ""){
			if($this->template != null && $this->template != ""){
				$this->dbTable= $this->template['dbtable'];
			}
		}
	}
	public function __construct(){

	}

	public function execute(){

	}
}

class SaveBean extends DBActionBean{
	protected $fields;
	protected $action;
// 	public function __construct($fields, $templateName=null, $dbTable = null){
// 		$this->templateName = $templateName;
// 		$this->dbTable = $dbTable;
// 		$this->fields;
// 	}
	public function __construct($infoArray){
		if(isset($infoArray["tn"]))
			$this->templateName =  $infoArray["tn"];
		if(isset($infoArray["dbtable"]))
			$this->dbTable = $infoArray["dbtable"];
		if(isset($infoArray["d"])){
			$this->fields = DataFixer::decocde($infoArray["d"]);
		}	
		$this->result = array();
	}
	
	public function prepare(){
		parent::prepare();
	}
	
	private function assignFields($fields){
		$action = null;
		if($fields != null && is_object($fields)){
			$op = "";
			if(isset($fields->{"__operation_type__"})){
				$op = $fields->{"operation_type"};
			}
			$action = new Action($op, $this->dbTable, Action::$systemUser);
			foreach ($fields as $k=>$v){
				if(($k != "__operation_type__") && ($k != $this->template["pk"]) && ($k != "__sub__")){
					$action->addField($k, $v);
				}
				else if($k == $this->template["pk"]){
					$action->setPrimaryKeyName($this->template["pk"]);
					if($v != null && $v != ""){
						//Check if exist
						$taction = new Action(Action::$select, $this->dbTable, Action::$systemUser);
						$taction->addFieldForWhere($k, '=', $v);
						$taction->execute();
						if($taction->getActionResult()->getData() != null && count($taction->getActionResult()->getData()) > 0){
							$action->setType(Action::$update);
							$action->setPrimaryKeyValue($v);
						}
						else{
							$action->setType(Action::$insert);
							$action->addField($k, $v);
						}
					}
				}
				else if($k == "__sub__"){
					$a = $this->assignFields($v);
					if($a != null)
						$action->addSubAction($a);
				}
			}
			if(($action->getType() == null) || ($action->getType() == "")){
				$action->setType(Action::$insert);
			}
		}
		return $action;
	}
	
	public function execute(){
		//check if operation is insert or it is delete
		if(!is_object($this->fields)){
			Logger::logWithSpecific(__CLASS__, __FUNCTION__, __LINE__, "fields are not set as array of json to be inserted");
		}
		else{
			try{
				$this->action = $this->assignFields($this->fields);
				$this->action->execute();
				if($this->action->hasMessages()){
					
				}
				else{
					return "[".$this->action->getPrimaryKeyName().":".$this->action->getPrimaryKeyValue()."]";
				}
			}
			catch (Exception $ex){
				Logger::logWithSpecific(__CLASS__, __FUNCTION__, __LINE__, "Error During execute save", $ex);
			}
		}
	}
	
	public function getAction(){
		return $this->action;
	}
}

class DeleteBean extends DBActionBean{
	protected $pkName;
	protected $pkValue;
	protected $deleteWhereAsArray;
	protected $deleteWhereAsString;
	protected $isCaseCade;
	protected $action;
	
	public function __construct($infoArray){
		if(isset($infoArray['casecade']))
			$this->isCaseCade = $infoArray['casecade'];
		if(isset($infoArray['condition'])){
			if(is_arra($infoArray['condition']))
				$this->deleteWhereAsArray = $infoArray['condition'];
			else
				$this->deleteWhereAsString = $infoArray['condition'];
		}
		if(isset($infoArray["tn"]))
			$this->templateName = $infoArray["tn"];
		if(isset($infoArray["dbtable"]))
			$this->dbTable = $infoArray["dbtable"];
		if(isset($infoArray["pk_name"])){
			$this->pkName = $infoArray["pk_name"];
		}
		
		if(isset($infoArray["pk_value"])){
			$this->pkValue = $infoArray["pk_value"];
		}
		
		if(isset($infoArray["dbtable"])){
			$this->dbTable = $infoArray["dbtable"];
		}
	}

	public function prepare(){
		parent::prepare();
		if($this->pkName == null || $this->pkName == ""){
			if($this->template != null){
				if(isset($this->template["pk"]))
					$this->pkName = $this->template["pk"];
			}
		}
	}
	
	public function execute(){
		try{
			$dbTable = null;
			if($this->template != null)
				$dbTable = $this->template["dbtable"];
			else if($this->dbTable != null && $this->dbTable != "")
				$dbTable = $this->dbTable;
			else
				Logger::logWithSpecific(__CLASS__, __FUNCTION__, __LINE__, "data base table is net set, so delete could not be complete");
			
			if($dbTable != null){
				$this->action = new Action(Action::$delete, $dbTable, Action::$systemUser);
				$this->action->setPrimaryKeyName($this->pkName);
				$this->action->setPrimaryKeyValue($this->pkValue);
				$this->action->execute();
			}
		}
		catch (Exception $ex){
			Logger::logWithSpecific(__CLASS__, __FUNCTION__, __LINE__, "Error during apply the bean", $ex);
		}
	}
	
	public function getResult(){
		$msgs = $this->action->getMessages();
		if(isset($msg) && count($msg)>0){
			$out = "";
			for($i=0; $i<count(v); $i++){
				$out .= $msg[$i]."\\n<br/>";  
			}
			return array("result"=>"false", "msg"=>$out);
		}
		else{
			return array("result"=>"true");
		}
	}
}

class SearchBean extends DBActionBean{
	protected $searchCriteria;
	protected $fieldsToDisplay;
	protected $result;

	public function __construct($infoArray){
		if(isset($infoArray["tn"]))
			$this->templateName =  $infoArray["tn"];
		if(isset($infoArray["dbtable"]))
			$this->dbTable = $infoArray["dbtable"];
		if(isset($infoArray["sc"])){
			$this->searchCriteria = DataFixer::decocde($infoArray["sc"]);
		}
		if(isset($infoArray["fieldsToDisplay"]))
			$this->fieldsToDisplay = $infoArray["fieldsToDisplay"];
		
		$this->result = array();
	}

	public function prepare(){
		parent::prepare();
		// 		if($this->searchCriteria != null && $this->searchCriteria != ""){
		// 			//collect search from template
		// 			$this->searchCriteria = array();
		// 			$i=0;
		// 			if(isset($this->template)){
		// 				if(isset($this->template["cells"])){
		// 					foreach ($this->template["cells"] as $currCell){
		// 						if(isset($currCell["searchable"]) || isset($currCell["searc"]))
			// 							$this->searchCriteria[$i] = $currCell;
			// 					}
			// 				}
			// 			}
			// 		}
	}

	public function execute(){
		try{
			if($this->dbTable == null || $this->dbTable == ""){
				Logger::logWithSpecific(__CLASS__, __FUNCTION__,"", "During execute search action, dbtable attribute did not found.", "");
			}
			else{
				$action = new Action(Action::$select, $this->dbTable, Action::$systemUser);
				//$this->searchCriteria = json_decode(	$this->searchCriteria);
				//$this->searchCriteria = $this->searchCriteria["data"];
				//foreach ($this->searchCriteria as $k=>$v){
				for($i=0; $i<count($this->searchCriteria); $i++){
					$curr = $this->searchCriteria[$i];
					try{
						if($curr->{"id"} != null){
							if($curr->{"search_operator"} != null){
								if($curr->{"value"} != null && $curr->{"value"} != ""){
									$action->addFieldForWhere($curr->{"id"}, $curr->{"search_operator"}, $curr->{"value"});
								}
							}
						}
					}
					catch(Exception $exx){
						$exx->getCode();
					}
				}
				$action->execute();
				if($action->getActionResult() != null){
					for($i=0; $i<count($action->getActionResult()->getData()); $i++){
						$row = $action->getActionResult()->getRowAsMap($i);
						$rowData = array();
						foreach ($row as $k=>$v){
							$inFieldsToView = false;
							if($this->fieldsToDisplay == null || $this->fieldsToDisplay = ""){
								$inFieldsToView = true;
							}
							else{
								$j = 0;
								while( ($j < count($this->fieldsToDisplay)) && (!$inFieldsToView)){
									if($k == $this->fieldsToDisplay[$j])
										$inFieldsToView = true;
									$j++;
								}
							}
							if($inFieldsToView){
								$rowData[$k] = $v;
							}
						}
						$this->result [] = $rowData;
					}
				}
			}
		}
		catch(Exception $e){
			Logger::logWithSpecific(__CLASS__, __FUNCTION__,__LINE__, "error ", $e);
		}
	}

	public function getResult(){
		return $this->result;
	}
}

class CreateBean extends DBActionBean{
	protected $row;
	public function __construct($infoArray){
		if(isset($infoArray["tn"]))
			$this->templateName = $infoArray["tn"];
		if(isset($infoArray["dbtable"]))
			$this->dbTable = $infoArray["dbtable"];
	}
	
	public function prepare(){
		parent::prepare();
		$this->row = array();
		$m = 0;
		if(is_object($this->template)){
			if(isset($this->template["cells"])){
				$cells = $this->template["cells"];
				for($i=0; $i<count($cells); $i++){
					$curr = $cells[$i];
					$id 	= $curr["id"];
					$val 	= null;
					if(isset($curr["default"]))
						$val = $curr["default"];
					$row[$m++] = array("id"=>$id, "default"=>$val);
				}
			}
			else{
				Logger::logWithSpecific(__CLASS__, __FUNCTION__, __LINE__, "Trying to create new row from template does not has a 'cells' attribute");
			}
		}
		else{
			//Check from dbtable
		}
	}
	
	public function execute(){
		if(is_object($this->row)){
			$defVals = new DefaultValuesBean($this->row);
			$defVals->prepare();
			$defVals->execute();
			$this->row = $defVals->getRow();
		}
	}
	
	public function getRow(){
		return $this->row;
	}
}

class DefaultValuesBean extends DBActionBean{
	/*
	 * set default values for selected row
	 * Default values can be set from outside using parameter
	 * Or It can be set from dbtable
	 * OR it can be set from template's cells attribute
	 * */
	protected $defaultFields;
	protected $row;
	public function __construct($row, $templateName = null, $dbTable = null, $defaultValues = null){
		$this->templateName = $templateName;
		$this->dbTable = $dbTable;
		$this->defaultFields = $defaultValues;
		$this->row = $row;
	}
	
	
	public function prepare(){
		parent::prepare();
		//set default fields
// 		if($this->defaultFields == null || $this->defaultFields = ""){
// 			$this->defaultFields = array();
// 			if($this->template != null && $this->template != ""){
// 				if(isset($this->template["cells"])){
// 					$cells = $this->template["cells"];
// 					if(is_array($cells)){
// 						$j = 0;
// 						for($i = 0; $i<count($cells); $i++){
// 							$curr = $cells[$i];
// 							if($curr != null && $curr != ""){
// 								if(isset($curr["default"])){
// 									$this->defaultFields[$j++] = $curr["default"];
// 								}
// 							}
// 						}
// 					}
// 					else{
// 						Logger::logWithSpecific(__CLASS__, __FUNCTION__, __LINE__, "It seems that template is exist, bu not 'cells' attribute is set", null);
// 					}
// 				}
// 				else{
// 					Logger::logWithSpecific(__CLASS__, __FUNCTION__, __LINE__, "template is not set, so default fields could not be set", null);
// 				}
// 			}
// 			else{
// 				//Check it from dbtable , get default values
// 				//not yet
// 			}
// 		}
	}
	
	public function execute(){
		if(!is_array($row)){
			Logger::logWithSpecific(__CLASS__, __FUNCTION__, __LINE__, "Row is not set as array, so, defaul values will not be set", null);
		}
		else{
			//Get Default Values
			if($this->row != null && $this->row != ""){
				for($i=0;$i<count($this->row); $i++){
					$curr = $this->row[$i];
					if(isset($curr["default"])){
						$defExp = $curr["default"];
						try{
							if($defExp[0] == '#' && $defExp[1] == "{"){
								$defExp = substr($defExp, 2);
								if($defExp[count($defExp) - 1] == "}"){
									$mainInjector = new MainInjector();
									$defExp = $mainInjector->processEL($defExp);
								}
								else{
									Logger::logWithSpecific(__CLASS__, __FUNCTION__, __LINE__, "not valid expression in default value");
								}
							}
							$curr["value"] = $defExp;
						}
						catch (Exception $ex){
							Logger::logWithSpecific(__CLASS__, __FUNCTION__, __LINE__, "Error in setting default value expression", $ex);	
						}
					}
				}
			}
		}
	}
	
	public function setRow($row){
		$this->row = $row;
	}
	
	public function getRow(){
		return $this->row;
	}
}
?>