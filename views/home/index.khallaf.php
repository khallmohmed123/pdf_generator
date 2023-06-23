<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Report Card</title>
    <!-- BootCss -->
    <link href="<?= asset("css/bootstrap.min.css") ?>" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" type="text/css" href="<?= asset("css/style.css") ?>">
    <link href="http://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.4/css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="http://cdn.bootcss.com/font-awesome/4.5.0/css/font-awesome.min.css" type="text/css" media="screen" title="no title" charset="utf-8"/>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.3/jspdf.debug.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.js" defer></script>
    <script type="text/javascript" src="<?= asset("js/script.js") ?>" defer></script>
      <style>
          .treetable .fa{
              cursor: pointer;
              padding-right: 5px;
          }
          .treetable .rowhidden{
              display: none;
          }
          .treetable .j-addChild{
              display: none;
          }
          .treetable .selected .j-addChild{
              display: block;
          }
          .treetable .btn-outline{
              background-color: transparent;
          }
          .treetable .form-control{
              width: auto;
              display: inline-block;
          }
          .treetable .textalign-center{
              text-align: center;
          }
          .treetable .j-expend{
              cursor: pointer;
              width: 35% !important;
              text-align: left !important;
          }
          .treetable .maintitle{
              width: 35% !important;
          }
          .treetable .j-remove{
              padding: 8px;
              cursor: pointer;
              font-size: 16px;
              color:red;
          }
          .treetable .tt-header{
              margin-top:10px;
          }
          .treetable .class-level-2 .class-level-ul .j-expend{
              position: relative;
              left: 22px;
          }
          .treetable .class-level-3 .class-level-ul .j-expend{
              position: relative;
              left: 44px;
          }
          .treetable .class-level-4 .class-level-ul .j-expend{
              position: relative;
              left: 66px;
          }
          .treetable .class-level-1 {
              border-bottom: dashed 1px #eee;
          }
          .treetable .class-level-ul{
              padding: 0;
              margin-bottom: 2px;
          }
          .treetable .class-level-ul li {
              float: left;
              text-align: center;
              vertical-align: middle;
              padding: 1px 10px;
              min-width: 120px;
              list-style: none;
          }
          .treetable .class-level-ul:after {
              display: block;
              clear: both;
              height: 0;
              content: "\0020";
          }
          .treetable .tt-header div span {
              width: auto;
              line-height: 29px;
              display: inline-block;
              min-width: 120px;
              text-align: center;
          }
          .treetable .tt-body{
              border: solid 1px #DDD;
              padding-top: 1px;
              background-color:#FFF;
          }
          .treetable .tt-header div{
              border: solid 1px #DDD;
              border-bottom:none;
              background-color:#FFF;
          }
          img{
              width: fit-content;
              max-width:70px;
          }
          input[type=file]{
              padding:10px;
              background:#2d2d2d;}
      </style>
  </head>
  <body>
  <div class="header">
      <h1 class="center">customize pdf  report resume</h1>
      <div>
          <button class="btn btn-primary btn-lg btn-add-some-pages"> + </button>
      </div>
  </div>
  <div class="container">
      <div class="row">
          <div id="bs-ml-treetable" class="treetable">
          </div>
      </div>
  </div>
  <div>
      <button class="btn btn-primary btn-lg make-pdf-data"> make pdf</button>
  </div>
  <!-- Bootstrap JS -->
    <script src="<?= asset("js/bootstrap.min.js") ?>"></script>
    <script type="text/javascript" src="<?= asset("js/jquery.edittreetable.js?".rand()) ?>"></script>
  <script>
       $(".btn-add-some-pages").on("click",function (){
           var page=$(".number").length
           $.ajax({
                   url: "<?= url("page") ?>",
                   method: "get",
                   data: {"page":page},
                   success: function (response)
                   {
                       $(".container .row").append(response);
                   }
               }
           )
       })
       $(document).on("click",".add-body-section",function (){
           $.ajax({
                   url: "<?= url("Body_section") ?>",
                   method: "get",
                   data: {},
                   success: function (response)
                   {
                       console.log(response)
                   }
               })
       })
       $("#bs-ml-treetable").bstreetable({
           maintitle:"khallaf",
           nodeaddCallback:function(data,callback){
           },
           noderemoveCallback:function(data,callback){
               callback();
           },
           nodeupdateCallback:function(data,callback){
               callback();
           },
           extfield:[
               {title:"innercode",key:"innercode",type:"select",options:["image","text"]}
           ]
       })
       function readURL(input) {
           if (input.files && input.files[0]) {
               var reader = new FileReader();

               reader.onload = function (e) {
                   console.log( $(input).parent().find('.img'))
                   $(input).parent().find('.img').attr('src', e.target.result);
               };

               reader.readAsDataURL(input.files[0]);
           }
       }
       $(document).on("change",".form-select",function (){
           switch (this.value){
               case "image":
                   $(this).parent().next().html(`
                        <input type='file' onchange="readURL(this);" />
                        <img class="img" src="http://placehold.it/180" alt="your image" />
                    `)
                   break;
               case "text":
                   $(this).parent().next().html(`<textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>`)
                   break;
               default:
                   break
           }
           console.log(this.value)
       })
       $(".make-pdf-data").on("click",function (){
           var data=Array();
            $("#bs-ml-treetable .tt-body > .class-level").each(function(elem){
                var childs=Array();
                elem=$(this);
                var type=elem.find(".class-level-ul select.form-select").val()
                var value=get_data(elem,type)
                var header=elem.find(".class-level-ul input[type=text]").val()
                nodes_extarct(elem,childs)
                data.push({
                    type:type,
                    header:header,
                    value:value,
                    childs:childs
                })
            })
           $.ajax({
               url:"<?= url("make_pdf") ?>",
               method:"post",
               data:{data:JSON.stringify(data)},
               success:function (resposne){
                    console.log(resposne)
               }
           })
       })
       function nodes_extarct(elem,data)
       {
            var direct_child=elem.find("> .class-level")
           direct_child.each(function (){
               var childs=Array();
               elem_bind=$(this);
               var type=elem_bind.find(".class-level-ul select.form-select").val()
               var header=elem_bind.find(".class-level-ul input[type=text]").val()
               var value=get_data(elem_bind,type)
               nodes_extarct(elem_bind,childs)
               data.push({
                   type:type,
                   header:header,
                   value:value,
                   childs:childs
               })
           })
            return;
       }
       function get_data(elem,type){
           return (type==="image")?elem.find("img").attr("src"):elem.find("textarea").val();
       }
  </script>
  </body>
</html>
