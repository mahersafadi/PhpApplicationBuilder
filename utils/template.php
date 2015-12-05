<?php
//Author: Maher.safadi@gmail.com


class template{
	public function processEL($expr){
		//get template name based on its type, execute generator
		$expr = $expr[0];
		
		//At this version, templates are got from templates class as arrays
		//At next versions, templates are loaded from xml files or Data bases
		
		$templatesVars = get_class_vars("templates");
		if($expr[0] == '$')
			$expr = substr($expr, 1);
		$selTemp = $templatesVars[''.$expr];
		if($selTemp != null){
			$selTemp["template_name"] = $expr;
			$type = $selTemp["type"];
			$type = Utils::toUpper($type, 1);
			$instance = (new ReflectionClass($type))->newInstance();
			//based on paring attribute 
			$tm = Utils::toUpper(Configs::$templateMode, 1);
			(new ReflectionMethod($type, "parseFrom".$tm))->invoke($instance, $selTemp);
			$ret = (new ReflectionMethod($type, "generate"))->invoke($instance, null);
			return $ret;
		}
		return "";
	}
}
?>