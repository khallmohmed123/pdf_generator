/*!
 * bootstrap-treetable - jQuery plugin for bootstrapview treetable
 *
 * Copyright (c) 2007-2015 songhlc
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   http://github.com/songhlc
 *
 * Version:  1.0.0
 *
 */
(function($){
	$.fn.bstreetable = function(options){
		$window = window;
		var element = this;
		var $container;
		var settings = {
			container:window,
			data:[],
			extfield:[],//{title:"column name",key:"",type:"input"}
			nodeaddEnable:true,
			maxlevel:4,
			nodeaddCallback:function(data,callback){},
			noderemoveCallback:function(data,callback){},
			nodeupdateCallback:function(data,callback){},
            customalert:function(msg){
                alert(msg);
            },
            customconfirm:function(msg){
                return confirm(msg);
            },
            text:{
                NodeDeleteText:"Are You Sure To Delete This Node?"
            }
		};
		var TREENODECACHE = "treenode";
		var language ={};
		language.addchild = "Add A Child Node";
		if(options) {           
            $.extend(settings, options);
        }
        /* Cache container as jQuery as object. */
        $container = (settings.container === undefined ||
                      settings.container === window) ? $window : $(settings.container);
        /*render data*/
        var dom_addFirstLevel = $("<div class='tt-operation m-b-sm'></div>").append($("<button class='btn btn-primary btn-sm j-addClass'><i class='fa fa-level-down'></i>&nbsp;Add A Root Node</button>"));
        var dom_table = $("<div class='tt-body'></div>");
        var dom_header = $("<div class='tt-header'></div>");
        /*renderHeader*/
        renderHeader(dom_header);
        element.html('').append(dom_addFirstLevel).append(dom_header);
        var treeData = {};
        /*render firstlevel tree*/
        for(var i=0;i<settings.data.length;i++){
        	var row = settings.data[i];
        	//render first level row while row.pid equals 0 or null or undefined
        	if(!row.pid){
                generateTreeNode(dom_table,row,1);
        		treeData[row.id] = row;
        	}
        	
        }

        element.append(dom_table);
        /*delegate click event*/
        element.delegate(".j-expend","click",function(event){
        	if(event.target.classList[0]=="fa"){
        		var treenode = treeData[$(this).attr('data-id')];
	        	toggleicon($(this));
	        	if($(this).parent().attr('data-loaded')){
	        		toggleExpendStatus($(this),treenode);        		
	        	}
	        	else{	        	
		        	loadNode($(this),treenode);
	        	}
        	}        	        
        });
        element.delegate(".j-addClass","click",function(){
            var curElement = $(".tt-body");
            var row = {id:"",name:"",pid:0};
            var curLevel = 1;
            generateTreeNode(curElement,row,curLevel,true);
        });
        /*delegate remove event*/
        element.delegate(".j-remove","click",function(event){
            var parentDom = $(this).parents(".class-level-ul");
            var isRemoveAble = false;
            if(parentDom.attr("data-loaded")=="true"){
                if(parentDom.parent().find(".class-level").length>0){
                    settings.customalert("Can not be deleted!");
                    return;
                }
                else{
                    isRemoveAble = true;
                }
            }
            else{
                if(parentDom.attr("data-id")){
                    var existChild = false;
                    for(var i=0;i<settings.data.length;i++){
                        if(settings.data[i].pid==parentDom.attr("data-id")){
                            existChild = true;
                            break;
                        }
                    }
                    if(existChild){
                        settings.customalert("Can not be deleted!");
                        return;
                    }
                    else{
                        isRemoveAble = true;
                    }
                }
                else{
                    isRemoveAble = true;
                }
            }
            if(isRemoveAble){
                var that = $(this);
                if(settings.customconfirm(settings.text.NodeDeleteText)){
                    /*trigger remove callback*/
                    settings.noderemoveCallback(that.parents(".class-level-ul").attr("data-id"),function(){
                        that.parents(".class-level-ul").parent().remove();
                    });
                }
            }
        });
        /*delegate addchild event*/
        element.delegate(".j-addChild","click",function(){
        	var curElement = $(this).closest(".class-level");
            var requiredInput = curElement.find(".form-control*[required]");
            var hasError = false;
            requiredInput.each(function(){
                if($(this).val()==""){
                    $(this).parent().addClass("has-error");
                    hasError = true;                    
                }
            });
            if(!hasError){
                var pid = curElement.find(".j-expend").attr("data-id");
                var curLevel = $(this).parents(".class-level-ul").attr("data-level")-0+1; 
                var row = {id:"",name:"",pid:pid};
                generateTreeNode(curElement,row,curLevel);   
            }
        	     	
        });
        element.delegate(".form-control","focus",function(){
            $(this).parent().removeClass("has-error");
        });
        /*delegate lose focus event*/
        element.delegate(".form-control","blur",function(){
            var curElement = $(this);
            var data = {};
            data.id = curElement.parent().parent().attr("data-id");
            var parentUl = curElement.closest(".class-level-ul");
            data.pid = parentUl.attr("data-pid");
            data.innercode = parentUl.attr("data-innercode");
            data.pinnercode = curElement.parents(".class-level-"+(parentUl.attr("data-level")-1)).children("ul").attr("data-innercode");
            parentUl.find(".form-control").each(function(){
                data[$(this).attr("name")]=$(this).val();                
            });
            if(!data.id&&!curElement.attr("data-oldval")){
                console.log("add node");                
                settings.nodeaddCallback(data,function(_data){
                    if(_data){
                        curElement.parent().attr("data-id",_data.id);
                        curElement.parent().parent().attr("data-id",_data.id);
                        curElement.parent().parent().attr("data-innercode",_data.innercode);
                        curElement.attr("data-oldval",curElement.val());
                    }
                });                            
            }
            else if(curElement.attr("data-oldval")!=curElement.val()){
                console.log("update node");   
                settings.nodeupdateCallback(data,function(){
                    curElement.attr("data-oldval",curElement.val());
                });
                
            }
        });
        function renderHeader(_dom_header){
        	var dom_row = $('<div></div>');
        	dom_row.append($("<span class='maintitle'></span>").text(settings.maintitle));
        	dom_row.append($("<span></span>"));        	
        	//render extfield
    		for(var j=0;j<settings.extfield.length;j++){
    			var column = settings.extfield[j];    			
    			$("<span></span>").css("min-width","166px").text(column.title).appendTo(dom_row);
    		}
    		dom_row.append($("<span class='textalign-center'>Operation</span>")); 
    		_dom_header.append(dom_row);
        }
        function generateColumn(row,extfield){
        	var generatedCol;
        	switch(extfield.type){
        		case "input":generatedCol=$("<input type='text' class='form-control input-sm'/>").val(row[extfield.key]).attr("data-oldval",row[extfield.key]).attr("name",extfield.key);break;
                case "select":generatedCol=$("<select class=\"form-select\" aria-label=\"Default select example\"></select>").val(row[extfield.key]).attr("data-oldval",row[extfield.key]).attr("name",extfield.key);
                    extfield.options.forEach((elem)=>{
                        generatedCol.append("<option value='"+elem+"'>"+elem+"</option>");
                    })
                break;
        		default:generatedCol=$("<span></span>").text(row[extfield.key]);break;
        	}
        	return generatedCol;
        }
        function toggleicon(toggleElement){
        	var _element = toggleElement.find(".fa");
        	if(_element.hasClass("fa-plus")){
        		_element.removeClass("fa-plus").addClass("fa-minus");
        		toggleElement.parent().addClass("selected");
        	}else{
        		_element.removeClass("fa-minus").addClass("fa-plus");
        		toggleElement.parent().removeClass("selected")
        	}
        }
		function toggleExpendStatus(curElement){
			if(curElement.find(".fa-minus").length>0){
                 curElement.parent().parent().find(".class-level").removeClass("rowhidden");
            }
            else{
                curElement.parent().parent().find(".class-level").addClass("rowhidden");
            }
           
		}
		function collapseNode(){

		}
		function expendNode(){

		}
		function loadNode(loadElement,parentNode){
			var curElement = loadElement.parent().parent();
        	var curLevel = loadElement.parent().attr("data-level")-0+1;
        	if(parentNode&&parentNode.id){
                for(var i=0;i<settings.data.length;i++){
    	        	var row = settings.data[i];
    	        	//render first level row while row.pid equals 0 or null or undefined
    	        	if(row.pid==parentNode.id){
    	        		generateTreeNode(curElement,row,curLevel);
                        //cache treenode 
                        treeData[row.id] = row;
    	        	}	        	
    	        }                
            }
            loadElement.parent().attr('data-loaded',true);
	        
		}
        function generateTreeNode(curElement,row,curLevel,isPrepend){
            var dom_row = $('<div class="class-level class-level-'+curLevel+'"></div>');
            var dom_ul =$('<ul class="class-level-ul"></ul>');
            dom_ul.attr("data-pid",row.pid).attr("data-level",curLevel).attr("data-id",row.id);
            row.innercode&&dom_ul.attr("data-innercode",row.innercode);
            if(curLevel-0>=settings.maxlevel){
                $('<li class="j-expend"></li>').append('<label class="fa p-xs"></label>').append($("<input type='text' class='form-control input-sm' required/>").attr("data-oldval",row['name']).val(row['name']).attr("name","name")).attr('data-id',row.id).appendTo(dom_ul);
                dom_ul.attr("data-loaded",true);
            }
            else{
                $('<li class="j-expend"></li>').append('<label class="fa fa-plus p-xs"></label>').append($("<input type='text' class='form-control input-sm' required/>").attr("data-oldval",row['name']).val(row['name']).attr("name","name")).attr('data-id',row.id).appendTo(dom_ul);
            }
           
            if(settings.nodeaddEnable){
                if(curLevel-0>=settings.maxlevel){
                    $("<li></li>").attr("data-id",row.id).appendTo(dom_ul);
                }
                else{
                    $("<li></li>").append($('<button class="btn btn-outline btn-sm j-addChild"><i class="fa fa-plus"></i>'+language.addchild +'</button>').attr("data-id",row.id)).appendTo(dom_ul);    
                }
                
            }       
            for(var j=0;j<settings.extfield.length;j++){
                    var colrender = settings.extfield[j];
                    var coltemplate = generateColumn(row,colrender);
                    $('<li></li>').attr("data-id",row.id).html(coltemplate).appendTo(dom_ul);
            }
        dom_ul.append(`<li class="image-box">
                        <input type='file' onchange="readURL(this);" />
                        <img class="img" src="http://placehold.it/180" alt="your image" />
                        </li>`)
            dom_ul.append($("<li><i class='fa fa-remove j-remove'></i></li>"));
            dom_row.append(dom_ul);
            if(isPrepend){
                curElement.prepend(dom_row);
            }
            else{
                curElement.append(dom_row);
            }
            
        }
	}
})(jQuery)