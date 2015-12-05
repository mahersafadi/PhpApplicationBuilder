<?php
//Author: Maher.safadi@gmail.com

class session{
	public function processEL($expr){
		
		if($expr != null && $expr != ""){
			if(is_array($expr))
				$expr = $expr[0];
			
			if(isset($_SESSION[''.$expr])){
				return $_SESSION[''.$expr];
			}
		}
		return $expr;
	}
}

class request{
	public function processEL($expr){
		if($expr != null && $expr != ""){
			if(is_array($expr))
				$expr = $expr[0];
			if(isset($_POST[''.$expr])){
				return $_POST[''.$_POST[''.$expr]];
			}
			if(isset($_GET[''.$expr])){
				return $_POST[''.$_GET[''.$expr]];
			}
			return "";
		}
		return "";
	}
}
?>