<?php
/*
 * Auther: Maher Safadi
* March - 2014
* */
	class Logger{
		private static $file;
		
		private static function get(){
			
			if(Logger::$file == null){
				$reuestURI = $_SERVER['REQUEST_URI'];
				if($reuestURI[0] == '/' || $reuestURI[0] == '\\')
					$reuestURI = substr($reuestURI, 1);
				$reuestURI = explode("/", $reuestURI)[0];
				$path = $_SERVER["DOCUMENT_ROOT"]."\\".$reuestURI."\\log.log";
				Logger::$file = fopen($path, "w");
			}
		}
		public static function log($msg){
			try{
				Logger::get();
				fwrite(Logger::$file,$msg."\n");
				fputs(Logger::$file, "\n", 1);
			}
			catch (Exception $ex){
				
			}
		}
		
		public static function LogErr($msg, $ex){
			try{
				Logger::get();
				fwrite(Logger::$file,"Err:\n");
				fwrite(Logger::$file,$msg."\n");
				if($ex != null)
					fwrite(Logger::$file,$ex->getMessage()."\n");
				fputs(Logger::$file, "\n", 1);
			}
			catch(Exception $ex1){
				
			}
		}
		
		public static function logWithSpecific($className="-", $functionName="-", $lineNo="-", $msg, $ex=null){
			try{
				Logger::get();
				fwrite(Logger::$file,"Class: ".$className.", ");
				fwrite(Logger::$file,"Method: ".$functionName.", ");
				fwrite(Logger::$file,"LineNo: ".$lineNo.", ");
				fwrite(Logger::$file,"Message: ". $msg."\n");
				if($ex != null)
					fwrite(Logger::$file,$ex->getMessage()."\n");
				fputs(Logger::$file, "\n", 1);
			}
			catch(Exception $ex1){
			
			}
		}
	}
?>