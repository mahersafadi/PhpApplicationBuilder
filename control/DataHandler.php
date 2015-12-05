<?php
// Auther maher.safadi@gmail.com
/*
 * parameters: type: data, op: template: executer: type:page pageName default type is data
 */
header ( 'Content-Type: text/html; charset=UTF-8' );
include_once 'BasicController.php';
$basicController = new BasicController ( '../' );
$basicController->__init ();

if (isset ( $_POST ["type"] )) {
	if($_POST["type"] == "delAttach"){
		try {
			$ret = (new ReflectionMethod ( BasicController::$Configurations ["ac"]['ah'], $_POST ["type"] ))->invoke ( (new ReflectionClass ( BasicController::$Configurations ["ac"]["ah"] ))->newInstance (), $_POST ['name'] );
			echo DataFixer::encode($ret);
		} catch ( Exception $ex ) {
			Logger::logWithSpecific ( __CLASS__, __FUNCTION__, __LINE__, "Error During apply delete file..", $ex );
		}
	}
	else if ($_POST ["type"] == "data") {
		if (isset ( $_POST ["op"] )) {
			try {
				$_1 = "DefaultDataHandler";
				if (isset ( $_POST ["tn"] )) {
					$templatesVars = get_class_vars ( "templates" );
					$templatesVars = $templatesVars[''.$_POST ["tn"]];
					$exec = null;
					if (isset ( $templatesVars ['executer'] ))
						$exec = $templatesVars ['executer'];
					if ($exec != null && $exec != '')
						$_1 = $exec;
				} else
					$_1 = isset ( $_POST ["executer"] ) ? $_POST ["executer"] : $_1;
				
				$inst = (new ReflectionClass ( $_1 ))->newInstance ();
				$rr = $inst->execute ( $_POST );
				
				$rr = DataFixer::encode ( $rr );
				echo $rr;
			} catch ( Exception $ex ) {
				Logger::logWithSpecific ( __CLASS__, __FUNCTION__, __LINE__, "Error During apply operation..", $ex );
			}
		}
	} else if ($_POST ["type"] == "upload") {
		try {
			$ret = (new ReflectionMethod ( BasicController::$Configurations ["ac"]['ah'], "uploadFile" ))->invoke ( (new ReflectionClass ( BasicController::$Configurations ["ac"]["ah"] ))->newInstance (), $_POST ['name'] );
			echo DataFixer::encode($ret);
		} catch ( Exception $ex ) {
			Logger::logWithSpecific ( __CLASS__, __FUNCTION__, __LINE__, "Error During apply Uploading..", $ex );
		}
	}
	else if($_POST["type"] == "gen"){
		try{
			$gen = $_POST["generator"];
			
		}
		catch(Exception $ex){
			Logger::logWithSpecific ( __CLASS__, __FUNCTION__, __LINE__, "Error During apply Uploading..", $ex );
		}
	}
}
class DefaultDataHandler {
	public function execute($infoArray) {
		try {
			$inst = (new ReflectionClass ( Utils::toUpper ( Utils::toLower ( $infoArray ["op"] ), 1 ) . "Bean" ))->newInstance ( $_POST );
			$inst->prepare ();
			$inst->execute ();
			$res = $inst->getResult ();
			return $res;
		} catch ( Exception $ex ) {
		}
	}
}
$p = ServiceProvider::getService ( "RequestFinilizer" );
$p->finish();
class DataFixer {
	public static function encode($arr) {
		$str = json_encode ( $arr );
		return $str;
	}
	public static function decocde($str) {
		// Now it is hardcoded, must be choosen from config,json, xml,....
		if (isset ( $str )) {
			if ($str [0] != '{')
				$str = substr ( $str, 1 );
			if ($str [strlen ( $str ) - 1] != '}')
				$str = substr ( $str, 0, - 1 );
			$str = preg_replace ( "/([a-zA-Z0-9_]+?):/", "\"$1\":", $str );
			$str = json_decode ( $str );
		}
		return $str;
	}
}
?>