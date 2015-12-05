<?php
/*
 * Auther: Maher Safadi
 * //Author: Maher.safadi@gmail.com
* March - 2014
* */
class Utils{
	public static function toUpper($str, $count=-1){
		$ll = strlen($str);
		if($count > 0)
			$ll = $count;
		for($i=0; $i<$ll; $i++)
			if(ord($str[$i]) >= 97 && ord($str[$i]) <= 122)
				$str[$i] = chr(ord($str[$i]) - 32);
		
		return $str;
	}

	public static function toLower($str, $count = -1){
		try{
			$ll = strlen($str);
			if($count > 0)
				$ll = $count;
			for($i=0; $i<$ll; $i++){
				if(ord($str[$i]) >= 64 && ord($str[$i]) <= 90){
					$str[$i] = chr(ord($str[$i]) + 32);
				}
			}
		}
		catch(Exception $ex){
			Logger::logWithSpecific(__CLASS__, __FUNCTION__, __LINE__, "str : ".$str, $ex);
		}
		return $str;
	}
	
	public static function startsWith($str, $startsWithWhat){
		$res = false;
		if($str){
			if($startsWithWhat){
				if(strlen($str)> strlen($startsWithWhat)){
					$res = true;
					$j = 0;
					$i = 0;
					while($i<strlen($str) && $res){
						if($j<strlen($startsWithWhat)){
							if(ord($str[$i]) != ord($startsWithWhat[$j])){
								$res = false;
							}
						}
						$i++;$j++;
					}
				}
			}
		}
		return $res;
	}
	
	public static function endsWith($str, $endsWithWhat){
		$res = false;
		if($str){
			if($endsWithWhat){
				if(strlen($str)> strlen($endsWithWhat)){
					$res = true;
					$j = strlen($endsWithWhat)-1;
					$i=strlen($str)-1;
					while($i>=0 && $res){
						if($j>=0){
							if(ord($str[$i]) != ord($endsWithWhat[$j])){
								$res = false;
							}
						}
						$i--;$j--;
					}
				}
			}
		}
		return $res;
	}
	
	public static function fetch($str){
		$x = "";
		$mainInjector = new MainInjector();
		for($i=0; $i<strlen($str); $i++){
			if($str[$i] == "#" && $str[$i+1] == "{"){
				$j = $i+1;
				while($str[$j] != "}")
					$j++;
				$injector = substr($str, $i+2, $j-2-$i);
				try{
					$result1 = $mainInjector->processEL($injector);
					if(is_array($result1))
						$x .= $result1['html'].$result1['js'];
					else
						$x .= $result1;
					$temp = $injector;
					$temp = explode(".", $temp);
					if($temp[0] == 'template'){
						$xx = $temp[1];
						if($xx[0] == '$')
							$xx = substr($xx, 1);
						$js .= " try{ init".$xx."();} catch(e){}";
					}
				}
				catch(Exception $exxx){
					Logger::log("During Generation, injection faced a problem, injector is:".$injector);
					Logger::LogErr(__CLASS__.",".__FUNCTION__.", ".__LINE__, $exxx);
				}
				$i = $j;
			}
			else{
				$x .= "".$str[$i];
			}
		}
		return $x;
	}
}
?>