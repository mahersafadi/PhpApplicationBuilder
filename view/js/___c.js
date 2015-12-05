
//-----------------------------------------------------------
//Auther Maher.safadi@gmail.com

var mode = "debug";
alertt = function(msg) {
	if (mode == "debug")
		alert(msg);
};

function ref(p1, p2, args) {
	try {
		var args = Array.prototype.slice.call(arguments).splice(2);
		var p5 = p1.split(".");
		var p4 = p5.pop();
		for ( var i = 0; i < p5.length; i++) {
			try {
				p2 = p2[p5[i]];
			} catch (e) {
				alert(e.message);
			}
		}
		// alert(p2);
		return p2[p4].apply(this, args);
	} catch (e) {
		alertt(e.message);
	}
};

var core = {
	back : null,
	senddata:function(_1){
		var __1 = '';
		var __2 = {};
		if(_1 != null){
			for(___1 in _1){
				___2 = _1[___1];
				__2[''+___1] = ___2; 
			}
		}
		$.ajax({url: '../control/DataHandler.php',type: 'post',data: __2,success:function(response){_1['response'] = response;ref(_1['callback'], window, _1);}});
	}
};
core.back = new Array();

var grid = {
	setSelector : function(tn, inp) {
		try {
			$('#' + tn + 'tbody').children().each(function() {
				var $this = $(this);
				$this.removeClass('grid_row_selector');
				if(this.children[0].children[0].id == inp.id){
					$this.addClass('grid_row_selector');
					// if there is onselect, then execute it
					var pkVal = inp.id.substring(tn.length);
					window[tn+'pkName'] = $('#'+tn+'ResultTable').attr('pk');
					window[tn+'pkValue'] = pkVal;
					var f = $('#'+tn+'MainDiv').attr('onselect');
					try{(f != null && f != '')?eval(f):null;}catch(e){}
				}
			});
		} catch (e) {
			alertt(e.message);
		}
	},
	btnClicked : function(tn, op) {
		try {
			var args = new Array();
			args['tn'] = tn;
			args['type'] = 'data';
			var v = 'grid.';
			((op == 'insert' || op == 'delete' || op == 'update' || op == 'search')) ?  v += op : v = op;
			ref(v, window, args);
		} catch (e) {
			alertt(e.message);
		}
	},
	search: function (args){
		// collect search fields, then go to server
		try{
			var tn = args['tn'];
			var scV = window[tn+'SearchVars'];
			var scR = "";
			if(scV != null && scV != "" && scV != undefined){
				for(var i =0; i<scV.length; i++){
					var  c = document.getElementById(scV[i]);
					if(c != null && c != "" && c != undefined){
						var id = c.id;
						id = id.substring("__search__".length+tn.length);
						// scR += id+":\""+c.value+"\",";
						scR += "{id:\""+id+"\",search_operator:\"like\",value:\""+c.value+"\"},";
					}
				}
			}
			// collect sc comming from args to
			var xx = args["sc"];
			if(xx != null){
				var __id = xx.id;
				var __op = xx.search_operator;
				var __val = xx.value;
				scR += "{id:\""+__id+"\",search_operator:\""+__op+"\",value:\""+__val+"\"},";
			}
			if(scR[scR.length - 1] == ",")
				scR = scR.substring(0, scR.length - 1);
			scR = "(["+scR+"])";
			args["callback"] = "grid.searchAfter";
// if(args["sc"] == null || args["sc"] == '' || args["sc"] == undefined)
				args["sc"] = scR;
			args["op"] = "search";
			core.senddata(args);
			// ----------------------------------------------
			
		}
		catch(e){
			alertt(e.message);
		}
		return true;
	},
	searchAfter:function(args){
		try{
			if(args['response'] != null && args['response'] != ''){
				var _1 = $.parseJSON(args['response']);
				var cols = $('#'+args['tn']+'ResultTable').attr('cols');
				var varsAsHTML = window[args['tn']+'VarsAsHTML'];
				if(cols[cols.length-1]==',')
					cols = cols.substring(0,cols.length-1);
				var sel = $('#'+args['tn']+'ResultTable').attr('selector');
				cols = cols.split(",");
				var res = "";
				$(_1).each(function(i,val){
					res += "<tr>";
					res += (sel == 'true')? "<td width='10px'><input type='radio' id='"+args['tn']+val[""+$(('#'+args['tn']+'ResultTable')).attr('pk')]+"'  name='"+$(('#'+args['tn']+'ResultTable')).attr('pk')+"Selector' onchange='grid.setSelector(\""+args['tn']+"\", this)' ></td>":"";
					$(cols).each(function(j, vv){
						var currVarAsHTML = varsAsHTML[vv];
						if(currVarAsHTML != null && currVarAsHTML != "" && currVarAsHTML != undefined){
							currVarAsHTML = currVarAsHTML.replace('{value}', 'value=\''+val[vv]+'\'');
							currVarAsHTML = currVarAsHTML.replace('{readonly}', 'readonly=\'true\'');
							currVarAsHTML = currVarAsHTML.replace('{disabled}', 'disabled=\'true\'');
							currVarAsHTML = currVarAsHTML.replace('{selected'+val[vv]+'}', 'selected');
						}
						//res += "<td>"+ val[""+vv] +"</td>";
						res += "<td>"+ currVarAsHTML +"</td>";
					});
					res += "</tr>";
				});
				$((('#'+args['tn']+'tbody'))).html(res);
			}
		}
		catch(e){
			alertt(e.message);
		}
		return true;
	}
	,
	update: function (args){
		try{
			var pk = window[args['tn']+'pkName'];
			var val = window[args['tn']+'pkValue'];
			var scR = "([{id:\""+pk+"\",search_operator:\"=\",value:\""+val+"\"}])";
			core.senddata({"type":"data", "callback":"grid.afterUpdate","sc":scR, "op":"search", "tn":args['tn']});			
		}
		catch(e){
			alertt(e.message);
		}
	},
	afterUpdate: function(args){
		var _1 = $.parseJSON(args['response']);
		var ft= $('#'+args['tn']+'MainDiv').attr('form_template');
		var fv = window[ft+'Vars'];
		var tv = window[ft+'Types'];
		$(fv).each(function(i,name){
			$('#'+ft+name).val(_1[0][name]);
			var currT = tv[name];
			if(currT == 'attachment'){
				//$('#file'+ft+name).val(_1[0][name]);
				$('#dir'+ft+name).val('../files/'+_1[0][name]);
				//----------------------------------------------
				if($('#file'+ft+name).attr('subtype')=='image'){
						var w = $('#file'+ft+name).attr('width')?" width='"+$('#file'+ft+name).attr('width')+"' ":"";
						var h = $('#file'+ft+name).attr('height')?" height='"+$('#file'+ft+name).attr('height')+"' ":"";
						try{$('#dir'+ft+name).html("<img alt='"+$('#file'+ft+name).attr('alt')+"' src='"+'../files/'+_1[0][name]+"' "+w+h+" />").animate({opacity: "show"});}catch(ee){;}
					}
					else{
						try{$('#dir'+ft+name).html("<a  href='"+_1['d']+"'>"+_1['f']+"</a>");}catch(ee){;}
					}
					if($('#file'+ft+name).attr('deleteallow')=='true'){
						try{
							$('#delete'+ft+name).animate({opacity: "show"});
						}
						catch(ee){;}
					}
					else{
						try{
							$('#delete'+ft+name).animate({opacity: "hide"});
						}
						catch(ee){;}
					}

				$('#dir'+ft+name).animate({opacity: "show"});
			}
			else if(currT == 'date'){
			}
			else if(currT == 'editor'){				
				//CKEDITOR.instances[ft+name].content_admin_detail_formtext.editable().setData(_1[0][name]);
				CKEDITOR.instances[ft+name].editable().setData(_1[0][name]);
			}
		});
		$('#'+args['tn']+'SearchDiv').animate({opacity: "hide"}, {duration: 350});
		$('#'+args['tn']+'BtnsDiv').animate({opacity: "hide"}, {duration: 350});
		$('#'+args['tn']+'ResultDiv').animate({opacity: "hide"}, {duration: 350});
		setTimeout('grid.showForm("'+args['tn']+'")', 500);
	},
	delete: function (args){
		try{
			if(window.confirm('Continue Delete?')){
				var sel = null;
				var tn = args['tn'];
				$('#' + tn + 'tbody').children().each(function() {
					var $this = $(this);
					if(sel == null)
						sel = $this.hasClass('grid_row_selector')?this.children[0].children[0].id:null;
				});
				if(sel != null && sel != ""){
					sel = sel.substring(tn.length);
					core.senddata({"type":"data", "callback":"grid.deleteAfter", "pk_value":sel, "op":"delete", "tn":tn});
				}
				else{
					alert('No Row is selected !');
				}
			}
		}
		catch(e){
			alertt(e.message);
		}
	},
	deleteAfter: function(args){
		var _1 = $.parseJSON(args['response']);
		$(_1).each(function(i,val){val['result']=='true'?grid.search(args):alert(val['msg']);});
	},
	insert: function (args){
		try{
			// call execute insert into grid
			// display popup, contains animate is mandatory.
			$('#'+args['tn']+'SearchDiv').animate({opacity: "hide"}, {duration: 350});
			$('#'+args['tn']+'BtnsDiv').animate({opacity: "hide"}, {duration: 350});
			$('#'+args['tn']+'ResultDiv').animate({opacity: "hide"}, {duration: 350});
			setTimeout('grid.showForm("'+args['tn']+'")', 500);
		}
		catch(e){
			alertt(e.message);
		}
	}
	,
	showForm: function(n){
		try{
			var ft= $('#'+n+'MainDiv').attr('form_template'); 
			$('#'+ft+'Popup').animate({opacity: "show"}, {duration: "slow"});
			core.back[''+ft] = n;
		}
		catch(e){
			alert(e.message);
		}
	},
	showGrid: function(n){
		var args = new Array();
		args['tn'] = n;
		args['type'] = 'data';
		ref('grid.search', window, args);
		grid.search({'tn':n});
		$('#'+n+'SearchDiv').animate({opacity: "show"}, {duration: 350});
		$('#'+n+'BtnsDiv').animate({opacity: "show"}, {duration: 350});
		$('#'+n+'ResultDiv').animate({opacity: "show"}, {duration: 350});
	},
	inserAfter: function(args){
		alert('insertAfter');
	}
};

var form = {
	ok: function(tn){
		try{
			var vars = window[tn+'Vars'];
			var types = window[tn+'Types'];
			var j = "";
			for(var i =0; i<vars.length; i++){
				if(types[vars[i]] == 'editor'){
					//Get cke_+varId is it div
					//Go to div->div->div->iframe->html->body
					//get the inner html and value of input
					var x = $('#cke_'+tn+vars[i]).children().children().children()[3].contentDocument.childNodes[1].childNodes[1].innerHTML;
					j += ""+vars[i]+":\""+x+"\",";
				}
				else
					j += ""+vars[i]+":\""+$('#'+tn+vars[i]).val()+"\",";
			}
			if(j.endsWith(","))
				j = j.substring(0, j.length-1);
			core.senddata({"type":"data", "callback":"form.okcancelAfter", "d":"({"+j+"})", "op":"save", "tn":"modules_form"});
		}
		catch(e){
			alertt(e.message);
		}
	},
	okcancelAfter: function(args){
		try{
			var tn = args['tn'];
			$('#'+tn+'Popup').animate({opacity: "hide"}, {duration: "slow"});
			var gt = core.back[tn];
			setTimeout('grid.showGrid("'+gt+'")', 500); 
		}
		catch(e){
			alertt(e.message);
		}
	},
	cancel : function(tn){
		try{
			form.okcancelAfter({'tn':tn});
		}
		catch(e){
			alertt(e.message);
		}
	},
	buildDetails:function(tn, pkVal){
		
	}
};
/*
 * var Grid = function Grid(_tn, _source) { var tn = _tn; var source = _tn; };
 * 
 * Grid.prototype = { setSelector : function(tn, inp) { try { $('#' + tn +
 * 'tbody').children().each(function() { var $this = $(this);
 * $this.removeClass('grid_row_selector'); if (this.children[0].children[0].id ==
 * inp.id) $this.addClass('grid_row_selector'); }); } catch (e) {
 * alertt(e.message); } }, }; var Form = function Form(_tn, _source) { var tn =
 * _tn; var source = _source; }; Form.prototyope = {
 *  };
 */

var ContentAdmin = {
	contentSelected:function(contentId){
		//Fill Extra Fields search field, Fill Extra Fields Form field
		$('#__search__content_admin_extracontent_id').val(contentId);
		$('#__search__content_admin_detailcontent_id').val(contentId);
		$('#__search__content_admin_form_girdcf_content_id').val(contentId);
		//Fill Details search and form fields
		
	},
	contentDetailSelected:function(contentDetailId){
		alert('ContentAdmin.contentDetailSelected:'+contentDetailId);
	},
	contentFormAnswerSelected:function(formAnswerId){
			
	},
	contentFormSelected:function(formId){
		try{
			$('#__search__content_admin_form_detailform_id').val(formId);
			grid.search({tn:"content_admin_form_detail",op:"search",type:"data",sc:{id:"form_id",search_operator:"=",value:''+formId}});
		}
		catch(e){
			alertt(e.message);
		}
	}
};
var Module = {
		moduleTypeSel:function(mId){
			$('#__search__module_types_detailsmtd_mi').val(mId);
			$('#module_types_details_formmtd_mi').val(mId);
			grid.search({tn:"module_types_details",op:"search",type:"data",sc:{id:"mtd_mi",search_operator:"=",value:mId}});
		},
		getSubItems: function(inp){
			
		},
		initForm: function(inp){
			//reset custom fields
		}
};

var FileUpploader = function(name){
	// $('#file'+name).change(function(e) {
	var inp = document.getElementById('file'+name);
	inp.addEventListener('change', function(e) {
		try{
			var inp = document.getElementById('file'+name);
			var file = inp.files[0];
			
			var fd = new FormData();
			fd.append(""+name, file);
			fd.append("name", name);
			
			try{$('#delete'+name).hide();}catch(ee){;}
			try{$('#err'+name).hide();}catch(ee){;}
			try{$('#dir'+name).hide();}catch(ee){;}
			
			fd.append("type", "upload");
			var xhr = new XMLHttpRequest();
			xhr.open('POST', '../control/DataHandler.php', true);
			xhr.upload.onprogress = function(e) {
				if (e.lengthComputable) {
					var percentComplete = (e.loaded / e.total) * 100;
					percentComplete = parseInt(''+percentComplete);
					$('#dir'+name).animate({opacity: "show"});
					$('#dir'+name).html(''+percentComplete+' % ');
				}
			};
			
			xhr.onload = function() {
				if (this.status == 200) {
					// Must parse it as jsons
					var _1 = $.parseJSON(this.response);
					if(_1['result']=='ok'){
						try{
							$('#'+name).val(_1['f']);
							if($('#file'+name).attr('subtype')=='image'){
								// var image = document.createElement('img');
								// image.src = resp.dataUrl;
								// document.body.appendChild(image);
								var w = $('#file'+name).attr('width')?" width='"+$('#file'+name).attr('width')+"' ":"";
								var h = $('#file'+name).attr('height')?" height='"+$('#file'+name).attr('height')+"' ":"";
								try{$('#dir'+name).html("<img alt='"+$('#file'+name).attr('alt')+"' src='"+_1['d']+"' "+w+h+" />").animate({opacity: "show"});}catch(ee){;}
								// $('#dir'+name).animate({opacity: "show"});
								
							}
							else{
								try{$('#dir'+name).html("<a  href='"+_1['d']+"'>"+_1['f']+"</a>");}catch(ee){;}
							}
							if($('#file'+name).attr('deleteallow')=='true'){
								try{
									$('#delete'+name).animate({opacity: "show"});
								}
								catch(ee){;}
							}
							else{
								try{
									$('#delete'+name).animate({opacity: "hide"});
								}
								catch(ee){;}
							}
						}
						catch(eee){
							;
						}
					}
					else if(_1['result'] === 'error'){
							try{$('#err'+name).html(_1['errors']).animate({opacity: "show"});;}catch(ee){}
						}
				}
			};
			xhr.send(fd);
		}
		catch(e){
			alertt(e.message);
		}
	}, false);
	$('#delete'+name).click(function(){
		try{
			if(window.confirm(delMsg)){
				var fd = new FormData();
				fd.append("name", $('#'+name).val());
				try{$('#delete'+name).hide();}catch(ee){;}
				try{$('#err'+name).hide();}catch(ee){;}
				try{$('#dir'+name).hide();}catch(ee){;}
				fd.append("type", "delAttach");
				var xhr = new XMLHttpRequest();
				xhr.open('POST', '../control/DataHandler.php', true);
				xhr.upload.onprogress = function(e) {
					if (e.lengthComputable) {
						var percentComplete = (e.loaded / e.total) * 100;
						percentComplete = parseInt(''+percentComplete);
						$('#dir'+name).animate({opacity: "show"});
						$('#dir'+name).html(''+percentComplete+' % ');
					}
				};
					
				xhr.onload = function() {
					if (this.status == 200) {
						var _1 = $.parseJSON(this.response);
						if(_1['result']=='ok'){
							try{
								$('#'+name).val('');
								$('#file'+name).val('');
								try{$('#dir'+name).html("");}catch(ee){;}
							}
							catch(eee){
								;
							}
						}
						else if(_1['result'] === 'error'){
								try{$('#err'+name).html(_1['errors']).animate({opacity: "show"});;}catch(ee){}
							}
					}
				};
				xhr.send(fd);
			}
		}
		catch(e){
			alertt(e.message);
		}
	});
};
