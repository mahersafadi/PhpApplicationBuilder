<?php
	//Auther. Maher.safadi@gmail.com
	
	@session_start();
	header ('Content-Type: text/html; charset=UTF-8');
	include_once '../control/BasicController.php';
	$basicController = new BasicController('../');
	$basicController->__init();
	
	class UIShell extends BasicController{
		private $pageToGenerate;
		private $lang;
		private $attrs ;
		public function __construct(){
			parent::__construct();
			//parent::__init();
			$this->attrs = array();
		}
		
		public function __destruct(){
			empty($this->attrs);
			$this->attrs = null;
		}
		
		public function generateResponse(){
			if($this->validateRequest()){
				if(!isset($_SESSION['Nlang']))
					$_SESSION['Nlang'] = 'ar';
				
				if(isset($_GET['changLang']))
				if($_GET['changLang'] != null && $_GET['changLang'] == '1'){
					Lang::changeLang();
				}
				//Checking For Security Level 1: Can User Access This page or not
				
				//===============================================================
				$generator = new FWGenerator();
				return $generator->generatePage($this->pageToGenerate);
			}
			else{
				Logger::log("uishell validate request is returns false");
			}
		}
		
		public function validateRequest(){
			try{
				foreach ($_REQUEST as $k=>$v){
					if($v != null && strlen($v) > 0){
						$this->attrs[$k] = $v;
					}
					else{
						$x1 = explode("_", $k);
						for ($i=0; $i<count($x1); $i++){
							$x2 = $x1[$i];
							if($x2 != null && strlen($x2) > 0){
								$x2 = explode("-", $x2);
								$kk = $x2[0];
								$vv = "";
								if(isset($x2[1]))
									$vv = $x2[1];
								$attrs[$kk] = $vv;
							}
						}
					}
				}
			}
			catch(Exception $ex){
				Logger::LogErr(get_class($this)." validateRequest", $ex);
			}
			
			if($attrs['p'] != null && $attrs['p'] != ""){
				if(isset(BasicController::$pages[$attrs['p']]))
					$this->pageToGenerate = BasicController::$pages[$attrs['p']];
				else
					return false; 
				return true;
			}
			return false;	
		}
	}
	
	//Convert Request Attributes to Array
	/*attributes are:
	 * p:page
	 * and other parameters
	 * seperator bweteen key and value is '-'
	 * seperator between parameters is '_'
	 */
	$_SESSION['user_name'] = 'test_user';
	$uiShell = new UIShell('../');
	$res = $uiShell->generateResponse();
	echo $res;
?>