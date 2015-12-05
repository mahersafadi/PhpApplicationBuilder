<?php
//Author: Maher.safadi@gmail.com

	try{
		@session_start();
	}
	catch (Exception $ex){
		
	}
	
	class lang{
		static private function checkLangInSession(){
			if($_SESSION['Nlang']== null || $_SESSION['Nlang'] == '')
				$_SESSION['Nlang']='ar';
		}
		
		static public function getByKey($key){
			$ret = "";
			$code = "ar";
			if(isset($_SESSION['Nlang'])){
				$code = $_SESSION['Nlang'];
			}
			$lang_detail = null;
			if(isset($_SERVER['lang_detail'])){
				$lang_detail = $_SERVER['lang_detail'];
			}
			else{
				$action = new Action(Action::$select, "lang_detail", Action::$systemUser);
				$action->addFieldForWhere("code","=", $code);
				$action->execute();
				$lang_detail = array();
				for($i=0; $i<count($action->getActionResult()->getData()); $i++){
					$m = $action->getActionResult()->getRowAsMap($i);
					if(is_array($m))
						$lang_detail[$m["key"]] = $m["value"];
				}
				$_SERVER['lang_detail'] = $lang_detail;
			}
			
			if(isset($lang_detail[$key]))
				$ret = $lang_detail[$key];
			else
				$ret = $key;			
			return $ret;
		}
		
		static public function currLangAsKeyValue(){
			$ret = "";
			$code = "ar";
			if(isset($_SESSION['Nlang'])){
				$code = $_SESSION['Nlang'];
			}
			$lang_detail = null;
			if(isset($_SERVER['lang_detail'])){
				$lang_detail = $_SERVER['lang_detail'];
			}
			else{
				$action = new Action(Action::$select, "lang_detail", Action::$systemUser);
				$action->addFieldForWhere("code","=", $code);
				$action->execute();
				$lang_detail = array();
				for($i=0; $i<count($action->getActionResult()->getData()); $i++){
					$m = $action->getActionResult()->getRowAsMap($i);
					if(is_array($m))
						$lang_detail[$m["key"]] = $m["value"];
				}
				$_SERVER['lang_detail'] = $lang_detail;
			}
			return $lang_detail;
		}
		
		static public function getDir(){
			if($_SESSION['Nlang'] == 'en'){
				return "ltr";
			}
			else{
				return "rtl";
			}
		}
		
		static public function getAlign(){
			if($_SESSION['Nlang'] == 'en'){
				return "left";
			}
			else{
				return "right";
			}
		}
		
		static public function getAntiAlign(){
			if($_SESSION['Nlang'] == 'en'){
				return "right";
			}
			else{
				return "left";
			}
		}
		
		static public function changeLang(){
			Logger::log("change lang");
			
			if($_SESSION['Nlang'] == 'en'){
				$_SESSION['Nlang'] = 'ar';
			}
			else if($_SESSION['Nlang'] == 'ar'){
				$_SESSION['Nlang'] = 'en';
			}
		}
		
		static public function getLang(){
			Lang::checkLangInSession();
			return $_SESSION['Nlang'];
		}
		
		static public function getUserNameFromSession(){
			$ret = $_SESSION["user_name"];
			if($ret)
				return $ret;
			else
				Lang::getByKey("not_logged_in");
		}
		
		static public function getLoginTimeFromSession(){
			$ret = $_SESSION["login_time"];
			if($ret){
				return $ret;
			}
			else
				Lang::getByKey("not_logged_in");
		}
		
		public function processEL($expr){
			$expr = $expr[0];
			if($expr == "dir"){
				return Lang::getDir();
			}
			else if($expr == "align"){
				return Lang::getAlign();
			}
			else if($expr == "antiAlign"){
				return Lang::getAntiAlign();
			}
			else
				return Lang::getByKey($expr);
		}
		
	}
?>