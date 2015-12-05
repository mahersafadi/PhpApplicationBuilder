<?php
	class ModuleFormDetails extends Item{
		public function parseFromArray($e){
			parent::parseFromArray($e);
		}
		
		public function parseFromXML($xmlString){
			parent::parseFromXML($xmlString);
		}
		
		public function generate($mode=""){
			$res = "";
		}
		
		public function execute($infoArray) {
			try{
				$op = $infoArray["op"];
				if($op == 'save'){
					//Get form fields
					//Get form details fields
					//create action and execute one transaction
					$rr = DataFixer::decocde($infoArray['d']);
					$action = new Action(Action::$insert, "module", Action::$systemUser);
					$action->setPrimaryKeyName("module_id");
					$action->addField("region", $rr->{region});
					$action->addField("type", $rr->{type});
					$action->addField("title", $rr->{title});
					$action->addField("category", $rr->{category});
					foreach ($rr->{__sub__} as $k=>$v){
						$subAction = new Action(Action::$insert, "module_detail", Action::$systemUser);
						$subAction->setPrimaryKeyName("md_id");
						$subAction->setForeignKeys("module_id");
						$subAction->addField("k", $k);
						$subAction->addField("v", $v);
						$action->addSubAction($subAction);
					}
					$action->execute();
					return 1;
				}
				$x = 0;
			}
			catch(Exception $ex){
				Logger::logWithSpecific(__CLASS__, __FUNCTION__, __LINE__, "", $ex);
			}
		}
	}
?>