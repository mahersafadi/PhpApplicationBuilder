<?php
	//Auther: Maher Alsfadi
	//Maher.safadi@gmail.com
	
	
	class Item {
		protected $id;
		protected  $style;
		protected  $cssClass;
		protected  $align;
		private $readonly;
		private $visable;
		
		private $onClick;
		private $onDbClick;
		private $onHover;
		private $onBlure;
		private $onChange;
		protected  $searchOperator;
		protected $templateName;
		protected $onselect;
		protected $onload;
		protected $default;
		protected $isSub;
		
		public function parseFromArray($e){
			try{
				if(isset($e["id"]))
					$this->id = $e["id"];
				else
					Logger::log(__CLASS__.".".__FUNCTION__."., id is not exist during parse, generating may made errors");
				if(isset($e["style"]))
					$this->style = $e["style"];
				if(isset($e["class"]))
					$this->cssClass = $e["class"];
				if(isset($e["align"]))
					$this->align = $e["align"];
				else
					$this->align = Lang::getAlign();
				
				if(isset($e["readonly"]))
					$this->readonly = $e["readonly"];
				
				if(isset($e["visable"])){
					$this->visable = $e["visable"];
					if($this->visable == "true")
						$this->visable = true;
					else
						$this->visable = false;
				}
				else
					$this->visable = true;
				
				if(isset($e["search_operator"]))
					$this->searchOperator = $e["search_operator"];
				
				if(isset($e["onclick"]))
					$this->onClick = $e["onclick"];
				if(isset($e["ondbclick"]))
					$this->onDbClick = $e["ondbclick"];
				if(isset($e["onhover"]))
					$this->onHover = $e["onhover"];
				if(isset($e["onblure"]))
					$this->onBlure = $e["onblure"];
				if(isset($e["onchange"]))
					$this->onChange = $e["onchange"];
				if(isset($e["template_name"])){
					$this->templateName = $e["template_name"];
					if($this->templateName[0] == '$')
						$this->templateName = substr($this->templateName, 1);
				}
				else
					$this->templateName = "";
				
				if(isset($e["onselect"])){
					$this->onselect = $e["onselect"];
				}
				
				if(isset($e["onload"])){
					$this->onload = $e["onload"];
				}
				else
					$this->onload = "";
				
				if(isset($e['default']))
					$this->default = $e['default'];
				else
					$this->default = null;
				
				if(isset($e["issub"]))
					$this->isSub = $e["issub"];
				else
					$this->isSub = false;
			}	
			catch (Exception $ex){
				Logger::LogErr(__CLASS__.".".__FUNCTION__.".".__LINE__.", Error During parse", $ex);
			}
		}
		
		public function parseFromXML($xmlString){
			try{
				
			}
			catch (Exception $ex){
				Logger::LogErr(__CLASS__.".".__FUNCTION__.".".__LINE__.", Error During parse", $ex);
			}
		}
		
		public function generate($mode=null){
			if($mode != null){
				if(is_array($mode))
					$mode = $mode["mode"];
			}
				
			$ret = "";
			if($mode == null)
				$ret = " id='".$this->id."' ";
			else if($mode == 'search')
				$ret = " id='__search__".$this->id."' ";
			
			$ret .= " mode='".$mode."' {value} {readonly} {disabled}";
			if($this->style != null && $this->style != ""){
				$ret .= " style='".$this->style." ";
				if($this->visable == false)
					$ret .= " display:none;";
				$ret .= "' ";
			}
			else{
				if(!$this->visable){
					$ret .= " style='display:none;' ";
				}
			}
			if($this->cssClass != null && $this->cssClass != "")
				$ret .= " class='".$this->cssClass."' ";
			if($this->align != null && $this->align != "")
				$ret .= " align='".$this->align."' ";
			if($this->readonly != null && $this->readonly != "")
				$ret .= " readonly='readonly' ";
			if($this->onClick != null && $this->onClick != ""){
				$func = $this->processJSFunc($this->onClick);
				if($func != "")
					$ret .= " onclick=\"".$func."\"";
			}
			if($this->onChange != null && $this->onChange != ""){
				$func = $this->processJSFunc($this->onChange);
				if($func != "")
					$ret .= " onchange=\"".$func."\"";
			}
			if($this->onBlure != null && $this->onBlure != ""){
				$func = $this->processJSFunc($this->onBlure);
				if($func != "")
					$ret .= " onblure=\"".$func."\"";
			}
			if($this->onDbClick != null && $this->onDbClick != ""){
				$func = $this->processJSFunc($this->onDbClick);
				if($func != "")
					$ret .= " ondbclick=\"".$func."\"";
			}
			if($this->onHover != null && $this->onHover != ""){
				$func = $this->processJSFunc($this->onHover);
				if($func != "")
					$ret .= " onhover=\"".$func."\"";
			}
			if($this->templateName != null && $this->templateName != "")
				$ret .= " tn='".$this->templateName."' ";
			
			if($this->onselect != null && $this->onselect != ""){
				$ret .= " onselect= '".$this->onselect."' ";
			}
			
			if($this->onload != null && $this->onload != ""){
				$func = $this->processJSFunc($this->onload);
				if($func != "")
					$ret .= " onload=\"".$func."\"";
			}
			
			
			if($this->default != null && $this->default != ""){
				$x = "";
				if(Utils::startsWith($this->default, "#{")){
					$x = ((new MainInjector())->processEL(substr($this->default, 2, -1)));
				}
				else{
					$x = $this->default;
				}
				$ret .= " value='".$x."' ";
			}
			
			return $ret;
		}
		
		private function processJSFunc($func){
			$xx = explode("(", $func);
			$rr = "";
			if(count($xx) > 1){
				$rr = $xx[0]."(this,".$xx[1];
			}
			else{
				$rr = $xx[0]."(this)";
			}
			return $rr;
		}
		
	}
	class Text extends Item{
		private $value;
		
		public function __construct(){
			
		}
		
		public function parseFromArray($e){
			parent::parseFromArray($e);
			if(isset($e["value"]))
				$this->value = $e["value"];
			if($this->searchOperator == null || $this->searchOperator == "")
				$this->searchOperator = "like";
		}
		
		public function parseFromXML($xmlString){
			parent::parseFromXML($xmlString);
		}
		
		public function generate($mode = null){
			$ret = "<input type='text' ";
			$ret .= parent::generate($mode);
			if($this->value != "" && $this->value != null)
				$ret .= " value='".$this->value."' ";
			$ret .= "/>";
			return array("html"=>$ret, "js"=>"");
		}
	}
	
	class Domain{
		private $domainName;
		private $domain;
		
		public function processEL($exp){
			if(is_array($exp))
				$exp = $exp[0];
			$this->domainName = $exp;
			return $this->generateAsOptions(true);
		}
		
		public function __construct(){
			
		}
		public function setDomainName($dn){
			$this->domainName = $dn;
		}
		public function getDomainName(){
			return $this->domainName;
		}
		
		private function prepare(){
			if(BasicController::$Configurations != null && isset(BasicController::$Configurations["domains"])){
				$domains = BasicController::$Configurations["domains"];
				if($domains != null){
					if(isset($domains[''.$this->domainName]))
						$this->domain = $domains[''.$this->domainName];
					else
						Logger::log("Error in preparing domain ".$this->domainName.", domain is not set");
				}
				else
					Logger::log("Error in preparing domain ".$this->domainName.", domains are not set");
			}
			else{
				Logger::log("Error in preparing domain ".$this->domainName.", Configurations are not set");
			}
		}
		
		public function generateAsOptions($withBlank=true){
			$ret = "";
			if($withBlank)
				$ret .= "<option value=''>".Lang::getByKey("ALL")."</option>";
			
			try{
				$this->prepare();
				if($this->domain != null){
					$type = $this->domain['type'];
					if($type == 'static'){
						$opts = $this->domain["options"];
						foreach ($opts as $label=>$value){
							$ret .= "<option value='".$value."' {selected".$value."}>";
							$ret .= lang::getByKey($label);
							$ret .= "</option>";
						}
					}
					else{
						$table = $this->domain["table"];
						$id = $this->domain["id"];
						$label = $this->domain["label"];
						
						if($table == null || $table == "")
							Logger::log("During Generate domain ".$this->domainName.", an error , domain is static and 'table' attribute is not set");
						
						if($id == null || $id == "")
							Logger::log("During Generate domain ".$this->domainName.", an error , domain is static and 'id' attribute is not set");
						
						if($label == null || $label == "")
							Logger::log("During Generate domain ".$this->domainName.", an error , domain is static and 'label' attribute is not set");
						
						$action = new Action(Action::$select, $table, Action::$systemUser);
						$action->execute();
						for($k=0; $k<count($action->getActionResult()->getData()); $k++){
							$row = $action->getActionResult()->getRowAsMap($k);
							if($row != null){
								$ret .= "<option value='".$row[''.$id]."' {selected".$row[''.$id]."} >";
								$ret .= $row[''.$label];
								$ret .= "</option>";
							}
						}						
					}
				}
			}
			catch(Exception $ex){
				Logger::logWithSpecific(__CLASS__, __FUNCTION__, __LINE__, "Error During Generate Domain As Options", $ex);
			}
			return $ret;
		}
		
		public function generateAsActionResult(){
			
		}
	}
	
	class Select extends Item{
		private $domainName;
		private $value;
		
		public function __construct(){
			$this->domainName = null;
			$this->value = null;
		}
		
		public function parseFromArray($e){
			parent::parseFromArray($e);
			if(isset($e["value"]))
				$this->value = $e["value"];
			if(isset($e["domain"]))
				$this->domainName = $e["domain"];
			if($this->searchOperator == null || $this->searchOperator == "")
				$this->searchOperator = "=";
		}
		
		public function parseFromXML($xmlString){
			parent::parseFromXML($xmlString);
			//parse value
			//parse domain
		}
		
		public function generate($mode = null){
			$ret = "<select ";
			$ret .= parent::generate($mode);
			$ret .= ">";
			//generate domain here
			$domain = new Domain();
			$domain->setDomainName(''.$this->domainName);
			$ret .= $domain->generateAsOptions(true);
			$ret .= "</select>";
			return array("html"=>$ret, "js"=>"");
		}
	}
	
	
	
	class Date extends Item{
		private $value;
		public function __construct(){
			$this->value = null;
		}
		
		public function parseFromArray($e){
			parent::parseFromArray($e);
			if($this->searchOperator == null || $this->searchOperator == "")
				$this->searchOperator = ">=";
		}
		
		public function parseFromXML($xmlString){
			parent::parseFromXML($xmlString);
			//parse value
			//parse domain
		}
		
		public function generate($mode = null){
			$ret = "<input type='text' ";
			$ret .= parent::generate($mode);
			if($this->value != null && $this->value != "")
				$ret .= " value='". $this->value."' ";
			$ret .= " />";
			$rr = array("html"=>$ret,  "js"=>"$('#".$this->id."').datepicker({changeMonth:true, changeYear:true,dateFormat:'yy-mm-dd'});");
			return $rr;
		}
	}
	
	class Button extends Item{
		private $label;
		public function __construct(){
			$this->label = null;
		}
		
		public function parseFromArray($e){
			parent::parseFromArray($e);
			if(isset($e["value"]))
				$this->label = $e["value"];
			
		}
		
		public function parseFromXML($xmlString){
			parent::parseFromXML($xmlString);
			//parse label item
			
		}
		
		public function generate($mode=null){
			$ret = "<input type='button'  ";
			$ret .= parent::generate($mode);
			$ret .= " value='".Lang::getByKey($this->label)."' ";
			$ret .= " />";
			return array("html"=>$ret, "js"=>"");
		}
	}
	
	class Link extends Item {
		private $href;
		private $label;
		public function __construct(){
			$this->href = null;
			$this->label = null;
		}
		public function parseFromArray($e){
			parent::parseFromArray($e);
			if(isset($e["href"]))
				$this->href = $e["href"];
			if(isset($e["label"]))
				$this->href = $e["label"];
				
		}
		public function parseFromXML($xmlString){
			parent::parseFromXML($xmlString);
			//parse href && label
		}
		public function generate($mode = null){
			$ret .= " <a ";
			$ret .= parent::generate($mode);
			if($this->href != null && $this->href != "")
				$ret .= " href='".$this->href."' ";
			$ret .= " >";
			if($this->label != null && $this->label != '')
				$ret .= lang::getByKey($this->label);
			$ret .= "</a>";
			return array("html"=>$ret, "js"=>"");
		}
	}
	
	class Label extends Item{
		private $value;
		public function __construct(){
			$this->value = null;
		}
		
		public function parseFromArray($e){
			parent::parseFromArray($e);
			if(isset($e["value"]))
				$this->value = $e["value"];
		}
		
		public function parseFromXML($xmlString){
			parent::parseFromXML($xmlString);
		}
		
		public function generate($mode=null){
			$ret = "<span";
			$ret .= parent::generate($mode);
			$ret .= ">";
			if($this->value != null && $this->value != "")
				$ret .= lang::getByKey($this->value);
			$ret .= "</span>";
			return array("html"=>$ret, "js"=>"");
		}
	}
	
	class Checkbox{
		public function __construct(){
				
		}
		
		public function parseFromArray($e){
			parent::parseFromArray($e);
		}
		
		public function parseFromXML($xmlString){
			parent::parseFromXML($xmlString);
		}
		
		public function generate($mode = null){
			return array("html"=>"", "js"=>"");
		}
	}
	
	class Radiobox{
		public function __construct(){
			
		}
		
		public function parseFromArray($e){
			parent::parseFromArray($e);
		}
		
		public function parseFromXML($xmlString){
			parent::parseFromXML($xmlString);
		}
		
		public function generate($mode = null){
			return array("html"=>"", "js"=>"");
		}
	}
	
	class Blank extends Item{
		public function __construct(){
				
		}
		
		public function parseFromArray($e){
			parent::parseFromArray($e);
		}
		
		public function parseFromXML($xmlString){
			parent::parseFromXML($xmlString);
		}
		
		public function generate($mode = null){
			$ret = "<span ".parent::generate($mode)." >";
			$ret .= "</span>";
			return $ret;
		}
	}
	
	class Editor extends  Item {
		private $value;
		public function __construct(){
			
		}
		
		public function parseFromArray($e){
			parent::parseFromArray($e);
			if(isset($e["value"]))
				$this->value = $e["value"];
			if($this->searchOperator == null || $this->searchOperator == "")
				$this->searchOperator = "like";
		}
		
		public function parseFromXML($xmlString){
			parent::parseFromXML($xmlString);
		}
		
		public function generate($mode = null){
			$html = "<textarea ". parent::generate() ."class=\"ckeditor\" cols='80' rows='10' style=\"visibility: hidden; display: none;\"></textarea>";
			return array("html"=>$html, "js"=>"");
		}
	}
	
	class Attachment extends Item{
		private $type;//image,vedio,pdf,....
		private $isMultiple;
		private $displayIn;//same,blank
		private $deleteAllow;//show delete btn for it
		private $width;//if it is image, the width of it
		private $height;//if it is image, the height of it
		private $alt;//if it is image, alt description
		
		public function __construct(){
			
		}
		
		public function parseFromArray($e){
			parent::parseFromArray($e);
			 
			$this->isMultiple = (isset($e['multiple']))?$e['multiple']:false;
			$this->type = 		(isset($e['subtype']))? $e['subtype']:'none';
			$this->displayIn = 	(isset($e['display_in']))? $e['display_in']:false;
			$this->deleteAllow = (isset($e['delete_allow'])) ? $e['delete_allow']:true;
			$this->width = 		(isset($e['width'])) ? $e['width']:'none';
			$this->height = 	(isset($e['height'])) ? $e['height']:'none';
			$this->alt	  = 	(isset($e['alt'])) ? lang::getByKey($e['alt']):lang::getByKey($this->id);
		}
		
		public function parseFromXML($xmlString){
			parent::parseFromXML($xmlString);
		}
		
		public function generate($mode = null){
			$ret = "<div class='attachment'>";
			$ret .= "<input type='file' id='file".$this->id."' ";
			//$ret .= parent::generate($mode);
			$ret .= ($this->isMultiple)? " multiple='multiple' ":"";
			$ret .= " subtype='".$this->type."' ";
			$ret .=	" displayin='".$this->displayIn."' ";
			$ret .=	" deleteallow='".$this->deleteAllow."' ";
			$ret .=	" width='".$this->width."' ";
			$ret .=	" height='".$this->height."' ";
			$ret .=	" alt='".$this->alt."' ";
			$ret .= "/>";
			$ret .= 	"<input type='hidden' id='".$this->id."' />";
			$ret .=		"<div style='display:none;' id='dir".$this->id."'></div>";
			$ret .=		"<div style='display:none;' id='err".$this->id."'></div>";
			$ret .=		"<div style='display:none;' id='delete".$this->id."'><img src='images/delete.png' /></div>";
			$ret .= "</div>";
			return array("html"=>$ret, "js"=>" new FileUpploader('".$this->id."'); ");
		}
	}
	
	class Form extends Item{
		/*
		 * Form:
		 * Has item attributes
		 * Has Special Attributs
		 * 	dbTable: data base table to connect with
		 * 	pk: primary key name of data base table
		 *  title: title of form [key in db]
		 *  cellsInRow: number of cells on each row
		 * 	cells: cells of form
		 * Has Cells 
		 * Each Cell Has
		 * 	-	label
		 * 	- 	id
		 * 	-	align
		 * 	-	type 		[text, date, select, checkbox, radiobox, button, link]
		 * 	-	value
		 * 	- 	required	[true: a star'*' must be set near, false]
		 * 	-	max_length	[]
		 * 	-	validator	[n:numbers only, t:text only, nt: text and numbers, d:data, e:email]
		 *  - 	domain		: just for select	*	
		 *  -   action		: just for button
		 *  -	href		: just for link
		 *  - 	ref_cell	: name of cell the change the result
		 *  -	ref_method	: method that do it.
		 *  -	onclick
		 *  -	ondbclick
		 *  -	onhover
		 *  -	onblure
		 *  -	onchange 
		 * */
		
		protected  $formTitle;
		protected $dbTable;
		protected $pk;
		protected $cells;
		protected $dbRow;
		protected $cellsInRow;
		protected $ok;
		protected $cancel;
		protected $executer;
		protected $sub;
		
		public function __construct(){
			$this->formTitle = "---";
			$this->dbTable = null;
			$this->pk = null;
			$this->cells = null;
			$this->dbRow = null;
			$this->fields = null;
			$this->cellsInRow = null;
			$this->ok = null;
			$this->cancel = null;
			$this->executer = "default";
			$this->sub = null;
		}
		
		public function parseFromArray($e){
			parent::parseFromArray($e);
			try{
				if(isset($e["title"]))
					$this->formTitle = $e["title"];
				if(isset($e["dbtable"]))
					$this->dbTable = $e["dbtable"];
				else
					Logger::logWithSpecific(__CLASS__, __FUNCTION__, __LINE__, "dbtable property is not exist, form my make error in generation");
				
				if(isset($e["pk"]))
					$this->pk = $e["pk"];
				else
					Logger::logWithSpecific(__CLASS__, __FUNCTION__, __LINE__, "primary key name property is not exist, form may make error");
				
				if(isset($e["cells"]))
					$this->cells = $e["cells"];
				else{
					Logger::logWithSpecific(__CLASS__, __FUNCTION__, __LINE__, "fields property is not exist, generation will go during defaults");
				}
				
				if(isset($e["dbRow"]))
					$this->dbRow = $e["dbRow"];
				if(isset($e["cellsInRow"]))
					$this->cellsInRow = $e["cellsInRow"];
				else
					$this->cellsInRow = 2;
				
				if(isset($e["ok"]))
					$this->ok = $e["ok"];
				else
					$this->ok = "dafault";
				
				if(isset($e["cancel"]))
					$this->cancel = $e["cancel"];
				else
					$this->cancel = "default";
				
				if(isset($e["executer"]))
					$this->executer = $e["executer"];
				
				if(isset($e["sub"]))
					$this->sub = $e["sub"];
				//--------------------------------------------------------------------------------------
				if($this->cells == null){
					$action = new Action(Action::$TableFieldsAsnTypes, $this->dbTable, Action::$systemUser);
					$fn = $action->getActionResult()->getFieldsNames();
					$fl = $action->getActionResult()->getFieldsLengths();
					$ft = $action->getActionResult()->getFieldsTypes();
				}
			}
			catch(Exception $ex){
				Logger::logWithSpecific(__CLASS__, __FUNCTION__, __LINE__, "Error done during parse ", $ex);
			}
		}
		
		public function parseFromXML($xmlString){
			parent::parseFromXML($xmlString);
		}
		
		public function generate($mode = null){
			$totalVs = "";
			$js = "";
			$ret = "";
			try{
				//------------------------------------------------------------------------------
				//Start Generation
				$ret = "<form method ='post'";
				$ret .= parent::generate($mode);
				//@Todo maybe we need,
				$ret .= ">";
				//$ret .= "<table class='form_header'><tr><td>".Lang::getByKey($this->formTitle)."</td></tr></table>";
				$ret .= "<table><tr><td height='10px'></td></tr></table>";
				$ret .= "<table width='100%' class='form_content'>";
				
				$currRowCells = 1;
				for($i =0; $i<count($this->cells); $i++){
					$curr = $this->cells[$i];
					try{
						if($currRowCells == 0){
							//create new Row
							$ret .= "<tr>";
						}
						
						$ll = "";
						
						if(isset($curr["label"]))
							$ll = $curr["label"];
						else
							$ll = $curr["id"];
						
						$l = array("type"=>"label", "value"=>$ll);
						if(isset($curr["required"]))
							$l["required"] = $curr["required"];
						
						$currType = $l["type"];
						$genLabel = true;
						if(isset($curr["visable"])){
							if($curr["visable"] == "false") $genLabel = false;
						}
						if($genLabel){
							$currType =  Utils::toUpper($currType, 1);
							$__ = (new ReflectionClass($currType))->newInstance();
							(new ReflectionMethod($currType, "parseFromArray"))->invoke($__, $l);
							$a = ((new ReflectionMethod($currType, "generate"))->invoke($__, null));
							if(isset($a["html"]))
								$ret .= "<td align='".Lang::getAntiAlign()."'>&nbsp;&nbsp;". $a["html"] ."&nbsp;&nbsp;: &nbsp;&nbsp;</td>";
						}
						//create cells here
						$curr = $this->cells[$i];
						$currType = $curr["type"];
						if(isset($curr["id"]))
							$totalVs .= "'".$curr["id"]."', ";
						$currType =  Utils::toUpper($currType, 1);
						$__ = (new ReflectionClass($currType))->newInstance();
						$curr['id'] = $this->templateName.$curr['id'];
						(new ReflectionMethod($currType, "parseFromArray"))->invoke($__, $curr);
						$a = ((new ReflectionMethod($currType, "generate"))->invoke($__, null));
						if(isset($a["html"]))
							$ret .= "<td>". $a["html"] ."</td>";
						if(isset($a["js"]))
							$js .= $a["js"];
						if($currRowCells == $this->cellsInRow){
							//close current Row
							$ret .= "</tr>";
							$currRowCells = 1;
						}
						else{
							$currRowCells++;
						}
					}
					catch (Exception $ex1){
						Logger::logWithSpecific(__CLASS__, __FUNCTION__,__LINE__, "Error Happend during generate cell in form", $ex1);
					}
				}
				if(strlen($totalVs) > 1)
					$totalVs = substr($totalVs, 0, -1);
				
				$ret .= "</table>";
				if($this->sub != null && $this->sub != ""){
					foreach ($this->sub as $k=>$v){
						$t = get_class_vars("templates");
						$t = $t[$v];
						$t["issub"] = true;
						$inst = (new ReflectionClass(Utils::toUpper(Utils::toLower($t['type']), 1)))->newInstance();
						$inst->parseFromArray($t);
						$ret .= $inst->generate();
					}
				}
				$ret .= "<table><tr><td height='10px'></td></tr></table>";
				$ret .= "<table class='form_footer'><tr><td width='10p'></td><td><input id='".$this->templateName."okbtn' type='button' class='form_ok' value='ok' />&nbsp;<input id='".$this->templateName."cancelbtn' type='button' class='form_cancel' value='cancel' /></td></tr></table>";
				$js .= "$('#".$this->templateName."okbtn').click(function(){form.ok(\"".$this->templateName."\");});";
				$js .= "$('#".$this->templateName."cancelbtn').click(function(){form.cancel(\"".$this->templateName."\");});";
				$ret .= "</form>";
			}
			catch(Exception $ex){
				Logger::logWithSpecific(__CLASS__, __FUNCTION__, __LINE__, "Error During Generate", $ex);
			}
			$tt = "";
			for($i =0; $i<count($this->cells); $i++){
				$curr = $this->cells[$i];
				$tt .= "'".$curr["id"]."':'".$curr['type']."',";
			}
			$tt = substr($tt, 0, -1);
			
			$vv = " var ".$this->templateName."Vars =[".$totalVs."];";
			$vv .= " var ".$this->templateName."Types = {".$tt."}; ";
			$m = " /*function init".$this->templateName."()*/{".$js."}";
			$ret .= "<script type='text/javascript'>  ".$vv. " ". $m." </script>";
			return $ret;
		}
	}
	
	class Dbform extends Form{
		protected $isSub;
		protected $buildType;
		protected $srcTable;
		protected $where;
		protected $nameField;
		protected $typeField;
		protected $defaultValueField;
		protected $keyField;
		protected $valueField;
		
		public function __construct(){
			$this->isSub = false;
			$this->buildType = "immediate";
			$this->srcTable = null;
			$this->where = null;
			$this->nameField = null;
			$this->typeField = null;
			$this->defaultValueField = null;
			$this->keyField = null;
			$this->valueField = null;
		}
		
		public function parseFromArray($e){
			parent::parseFromArray($e);
			if(isset($e["issub"])){
				$this->isSub = $e["issub"];
			}
			if(isset($e["srctable"])){
				$this->srcTable = $e["srctable"];
			}
			if(isset($e["where"])){
				$this->where = $e["where"];
			}
			if($e["name_field"]){
				$this->nameField = $e["name_field"]; 
			}
			if($e["type_field"]){
				$this->typeField = $e["type_field"];
			}
			if($e["default_value_field"]){
				$this->defaultValueField = $e["default_value_field"];
			}
			if($e["key_field"]){
				$this->keyField = $e["key_field"];
			}
			if($e["value_field"]){
				$this->valueField = $e["value_field"];
			}
		}
		
		public function parseFromXML($xmlString){
			parent::parseFromXML($xmlString);
		}
		
		public function generate($mode=""){
			$totalVs = "";
			$js = "";
			$ret = "";
			$ret = "<form method ='post'";
			$ret .= parent::generate($mode);
			//@Todo maybe we need,
			$ret .= ">";
			$ret .= "<table id='".$this->id."' class='".$this->cssClass."'>";
			try{
				$ret .= "<table><tr><td height='10px'></td></tr></table>";
				$ret .= "<table width='100%' class='form_content'>";
				$currRowCells = 1;
				for($i =0; $i<count($this->cells); $i++){
					$curr = $this->cells[$i];
					try{
						if($currRowCells == 0){
							//create new Row
							$ret .= "<tr>";
						}
						if(isset($curr["label"]))
							$ll = $curr["label"];
						else
							$ll = $curr["id"];
						
						$l = array("type"=>"label", "value"=>$ll);
						if(isset($curr["required"]))
							$l["required"] = $curr["required"];
						
						$currType = $l["type"];
						$genLabel = true;
						if(isset($curr["visable"])){
							if($curr["visable"] == "false") $genLabel = false;
						}
						if($genLabel){
							$currType =  Utils::toUpper($currType, 1);
							$__ = (new ReflectionClass($currType))->newInstance();
							(new ReflectionMethod($currType, "parseFromArray"))->invoke($__, $l);
							$a = ((new ReflectionMethod($currType, "generate"))->invoke($__, null));
							if(isset($a["html"]))
								$ret .= "<td align='".Lang::getAntiAlign()."'>&nbsp;&nbsp;". $a["html"] ."&nbsp;&nbsp;: &nbsp;&nbsp;</td>";
						}
						//create cells here
						$curr = $this->cells[$i];
						$currType = $curr["type"];
						if(isset($curr["id"]))
							$totalVs .= "'".$curr["id"]."', ";
						$currType =  Utils::toUpper($currType, 1);
						$__ = (new ReflectionClass($currType))->newInstance();
						$curr['id'] = $this->templateName.$curr['id'];
						(new ReflectionMethod($currType, "parseFromArray"))->invoke($__, $curr);
						$a = ((new ReflectionMethod($currType, "generate"))->invoke($__, null));
						if(isset($a["html"]))
							$ret .= "<td>". $a["html"] ."</td>";
						if(isset($a["js"]))
							$js .= $a["js"];
						if($currRowCells == $this->cellsInRow){
							//close current Row
							$ret .= "</tr>";
							$currRowCells = 1;
						}
						else{
							$currRowCells++;
						}
					}
					catch (Exception $ex){
						Logger::logWithSpecific(__CLASS__, __FUNCTION__,__LINE__, "Error Happend during generate cell in form", $ex1);
					}
				}
				
				$action = new Action(Action::$select, $this->srcTable, Action::$systemUser);
				$f = Utils::fetch($this->where);
				$action->setExtraWhere(is_array($f)?$f["html"]:$f);
				$action->execute();
				for($i=0; $i<count($action->getActionResult()->getData()); $i++){
					try{
						$row = $action->getActionResult()->getRowAsMap($i);
						if($currRowCells == 0){
							//create new Row
							$ret .= "<tr>";
						}
						
						$label = $row[''.$this->nameField];
						$currType = $row[''.$this->typeField];
						$defaultVal = $row[''.$this->defaultValueField];
						
						$l = array("type"=>"label", "value"=>$ll);
						if(isset($curr["required"]))
							$l["required"] = $curr["required"];
						
						
						$genLabel = true;
						if(isset($curr["visable"])){
							if($curr["visable"] == "false") $genLabel = false;
						}
						if($genLabel){
							$currType =  Utils::toUpper($currType, 1);
							$__ = (new ReflectionClass($currType))->newInstance();
							(new ReflectionMethod($currType, "parseFromArray"))->invoke($__, $l);
							$a = ((new ReflectionMethod($currType, "generate"))->invoke($__, null));
							if(isset($a["html"]))
								$ret .= "<td align='".Lang::getAntiAlign()."'>&nbsp;&nbsp;". $a["html"] ."&nbsp;&nbsp;: &nbsp;&nbsp;</td>";
							
							//----------------------------------------
							
							
							if(isset($curr["id"]))
								$totalVs .= "'".$curr["id"]."', ";
							$currType =  Utils::toUpper($currType, 1);
							$__ = (new ReflectionClass($currType))->newInstance();
							$curr['id'] = $this->templateName.$curr['id'];
							(new ReflectionMethod($currType, "parseFromArray"))->invoke($__, $curr);
							$a = ((new ReflectionMethod($currType, "generate"))->invoke($__, null));
							if(isset($a["html"]))
								$ret .= "<td>". $a["html"] ."</td>";
							if(isset($a["js"]))
								$js .= $a["js"];
						}
						
						if($currRowCells == $this->cellsInRow){
							//close current Row
							$ret .= "</tr>";
							$currRowCells = 1;
						}
						else{
							$currRowCells++;
						}
					}
					catch (Exception $ex){
						Logger::logWithSpecific(__CLASS__, __FUNCTION__,__LINE__, "Error Happend during generate cell in form", $ex1);
					}
				}
				
				$ret .= "</table>";
			}
			catch (Exception $ex){
				Logger::logWithSpecific(__CLASS__, __FUNCTION__, __LINE__, "Error", $ex);
			}
			$ret .= "</table></form>";
			return $ret;
		}
	}
	
	class Html extends Item{
		protected $src;
		public function parseFromArray($e){
			parent::parseFromArray($e);
			if(isset($e['src']))
				$this->src = $e['src'];
			else
				Logger::logWithSpecific(__CLASS__, __FUNCTION__, "", "src attribute is not exist in html type, generation may made error", "");
		}
		
		public function parseFromXML($xmlString){
			parent::parseFromXML($xmlString);
		}
		
		public function generate($mode=null){
			try{
				$str = file_get_contents($this->src);
				$str = Utils::fetch($str);
				//$str = str_replace($str, "{template_name}", " tn='".$this->templateName."' ");
				return $str;
			}
			catch(Exception $ex){
				Logger::logWithSpecific(__CLASS__, __FUNCTION__, "", "src attribute is not exist in html type, generation may made error", "");
			}
		}
	}
	
	
	class Grid extends Item{
		/*
		 * Grid
		 * Has item attributes
		 * Has Special Attributs
		 * 	dbTable: data base table to connect with
		 * 	pk: primary key name of data base table
		 * 	title: title of grid
		 * 	cells: cells of grid
		 *  operations: insert,update,delete
		 *  executer
		 *  auto_fill: "true": fill data immediatlly, "false": do not fill data immediatlly
		 *  
		 * Has Cells 
		 * 	Each Cell Has
		 * 		-	label
		 * 		- 	id
		 * 		-	align
		 * 		-	type 		[text, date, select, checkbox, radiobox, button, link]
		 * 		-	value
		 * 		- 	required	[true: a star'*' must be set near, false]
		 * 		-	max_length	[]
		 * 		- 	searchable	[true:set it in search criteria- false: do not set it in search criteria. default: false].
		 * 		-	align
		 * 		-	type 		[text, date, select, checkbox, radiobox, button, link]
		 * 		-	value
		 *  	- 	domain		: just for select	*
		 *  	-   action		: just for button
		 *  	-	href		: just for link
		 *  	- 	ref_cell	: name of cell the change the result
		 *  	-	ref_method	: method that do it.
		 *  	-	onclick
		 *  	-	ondbclick
		 *  	-	onhover
		 *  	-	onblure
		 *  	-	onchange  
		 * */
		
		private $dbTable;
		private $pk;
		private $gridTitle;
		private $cells;
		private $formTemplate;
		private $operations;
		private $executer;
		private $autoFillData;
		private $cellsInRow;
		/*
		 * must check if it is readonly grid
		 * must check if it is  
		 * */
		
		public function __construct(){
			$this->dbTable = "";
			$this->pk = "";
			$this->title = "";
			$this->cells = null;
			$this->formTemplate = "";
			$this->operations = "search";
			$this->executer = "default";
			$this->autoFillData = "false";
			$this->cellsInRow = 4;
		}
		
		public function parseFromArray($e){
			parent::parseFromArray($e);
			if(isset($e["title"]))
				$this->gridTitle = $e["title"];
			if(isset($e["pk"]))
				$this->pk = $e["pk"];
			else
				Logger::logWithSpecific(__CLASS__, __FUNCTION__, null, "primary key 'pk' is not set, generation may be wrong", null);
			
			if(isset($e['dbtable']))
				$this->dbTable = $e["dbtable"];
			else
				Logger::logWithSpecific(__CLASS__, __FUNCTION__, null, "data base table attribute 'dbtable' is not set, generation may be wrong", null);
			
			if(isset($e["cells"]))
				$this->cells = $e["cells"];
			else
				Logger::logWithSpecific(__CLASS__, __FUNCTION__, null, "cells attribute 'cells' is not set , generation may be wrong", null);
			
			if(isset($e["form_template"]))
				$this->formTemplate = $e["form_template"];
			else
				Logger::logWithSpecific(__CLASS__, __FUNCTION__, null, "form template attribute 'form_template' is not set, generation may be wrong", null);
			
			if(isset($e["operations"]))
				$this->operations = $e["operations"];
			if(isset($e["executer"]))
				$this->executer = $e["executer"];
			if(isset($e["fill_data"]))
				$this->autoFillData = $e["fill_data"];
			if(isset($e["cellsInRow"]))
				$this->cellsInRow = $e["cellsInRow"];
			else
				$this->cellsInRow = 2;
		}
		
		public function parseFromXML($xmlString){
			parent::parseFromXML($xmlString);
		}
		
		public function generate($mode=null){
			$totalSearchVars = "";
			$totalVars = "";
			$varsAsHTML = "";
			$js = "";
			$ret = "";
			try{
				$fromParent = "";
				if($this->cssClass != null)
					$fromParent .= " class='".$this->cssClass."' ";
				else
					$fromParent .= " class='grid' ";
				
				if($this->style)
					$fromParent .= " style='".$this->style."' ";
				
				if($this->align)
					$fromParent .= " align='".$this->align."' ";
				else
					$fromParent .= " align='".Lang::getAlign()."' ";
				if($this->gridTitle == null)
					$this->gridTitle = $this->dbTable;
				
				if($this->onselect)
					$fromParent.= " onselect=".$this->onselect;
				
				$ret .= "<div id='".$this->templateName."MainDiv' ".$fromParent." form_template='".$this->formTemplate."' >";
				$ret .= 	"<div class='grid_title'><span>". Lang::getByKey($this->gridTitle). "</span></div>";
				$ret .= 	"<div class='grid_search' id='".$this->templateName."SearchDiv'>";
				if(isset($this->cells)){
					$ret .= "<table width='100%'>";
					$currRowCells = 0;
					for($i=0; $i<count($this->cells); $i++){
						if($currRowCells == 0){
							//create new Row
							$ret .= "<tr>";
						}
						
						$curr = $this->cells[$i];
						//------------------------------
						$currType = $curr["type"];
						$currType =  Utils::toUpper($currType, 1);
						$__ = (new ReflectionClass($currType))->newInstance();
						(new ReflectionMethod($currType, "parseFromArray"))->invoke($__, $curr);
						$__->id = $this->templateName.$__->id;
						$a = ((new ReflectionMethod($currType, "generate"))->invoke($__, array("mode"=>"search")));
						if(isset($a["html"]))
							$varsAsHTML .= "\"".$curr['id']."\":\"". $a["html"]."\", ";
						if(isset($a["js"]))
							$js .= $a["js"];
						
						//-------------------------------
						$ok = false;
						if(isset($curr["search"]))
							if($curr["search"] == "true")
								$ok = true;
						if(! $ok)
							if(isset($curr["searchable"]))
								if($curr["searchable"] == "true")
									$ok = true;
						
						//-----------------------------------------------------------------------------
						
						if($ok){
							//Generate Label
							$l = array("type"=>"label", "value"=>$curr["id"]);
							$currType = $l["type"];
							$currType =  Utils::toUpper($currType, 1);
							$__ = (new ReflectionClass($currType))->newInstance();
							(new ReflectionMethod($currType, "parseFromArray"))->invoke($__, $l);
							$a = ((new ReflectionMethod($currType, "generate"))->invoke($__, null));
							if(isset($a["html"]))
								$ret .= "<td align='".Lang::getAntiAlign()."'>&nbsp;&nbsp;". $a["html"] ."&nbsp;&nbsp;: &nbsp;&nbsp;</td>";
							
							$currType = $curr["type"];
							$currType =  Utils::toUpper($currType, 1);
							$__ = (new ReflectionClass($currType))->newInstance();
							(new ReflectionMethod($currType, "parseFromArray"))->invoke($__, $curr);
							$__->id = $this->templateName.$__->id;
							$a = ((new ReflectionMethod($currType, "generate"))->invoke($__, array("mode"=>"search")));
							if(isset($a["html"]))
								$ret .= "<td>". $a["html"] ."</td>";
							if(isset($a["js"]))
								$js .= $a["js"];
							if(isset($curr["id"]))
								$totalSearchVars .= "'__search__".$this->templateName.$curr["id"]."', ";
						}
						if(isset($curr["id"]))
							$totalVars .= "'".$curr["id"]."', ";
						
						if($currRowCells == $this->cellsInRow){
							//close current Row
							$ret .= "</tr>";
							$currRowCells = 0;
						}
						else{
							$currRowCells++;
						}
					}
					$ret .= "</table>";
				}
				$ret .=     "</div>";
				$ret .= 	"<div class='seperator'></div>";
				$ret .= 	"<div class='grid_buttons' id='".$this->templateName."BtnsDiv'>";
				
				
				if($this->operations != null){
					if(!is_array($this->operations)){
						$this->operations = explode(",", $this->operations);
					}
				}
				for($i=0; $i<count($this->operations); $i++){
					$cid = $this->templateName.$this->operations[$i];
					$ret .= "<span><input class='gridbtn' id='".$cid."' type='button' value='".Lang::getByKey($this->operations[$i])."' /></span>";
					$js .= " $('#".$cid."').click(function(){grid.btnClicked(\"".$this->templateName."\", \"".$this->operations[$i]."\");});";
				}
				$ret .=     "</div>";
				$ret .= 	"<div class='grid_result' id='".$this->templateName."ResultDiv'>";
				$cols = "";
				if(isset($this->cells)){
					for($i=0; $i<count($this->cells); $i++){
						$c = $this->cells[$i];
						$cols .= Lang::getByKey($c["id"]).",";
					}
				}
				if(count($cols) > 1)
					$cols = substr($cols, 0, -1);
				$ret .=			"<table id='".$this->templateName."ResultTable' class='ctable' cols='".$cols."' selector='true' pk='".$this->pk."' tn='".$this->templateName."'>";
				$ret .=				"<thead>";
				$ret .=					"<tr>";
				if(isset($this->cells)){
					$ret .= "<th width='10px'></th>";
					for($i=0; $i<count($this->cells); $i++){
						$ret .=	"<th>";
						$c = $this->cells[$i];
						$ret .=		Lang::getByKey($c["id"]);
						$ret .=	"</th>";
					}
				}
				$ret .=					"</tr>";
				$ret .=				"</thead>";
				$ret .=				"<tbody id='".$this->templateName."tbody'>";
				if($this->autoFillData && $this->autoFillData != "false"){
					$action = new Action(Action::$select, $this->dbTable, Action::$systemUser);
					$action->execute();
					for($i=0; $i<count($action->getActionResult()->getData()); $i++){
						$m = $action->getActionResult()->getRowAsMap($i);
						$ret .= "<tr>";
						$ret .= "<td width='10px'><input type='radio' id='".$this->templateName.$m[$this->pk]."'  name='".$this->templateName."Selector' onchange='grid.setSelector(\"".$this->templateName."\", this)' /></td>";
						for($j=0; $j<count($this->cells); $j++){
							$currCell = $this->cells[$j];
							$ret .= "<td>";
							$ret .=	 $m["".$currCell["id"]];
							$ret .=	"</td>";
						}
						$ret .= "</tr>";
					}
				}
				$ret .=				"</tbody>";
				$ret .=			"</table>";
				$ret .=     "</div>";
				$ret .=		"<div class='grid_footer'>.</div>";
				$ret .= "</div>";
				if($this->operations != null){
					$insert = false;
					foreach ($this->operations as $k=>$v)
					if($v == "insert" || $v == "new" || $v == "add")
						$insert = true;
					if($insert){
						$ret .= "<div id='".$this->formTemplate."Popup' style='display:none;'>";
						$ret .= "<div id='".$this->formTemplate."PopupContent'>";
						if($this->formTemplate != null){
							$mainInjector = new MainInjector();
							$ret .= $mainInjector->processEL("template.".$this->formTemplate);
						}
						$ret .= "</div>";
						$ret .= "</div>";
					}
				}
				//Generate fields html types and set them in variables
				if($varsAsHTML != null && $varsAsHTML != ""){
					if($varsAsHTML[strlen($varsAsHTML)-1] ==',')
						$varsAsHTML = substr($varsAsHTML, 0, -1);
				}
			}
			catch(Exception $ex){
				Logger::logWithSpecific(__CLASS__, __FUNCTION__, __LINE__, "Grid Generate faces erre:", $ex);
			}
			$sv = " var ".$this->templateName."SearchVars =[".$totalSearchVars."];";
			$vv = " var ".$this->templateName."Vars =[".$totalVars."];";
			$m = " function init". $this->templateName ."(){".$js."}";
			$varsAsHTML = " var ".$this->templateName.'VarsAsHTML = {'.$varsAsHTML.'};';
			$ret .= "<script type='text/javascript'>  ".$vv.$sv. " ". $m.$varsAsHTML." </script>";
			return $ret;
		}
	}
	
	//=============================================================================
	//=============================================================================
	class FWGenerator{
		public function generatePage($page){
			try{
				$title = isset($page['title'])?lang::getByKey($page['title']):"";
				$image = isset($page['image'])?$page['image']:"";
				$template = isset($page['template'])?$this->fetchTemplate(file_get_contents($page['template'])):"";
				
				$pagePath = $page['url'];
				$str = file_get_contents($pagePath);
				$js = "";
				//$pageFragments = explode("#{", $pageAsString);
				$result = "";
				$mainInjector = new MainInjector();
				$bodyTag = 0;
				$headTag = 0;
				for($i=0; $i< strlen($str); $i++){
					if($str[$i] == "#" && $str[$i+1] == "{"){
						$j = $i+1;
						while($str[$j] != "}")
							$j++;
						$injector = substr($str, $i+2, $j-2-$i);
						try{
							$result1 = $mainInjector->processEL($injector);
							if(is_array($result1))
								$result .= $result1['html'].$result1['js'];
							else
								$result .= $result1;
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
						$result .= "".$str[$i];
					}
					$ll = strlen($result);
					if($ll > 4){
						if( ($result[$ll-5] == '<') && 
							(Utils::toLower($result[$ll-4]) == 'b')	&&
							(Utils::toLower($result[$ll-3]) == 'o')	&&
							(Utils::toLower($result[$ll-2]) == 'd')	&&
							(Utils::toLower($result[$ll-1]) == 'y')
						 ){
							$bodyTag = 1;
						}
						if($str[$i] == '>' && $bodyTag == 1){
							$result = substr($result, 0, -1);
							$result .= " onload='initPage()' >";
							$result .= $template["befor-content"];
							//Get Body Of Template untill content tag
							$bodyTag = 2;
						}
					}
					
					if($ll > 4){
						if( ($result[$ll-5] == '<') &&
						(Utils::toLower($result[$ll-4]) == 'h')	&&
						(Utils::toLower($result[$ll-3]) == 'e')	&&
						(Utils::toLower($result[$ll-2]) == 'a')	&&
						(Utils::toLower($result[$ll-1]) == 'd')
						){
							$headTag = 1;
						}
						if($str[$i] == '>' && $headTag == 1){
							//Get Head Of Template:
							$result .= $template["head"];
							$need2ACS = true;
							//---------------------------------------------------------------------------------------
							$result .= " <meta 	http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"  />";
							$result .= "<link 	href=\"css/jquery-ui.css\" rel=\"stylesheet\" type=\"text/css\" />";
							$result .= "<script type=\"text/javascript\" src=\"js/jquery-1.8.3.js\"></script>";
							$result .= "<script type=\"text/javascript\" src=\"js/jquery-ui-1.9.2.custom.js\"></script>";
							$result .= "<script type=\"text/javascript\" src=\"js/jquery-ui-1.9.2.custom.min.js\"></script>";
							$result .= $this->generateLangScript();
							if($need2ACS){
								$result .= "<script src=\"ckeditor/ckeditor.js\" type=\"text/javascript\"></script>";
								$result .= "<link href=\"ckeditor/contents.css\" rel=\"stylesheet\" type=\"text/css\" />";
								$result .= "<script src=\"ckeditor/config.js\" type=\"text/javascript\"></script>";
								$result .= "<link href=\"ckeditor/skins/moono/editor.css\" rel=\"stylesheet\" type=\"text/css\" />";
								$result .= "<script src=\"ckeditor/lang/ar.js\" type=\"text/javascript\"></script>";
								$result .= "<script src=\"ckeditor/styles.js\" type=\"text/javascript\"></script>";
								$result .= "<script src=\"ckeditor/build-config.js\" type=\"text/javascript\"></script>";
							}
							$result .= "<script type=\"text/javascript\" src=\"js/___c.js\"></script>";
							$result .= "<link 	href=\"css/general.css\" rel=\"stylesheet\" type=\"text/css\" />";
							$headTag = 2;
							
						}
					}
				}
				$result.= "<script type=\"text/javascript\">function initPage(){".$js."}</script>";
				
				// While $result ends of </html> and </body> remove them
				// add rest of template
				$result .= $template["after-content"];
				return $result;
			}
			catch(Exception $ex){
				Logger::LogErr(__CLASS__.",".__FUNCTION__.", ".__LINE__, $ex);
				return ;
			}
		}
		public function generateLangScript(){
			if(BasicController::$Configurations["lang_script"]){
				try{
					$reuestURI = $_SERVER['REQUEST_URI'];
					if($reuestURI[0] == '/' || $reuestURI[0] == '\\')
						$reuestURI = substr($reuestURI, 1);
					$reuestURI = explode("/", $reuestURI)[0];
					$path = $_SERVER["DOCUMENT_ROOT"]."\\".$reuestURI."\\".BasicController::$Configurations["lang_script"]."\\lang_".lang::getLang().".js";
					$f1 = fopen($path, "w");
					$l = lang::currLangAsKeyValue();
					if(is_array($l)){
						foreach ($l as $k=>$v){
							fwrite($f1,"var ".$k."='".$v."';\n");
						}
					}
					fclose($f1);
				}
				catch (Exception $ex){
					
				}
				return "<script type=\"text/javascript\" src=\"js/lang_".lang::getLang().".js\"></script>";
			}
		}
		public function fetchTemplate($templateAsStr){
			$head = "";
			$beforContent = "";
			$afterContent = "";
			
			for($i=0;$i<strlen($templateAsStr); $i++){
				if($i>4){
					if($templateAsStr[$i-4] == 'h' && 
						$templateAsStr[$i-3] == 'e' &&
							$templateAsStr[$i-2] == 'a'	&&
								$templateAsStr[$i-1] == 'd' &&
									$templateAsStr[$i] == '>'){
						$i++;
						$ok = false;
						while($i+5 < strlen($templateAsStr) && !$ok){
							if($templateAsStr[$i] == '<' &&
								$templateAsStr[$i+1] == '/' &&
								$templateAsStr[$i+2] == 'h' && 
									$templateAsStr[$i+3] == 'e' &&
										$templateAsStr[$i+4] == 'a'	&&
											$templateAsStr[$i+5] == 'd'){
								$i = $i+6;
								$ok = true;
							}
							else{
								$head .= "".$templateAsStr[$i++];
							}
						}
					}
					if($templateAsStr[$i-5] == '<' &&
						$templateAsStr[$i-4] == 'b' &&
						$templateAsStr[$i-3] == 'o' &&
							$templateAsStr[$i-2] == 'd'	&&
								$templateAsStr[$i-1] == 'y'){
						while($i < strlen($templateAsStr) && $templateAsStr[$i] != '>')
							$i++;
						$i++;
						$ok = false;
						while($i+6 < strlen($templateAsStr) && !$ok){
							if($templateAsStr[$i] == '#' && 
								$templateAsStr[$i+1] == 'c' &&
									$templateAsStr[$i+2] == 'o' &&
										$templateAsStr[$i+3] == 'n' &&
											$templateAsStr[$i+4] == 't' && 
												$templateAsStr[$i+5] == 'e' && 
													$templateAsStr[$i+6] == 'n' &&
														$templateAsStr[$i+7] == 't'&&
															$templateAsStr[$i+8] == '#'){
								$ok = true;
								$i = $i + 9;
							}
							else{
								$beforContent .= "".$templateAsStr[$i++];
							}
						}		

						$ok = false;
						while($i+7<strlen($templateAsStr) && !$ok){
							if($templateAsStr[$i+1] == '<' && 
								$templateAsStr[$i+2] == '/' &&
									$templateAsStr[$i+3] == 'b' &&
										$templateAsStr[$i+4] == 'o' &&
											$templateAsStr[$i+5] == 'd' &&
												$templateAsStr[$i+6] == 'y' &&
													$templateAsStr[$i+7] == '>'){
								$ok = true;								
							}
							else{
								$afterContent .= "".$templateAsStr[$i++];
							}
						}
					}
				}
			}
			$ret = array("head"=>Utils::fetch($head), "befor-content"=>Utils::fetch($beforContent), "after-content"=>Utils::fetch($afterContent));
			return $ret;
		}
	}
	
?>