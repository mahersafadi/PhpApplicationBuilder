<?php
/*
 * Auther: Maher Safadi
* March - 2014
* */
class ServiceProvider{
	private static $services;
	public static function _f(){
		if(ServiceProvider::$services == null){
			ServiceProvider::$services = array();
			foreach (Configs::$services as $k=>$v){
				if($v != null){
					$s = (new ReflectionMethod($v[0], $v[1]))->invoke((new ReflectionClass($v[0]))->newInstance());
					ServiceProvider::$services[$k]=$s;
				}
			}
		}
		
	}
	
	public function __f(){
		try{
			if(Configs::$dbEngine != null){
				return (new ReflectionClass(Configs::$dbEngine))->newInstance();
			}
		}
		catch(Exception $ex){
			Logger::LogErr("ServiceProvider.__f", $ex);
		}
		return null;
	}
	
	public function ___f(){
		return new RequestFinilizer();
	}
	
	public static function getService($name){
		return ServiceProvider::$services[$name];
	}
}

class RequestFinilizer{
	public function finish(){
		try{
			$db = ServiceProvider::getService ( "db" );
			$db->closeConnection();
		}
		catch(Exception $ex){
			Logger::logWithSpecific(__CLASS__, __FUNCTION__, __LINE__, "Error During finish the request", $ex);
		}
	}
}
?>