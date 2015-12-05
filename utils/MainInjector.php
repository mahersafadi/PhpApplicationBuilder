<?php
	//Author: Maher.safadi@gmail.com
	
	class MainInjector{
		public function processEL($expr){
			if($expr != null && $expr != ""){
				$t = explode(".", $expr);
				$class = null;
				if(count($t) > 1){
					$class = Utils::toLower($t[0]);
				}
				$rest = substr($expr, strlen($class)+1);
				$injector = null;
				$injectorFile = null;
				$injectors = Configs::$injectors;
				$i = 0;
				while($i < count($injectors) && $injector == null){
					$currInjector = $injectors[$i];
					
					if($currInjector["name"] == $class){
						$injector = $class;
						$injectorFile = $currInjector["value"]; 
					}
					$i++;
				}
				
				if($injector != null){
					$injector = Utils::toUpper($injector, 1);
					//include_once ''.$injectorFile;
					$ret = (new ReflectionMethod($injector, "processEL"))->invoke((new ReflectionClass($injector))->newInstance(), array($rest));
				}
				else{//There is no injector, so class.execute method is requested
					$method = explode(".", $rest);
					if(count($method) > 1){
						$method = $method[0];
						$rest = substr($rest, length($method)+1);
					}
					else{
						$method = $method[0];
						$rest = "";
					}
					$ret = (new ReflectionMethod($injector, $method))->invoke(new ReflectionClass($injector), $rest);
				}
			}
			return $ret;
		}
	}
?>