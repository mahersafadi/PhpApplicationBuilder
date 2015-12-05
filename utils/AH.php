<?php
          //Maher Alsafadi
          //maher.safadi@gmail.com	
	class AH{
		private $fileOptions = null;
		private $errors;
		
		public function __construct(){
			$this->errors = array();
		}
		
		public function uploadFile($inputName){
			$fn = "";
			$type = "";
			
			if(isset($_FILES[''.$inputName])){
				$orginalName = $_FILES[''.$inputName]['name'];
				$config = BasicController::$Configurations['ac'];
				if($_FILES[''.$inputName]['size'] > ($config['max_size_in_kelo']*1000)){
					 $this->errors['file_size_more_than']= $config['max_size_in_kelo']." kb";
				}
				
				if(isset($_FILES[''.$inputName]['type'])){
					$type = $_FILES[''.$inputName]['type'];
				}
				
				$isAllowedType = false;
				$allowedTypes = $config["allowed_extensions"];
				if($allowedTypes == null || $allowedTypes == "")
					$isAllowedType = true;
				else{
					$allowedTypes = explode(",", $allowedTypes);
					for($i=0;$i<count($allowedTypes); $i++){
						if(Utils::toLower($allowedTypes[$i]) == Utils::toLower($type))
							$isAllowedType = true;
					}
				}
				
				$t = "";
				try{
					$t = explode(".", $orginalName);
					$t = $t[count($t)-1];
				}
				catch(Exception $exx){
					$t = explode("/", $_FILES[''.$inputName]['type'])[1];
				}
				if($isAllowedType && ($this->hasErrors()) == ""){
					$fn =   (new ReflectionMethod(Utils::toUpper(Utils::toLower($config['fn']), 1), ("getName")))->invoke((new ReflectionClass(Utils::toUpper(Utils::toLower($config['fn']), 1)))->newInstance(), array($t));
					$dir =  $config['permenant_dir'].$fn;
					move_uploaded_file($_FILES[''.$inputName]['tmp_name'], $dir);
					if(Utils::startsWith($_FILES[''.$inputName]['type'], "image")){
						//Hanlde image, resize it if needed
					}
				}
				else{
					$this->errors['type_is_not_allowed'] = $t;
				}
			}
			else{
				Logger::log("During try to upload a file '".$fileName."' from client, files array was not set");
			}
			//-------------------------------------------------------------------------------------------------
			if($this->hasErrors()){
				$es = "";
				foreach ($this->errors as $k=>$v){
					$es .= lang::getByKey($k)." ".lang::getByKey($v)."<br/>";
				}
				$ret = array("result" => "error", "errors"=>$es);
			}
			else{
				
				$ret = array("result"=>"ok","f"=>$fn, "d"=>$dir);//"file :../files/".$fn;
			}
			return $ret;
		}
		
		public function delAttach($name){
			try{
				if(unlink(BasicController::$Configurations['ac']['permenant_dir'].$name))
					return array("result"=>"ok");
				else
					return array("result"=>"error", "msg"=>lang::getByKey("an_error_accured_during_delte_file"));
			}
			catch(Exception $ex){
				Logger::logWithSpecific("", "", "", "Error During attaching file ", $ex);
				return array("result"=>"error", "errors"=>lang::getByKey("an_error_accured_during_delte_file"));
			}
		}
		
		public function hasErrors(){
			if($this->errors == null || count($this->errors) == 0)
				return false;
			else
				return true;
		}
		public function getErrors(){
			return $this->errors;
		}
		
		public function displayFileAsHTML($fileOptions){
			
		}
		
		public function deleteFile($fileOptions){
			
		}
	}
	
	class AN{
		public function getName($extension){
			if(is_array($extension))
				$extension = $extension[0];
			$fwDate = new FWDate();
			return $fwDate->toStringFullDate("","","").".".$extension;
		}
	}
?>