<?php
class Content{
	private $detail;
	private $form;
	private $answers;
	 
	public function processEL($expr){
		$this->detail = new ContentDetail();
		$this->form = new ContentForm();
		$this->answers = new ContentFormAnswer();
		
		$ret = "";
		if(Utils::toLower($expr) == "admin"){
			$ret .= $this->generateForAdmin();
			$ret .= $this->detail->generateForAdmin();
			$ret .= $this->form->generateForAdmin();
			$ret .= $this->answers->generateForAdmin();
		}
		else if(Utils::toLower($expr) == "client"){
			$ret .= $this->generateForClient();
			$ret .= $this->detail->generateForClient();
			$ret .= $this->form->generateForClient();
			$ret .= $this->answers->generateForClient(); 
		}
		return $ret;
	}
	
	public function generateForAdmin(){
		
	}
	
	public function generateForClient(){
		
	}
}

class ContentDetail{
	public function generateForAdmin(){
	
	}
	
	public function generateForClient(){
	
	}
}

class ContentForm{
	public function generateForAdmin(){
	
	}
	
	public function generateForClient(){
	
	}
}

class ContentFormAnswer{
	public function generateForAdmin(){
	
	}
	
	public function generateForClient(){
	
	}
}
?>