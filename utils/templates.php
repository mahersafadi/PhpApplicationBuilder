<?php
//Author: Maher.safadi@gmail.com

class Templates{
	//Baisc Templates
	public $lang_grid = array(	"type"=>"grid",
								"dbtable"=>"lang",
								"pk"=>"code",
								"fill_data"=>"true",
								"order_by"=>"code",
								"operations"=>"insert,update,delete,search",
								"form_template"=>"lang_form",
								"onselect"=>"grid.search({tn:\"lang_details\",op:\"search\",type:\"data\",sc:{id:\"code\",search_operator:\"=\",value:eval('pkVal')}})",
								"cells"=>array(
											array("id"=>"code",		 "type"=>"text",	"search"=>"true", "search_operator"=>"=",	"max_leng"	=>"30", "unique"),
											array("id"=>"lang_name", "type"=>"text",	"search"=>"true", "search_operator"=>"like",	"max_length"=>"50", "unique")
										  )
							);
	public $lang_form = array(	"type"=>"form",
								"dbtable"=>"lang",
								"pk"=>"code",
								"cellsInRow"=>"1",
								"cells"=>array(
									array("type"=>"text",	"id"=>"code"),
									array("type"=>"text",   "id"=>"lang_name")
								)
							);
	public $lang_details = array("type"=>"grid",
								"dbtable"=>"lang_detail",
								"pk"=>"key",
								"fill_data"=>"false",
								"order_by"=>"key",
								"operations"=>"insert,update,delete",
								"form_template"=>"lang_detail_form",
								"cells"=>array(
											array("id"=>"key",	 "type"=>"text",	"search"=>"false", "searchable"=>"false", "max_leng"	=>"30", "unique"),
											array("id"=>"value", "type"=>"text",	"search"=>"false", "searchable"=>"false", "max_length"	=>"50", "unique"),
											array("id"=>"code",	 "type"=>"text",	"search"=>"false", "searchable"=>"false", "max_leng"	=>"30", "unique")
										  )
							);
	public $lang_detail_form = array(	"type"=>"form",
								"dbtable"=>"lang_detail",
								"pk"=>"key",
								"cellsInRow"=>"1",
								"cells"=>array(
										array("type"=>"text",	"id"=>"code"),
										array("type"=>"text",	"id"=>"key"),
										array("type"=>"text",   "id"=>"value")
									)
								);
	//content:
	public $content_admin = array("type"=>"grid",
									"dbtable"=>"content",
									"pk"=>"content_id",
									"fill_data"=>"true",
									"order_by"=>"key",
									"operations"=>"insert,update,delete,search",
									"form_template"=>"content_admin_form",
									"onselect"=>"ContentAdmin.contentSelected(eval('pkVal'))",
									"cells"=>array(
										array("id"=>"content_id", 	"type"=>"text", "search"=>"false"),
										array("id"=>"title", 		"type"=>"text", "search"=>"true")
									)
							);
	public $content_admin_form = array("type"=>"form",
										"dbtable"=>"content",
										"pk"=>"content_id",
										"cellsInRow"=>"1",
										"cells"=>array(
												array("id"=>"content_id", 	"type"=>"text", "visable"=>"false"),
												array("id"=>"title", 		"type"=>"text"),
												array("id"=>"cat_id",		"type"=>"select", "domain"=>"category"),
												array("id"=>"create_date",	"type"=>"date",	 	"visable"=>"true", 		"default"=>"#{defaults.today}"),
												array("id"=>"create_user",	"type"=>"text",  	"visable"=>"true",		"default"=>"#{defaults.userid}"),
												array("id"=>"image",		"type"=>"attachment", "subtype"=>"image", 	"display_in"=>"same", "delete_allow"=>"true", "width"=>"175", "height"=>"100", "alt"=>"content_image")
										)
									);

	public $content_admin_extra = array("type"=>"grid",
										"dbtable"=>"content_custom_fields",
										"pk"=>"csf_id",
										"fill_date"=>"false",
										"order_by"=>"csf_name",
										"operations"=>"insert,update,delete,search",
										"form_template"=>"content_admin_extra_form",
										"cells"=>array(
												array("id"=>"csf_id",		"type"=>"text","search"=>"false"),
												array("id"=>"content_id",	"type"=>"select", "domain"=>"content", "search"=>"true"),
												array("id"=>"csf_name", 	"type"=>"text","search"=>"false", "searchable"=>"false"),
												array("id"=>"csf_value", 	"type"=>"text","search"=>"false", "searchable"=>"false"),
												array("id"=>"csf_others", 	"type"=>"text","search"=>"false", "searchable"=>"false"),
										)
									);
	public $content_admin_extra_form = array("type"=>"form",
										"dbtable"=>"content_custom_fields",
										"pk"=>"csf_id",
										"cells"=>array(
												array("id"=>"csf_id",		"type"=>"text", "visable"=>"false"),
												array("id"=>"content_id",	"type"=>"select", "domain"=>"content"),
												array("id"=>"csf_name", 	"type"=>"text"),
												array("id"=>"csf_value", 	"type"=>"text"),
												array("id"=>"csf_others", 	"type"=>"text"),
										)
									);

	public $content_admin_detail = array("type"=>"grid",
										"dbtable"=>"content_detail",
										"pk"=>"cd_id",
										"fill_data"=>"false",
										"order_by"=>"cd_id",
										"operations"=>"insert,update,delete,search",
										"form_template"=>"content_admin_detail_form",
										"cells"=>array(
												array("id"=>"cd_id", 	"type"=>"text", "search"=>"false"),
												array("id"=>"content_id", "type"=>"select", "domain"=>"content", "search"=>"true"),
												array("id"=>"keywords",	"type"=>"text","search"=>"true","search_operator"=>"like"),
												array("id"=>"lang",		"type"=>"select", "domain"=>"lang"),
												array("id"=>"ref_content",	"type"=>"select", "domain"=>"content", "search"=>"true"),
										)
							);
	public $content_admin_detail_form = array("type"=>"form",
												"dbtable"=>"content_detail",
												"pk"=>"cd_id",
												"cellsInRow"=>"1",
												"cells"=>array(
														array("id"=>"cd_id", 	"type"=>"text", "visable"=>"false"),
														array("id"=>"content_id", "type"=>"select", "domain"=>"content"),
														array("id"=>"keywords",	"type"=>"text"),
														array("id"=>"lang",		"type"=>"select", "domain"=>"lang", "default"=>"#{session.Nlang}"),
														array("id"=>"ref_content",	"type"=>"select", "domain"=>"content"),
														array("id"=>"text",			"type"=>"editor")
												)

							);

	public $content_admin_form_gird = array("type"=>"grid",
											"dbtable"=>"content_form",
											"pk"=>"cf_id",
											"cellsInRow"=>"2",
											"fill_data"=>"false",
											"order_by"=>"cf_id",
											"operations"=>"insert,update,delete,search",
											"form_template"=>"content_admin_form_form",
											"onselect"=>"ContentAdmin.contentFormSelected(eval('pkVal'))",
											"cells"=>array(
													array("id"=>"cf_id", 		"type"=>"text", 		"search"=>"false"),
													array("id"=>"cf_content_id","type"=>"select", 		"search"=>"true", 	"search_operator"=>"like",  "domain"=>"content"),
													array("id"=>"cf_type",		"type"=>"select", 		"search"=>"true",	"search_operator"=>"=",		"domain"=>"content_form_types"),
													array("id"=>"cf_login_required", "type"=>"select",  "search"=>"true",	"search_operator"=>"=",		"domain"=>"yes_no"),
													array("id"=>"cf_review_required", "type"=>"select", "search"=>"true",	"search_operator"=>"=",		"domain"=>"yes_no"),
													array("id"=>"cf_showit_required", "type"=>"select", "search"=>"true",	"search_operator"=>"=",		"domain"=>"yes_no"),
													array("id"=>"cf_title",			  "type"=>"text",	"search"=>"false")
											)
										);
	public $content_admin_form_form = array("type"=>"form",
											"dbtable"=>"content_form",
											"pk"=>"cf_id",
											"cellsInRow"=>"1",
											"cells"=>array(
													array("id"=>"cf_id", 		"type"=>"text", 		"visable"=>"false"),
													array("id"=>"cf_content_id","type"=>"select", 		"domain"=>"content"),
													array("id"=>"cf_type",		"type"=>"select", 		"domain"=>"content_form_types"),
													array("id"=>"cf_login_required", "type"=>"select",  "domain"=>"yes_no"),
													array("id"=>"cf_review_required", "type"=>"select", "domain"=>"yes_no"),
													array("id"=>"cf_showit_required", "type"=>"select", "domain"=>"yes_no"),
													array("id"=>"cf_title",			  "type"=>"text")
											)
										);
	public $content_admin_form_detail = array(	"type"=>"grid",
											   	"dbtable"=>"content_form_detail",
											 	"pk"=>"cfd_id",
												"fill_data"=>"false",
												"order_by"=>"cfd_id",
												"operations"=>"insert,update,delete",
												"form_template"=>"content_admin_form_detail_form",
												"cells"=>array(
														array("id"=>"cfd_id",	"type"=>"text",		"search"=>"false"),
														array("id"=>"name",		"type"=>"text"),
														array("id"=>"type",		"type"=>"select",	"domain"=>"types"),
														array("id"=>"domain",	"type"=>"select",	"domain"=>"domains"),
														array("id"=>"form_id",	"type"=>"select",	"domain"=>"forms", "search"=>"true"),
														array("id"=>"required",	"type"=>"select",	"domain"=>"yes_no")
												)
											);
	public $content_admin_form_detail_form = array("type"=>"form",
													"dbtable"=>"content_form_detail",
													"pk"=>"cfd_id",
													"cellsInRow"=>"2",
													"cells"=>array(
															array("id"=>"cfd_id",	"type"=>"text",		"visable"=>"false"),
															array("id"=>"name",		"type"=>"text"),
															array("id"=>"type",		"type"=>"select",	"domain"=>"types"),
															array("id"=>"domain",	"type"=>"select",	"domain"=>"domains"),
															array("id"=>"form_id",	"type"=>"select",	"domain"=>"forms"),
															array("id"=>"required",	"type"=>"select",	"domain"=>"yes_no")
													)
												);

// 	public $content_admin_form_answers = array();
// 	public $content_admin_form_answers_form = array();

//---------------- Module Types --------------------
	public $module_types = array("type"=>"grid","dbtable"=>"module_types", "pk"=>"mt_id", "fill_data"=>"true","order_by"=>"mt_name", "operations"=>"insert,update,delete,search",
								"form_template"=>"module_types_form",
								"onselect"=>"Module.moduleTypeSel(eval('pkVal'))",
								"cells"=>array(
											array("id"=>"mt_id", 	"type"=>"text", "search"=>"false"),
											array("id"=>"mt_name",  "type"=>"text", "search"=>"false"),
											array("id"=>"active",	"type"=>"select", "domain"=>"yes_no", "search"=>"true")
								)
							);
	public $module_types_form = array(	"type"=>"form",
										"dbtable"=>"module_types",
										"pk"=>"mt_id",
										"cellsInRow"=>"1",
											"cells"=>array(
													array("id"=>"mt_id", 	"type"=>"text", "visable"=>"false"),
													array("id"=>"mt_name",  "type"=>"text"),
													array("id"=>"active",	"type"=>"select", "domain"=>"yes_no")
											)
										);
	public $modules_grid = array("type"=>"grid", "dbtable"=>"module", "pk"=>"module_id",
			"fill_data"=>"true", "order_by"=>"region",
			"operations"=>"insert,update,delete,search",
			"form_template"=>"modules_form",
			"cells"=>array(
					array("id"=>"module_id", "type"=>"text", 	"search"=>"true"),
					array("id"=>"type", 	 "type"=>"select", 	"domain"=>"module_types", "search"=>"true"),
					array("id"=>"title",	 "type"=>"text"),
					array("id"=>"region",	 "type"=>"select", "domain"=>"regions")
			)
	);
	public $modules_form = array("type"=>"html",
								 "src"=>"../view/root/module_form.html",
								 "executer"=>"ModuleFormDetails"
							);
	//--------------------------------------------------
	//Menus
	public $menu_grid = array("type"=>"grid",
							 "dbtable"=>"menu","pk"=>"mid","fill_data"=>"true",
							 "operations"=>"insert,update,delete,search",
							 "form_template"=>"menu_form",
							 "cells"=>array(
								array("id"=>"mid", 		"type"=>"text"),
							 	array("id"=>"title", 	"type"=>"text"),
							 	array("id"=>"css_class","type"=>"text")
							)
						);
	public $menu_form = array("type"=>"form",
								"dbtable"=>"menu",
								"pk"=>"mid",
								"cells"=>array(
										array("id"=>"mid", 		"type"=>"text", "visable"=>"false"),
										array("id"=>"title", 	"type"=>"text"),
										array("id"=>"css_class","type"=>"text")
								)
							);
	//-----------------------------------------------
	public $module_grid_detail = array("type"=>"grid",
										"dbtable"=>"menu_detail",
										"pk"=>"mdid",
										"cells"=>array(
											array("mdid",		"type"=>"text"),
											array("md_name", 	"type"=>"text"),
											array("md_parent",	"type"=>"select", "domain"=>"menus"),
											array("page",		"type"=>"text")
										)
								);
	public $menu_detail = array("type"=>"form","dbtable"=>"menu_detail",
								"pk"=>"mdid",
								"cellsInRow"=>"1",
								"cells"=>array()

						);
	//------------------------------------------------------
}

?>