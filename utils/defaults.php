<?php
	class defaults{
		public function processEL($exp){
			try{
				return (new ReflectionMethod(__CLASS__, $exp[0]))->invoke((new ReflectionClass(__CLASS__))->newInstance(), null);
			}
			catch (Exception $ex){
				Logger::logWithSpecific("Error During execute default value, ".$ex->getMessage());
			}
		}
		
		public function today(){
			return (new FWDate())->toString(); 
		}
		
		public function userid(){
			try{
				$u_id ="";
				if(isset($_SESSION['user_id']))
					$u_id = $_SESSION['user_id'];
				
				return $u_id;
			}
			catch(Exception $ex){
				return "";
			}
		}
	}
	
?>