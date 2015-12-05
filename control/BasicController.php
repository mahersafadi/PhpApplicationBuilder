<?php
/*
 * Auther: Maher Safadi, maher.safadi@gmail.com
* March - 2014
* */

class BasicController {
	public static $Configurations = null;
	public static $pages = null;
	private $dir;
	public function __construct($dir=''){
		$this->dir = $dir;
		if(isset($_SESSION['basic_config']))
			BasicController::$Configurations = $_SESSION['basic_config'];
		if(isset($_SESSION['pages']))
			BasicController::$pages = $_SESSION['pages'];
		
		if(BasicController::$pages == null){
			BasicController::$pages = array();
			try{
				$requestURI = $_SERVER['REQUEST_URI'];
				if($requestURI[0] == "/")
					$requestURI = substr($requestURI, 1);
				$x = explode("/", $requestURI);
				$x = $x[0];
				$path = $_SERVER["DOCUMENT_ROOT"]."/".$x."/__config__/pages.xml";
				$xml = simplexml_load_file($path);
				for ($i=0; $i<count($xml->pages->page); $i++){
					$ff = $xml->pages->page[$i];
					$attrs = $ff->attributes();
					$name = $attrs["name"];
					$name = (string)$name;
					$security = $attrs["security"];
					$security = (string)$security;
					
					$template = '';
					if(isset($attrs["template"])){
						$template = $attrs["template"];
						$template = (string)$template;
					}
					
					$title = "";
					if(isset($attrs["title"])){
						$title = $attrs["title"];
						$title = (string)$title;
					}
					else 
						$title = "NO_TITLE";
					
					$image = "";
					if(isset($attrs["image"])){
						$image = $attrs["image"];
						$image = (string)$image;
					}
					
					$url = (string)$ff;
					$mustAdd = true;
					if($security == "true" || $security=="1"){
						//Apply Security Here
					}
					if($mustAdd){
						BasicController::$pages[''.$name] = array("url"=>$url, "title"=>$title, "image"=>$image, "template"=>$template);
					}
				}
			}
			catch(Exception $ex){
				
			}
		}
		
		//if(BasicController::$Configurations == null){
		if(true){
			BasicController::$Configurations = array(); 
			try{
				$requestURI = $_SERVER['REQUEST_URI'];
				if($requestURI[0] == "/")
					$requestURI = substr($requestURI, 1);
				$x = explode("/", $requestURI);
				$x = $x[0];
				$path = $_SERVER["DOCUMENT_ROOT"]."/".$x."/__config__/config.xml";
				$xml = simplexml_load_file($path);
				//include required files
				$includes = array();
				$j = 0;
				for ($i =0; $i<count($xml->inlcudes->include) ; $i++){
					$ff = $xml->inlcudes->include[$i];
					$includes[$j++] = (string)$ff;
					
				}
				BasicController::$Configurations["includes"] = $includes;
				
				BasicController::$Configurations["host"] = (string)$xml->db->host;
				BasicController::$Configurations["dbname"]= (string)$xml->db->dbname;
				BasicController::$Configurations["dbuser_name"]= (string)$xml->db->dbuser_name;
				BasicController::$Configurations["dbpassword"]= (string)$xml->db->password;
				BasicController::$Configurations["dbEngine"]= (string)$xml->db->dbEngine;
				BasicController::$Configurations["default_time_zone"] = (string)$xml->default_time_zone;
				BasicController::$Configurations["mobile_support"]	= (string)$xml->mobile_support;
				BasicController::$Configurations["send_receive"]	= (string)$xml->send_receive_mode;
				BasicController::$Configurations["template_mode"]	= (string)$xml->template_mode;
				
				BasicController::$Configurations["lang_script"] = (string)($xml->lang_script);
				
				$s = array();
				for ($i=0; $i<count($xml->services->service); $i++){
					$ff = $xml->services->service[$i];
					$attrs = $ff->attributes();
					$name = $attrs["name"];
					$name = (string)$name;
					$class = $attrs["class"];
					$class = (string)$class;
					$method = $attrs["method"];
					$method = (string)$method;
					$s["".$name] = array($class, $method);
				}
				BasicController::$Configurations["services"] = $s;
				
// 				Configs::$services = $s;
// 				ServiceProvider::_f();
// 				BasicController::$Configurations = true;

				$injectors = array();
				$j = 0;
				for ($i =0; $i<count($xml->injectors->injector) ; $i++){
					$ff = $xml->injectors->injector[$i];
					$attrs = $ff->attributes();
					$name = $attrs["name"];
					$name = (string)$name;
					$value = $attrs["value"];
					$value = (string)$value;
					$injectors[$j++] = array("name"=>$name, "value"=>$value);
				}
				BasicController::$Configurations["injectors"] = $injectors;
				
				
				$path_d = $_SERVER["DOCUMENT_ROOT"]."/".$x."/__config__/domains.xml";
				$xml1 = simplexml_load_file($path_d);
				
				$domains = array();
				for ($i =0; $i<count($xml1->domains->domain) ; $i++){
					$currDomain = $xml1->domains->domain[$i];
					$attrs = $currDomain->attributes();
					$name = $attrs["name"];
					$name = (string)$name;
					$type = $attrs["type"];
					$type = (string)$type;
					if($type=='static'){
						$options = array();
						if($currDomain->option != null && count($currDomain->option) > 0)
							;
						for($k=0; $k<count($currDomain->option); $k++){
							$currOption = $currDomain->option[$k];
							$label = $currOption['label'];
							$value = $currOption['value'];
							if($label == null || $label == '');
							if($value == null || $value == '');
							$label = (string)$label;
							$value = (string)$value;
							$options[''.$label] = ''.$value;
						}
						$domain = array("type"=>$type, "options"=>$options);
						$domains[''.$name] = $domain;
					}
					else if($type == 'dynamic'){
						$table = $attrs['table'];
						$id = $attrs['id'];
						$label = $attrs['label'];
						$table = (string)$table;
						$id = (string)$id;
						$label = (string)$label;
						$domain = array("type"=>$type, "table"=>$table, "id"=>$id, "label"=>$label);
						$domains[''.$name] = $domain;
					}
				}
				BasicController::$Configurations["domains"] = $domains;
				
				$ac = array("temp_dir"=>"", "permenant_dir"=>"", "allowed_extensions"=>"", "max_size_in_kelo"=>"", "file_nammer"=>"");
				
				$acn = $xml->attachment_config;
				
				$temp_dir = $acn->temp_dir;
				$attachmentConfig["temp_dir"] = (string)$temp_dir;
				
				$permenant_dir = $acn->permenant_dir;
				$attachmentConfig["permenant_dir"] = (string)$permenant_dir;
				
				$allowed_extensions = $acn->allowed_extensions;
				$attachmentConfig["allowed_extensions"] = (string)$allowed_extensions;
				
				$max_size_in_kelo = $acn->max_size_in_kelo;
				$attachmentConfig["max_size_in_kelo"] = (string)$max_size_in_kelo;
				
				$fn = $acn->fn;
				$attachmentConfig["fn"] = (string)$fn;
				
				$ah = $acn->ah;
				$attachmentConfig["ah"] = (string)$ah;
				
				BasicController::$Configurations["ac"] = $attachmentConfig; 
				
				$_SESSION['basic_config']= BasicController::$Configurations;
				
				$cc = "";
			}
			catch(Exception $ex){
			}
		}
	}

	
	
	public function __init(){
		//load includes
		$includes = BasicController::$Configurations["includes"];
		for($i = 0; $i<count($includes); $i++){
			if($includes[$i] != null && $includes[$i] != ''){
				include_once ''.$this->dir.$includes[$i];
			}
		}
		
		//init Config
		Configs::$host = BasicController::$Configurations["host"];
		Configs::$dbname = BasicController::$Configurations["dbname"];
		Configs::$dbuser_name = BasicController::$Configurations["dbuser_name"];
		Configs::$dbpassword = BasicController::$Configurations["dbpassword"];
		Configs::$dbEngine = BasicController::$Configurations["dbEngine"];
		Configs::$default_time_zone = BasicController::$Configurations["default_time_zone"];
		Configs::$mobile_support = BasicController::$Configurations["mobile_support"];
		Configs::$send_receive = BasicController::$Configurations["send_receive"];
		Configs::$templateMode = BasicController::$Configurations["template_mode"];
				
		//run model 
		Configs::$services = BasicController::$Configurations["services"];
		Configs::$injectors = BasicController::$Configurations["injectors"];
		ServiceProvider::_f();
	}
	
	public function checlInsert(){
		
	}
	
	public function checlDelete(){
		
	}
}
?>