<?php
namespace controllers;
use vendor\khallaf\views\View;
class home{
    public function index($request){
        $view= new View("home.index",array());
        $view->render();
    }
    public function page($request){
        $view= new View("helper.page",array());
        $view->get_template(array("page"=>$request->page));
    }
    public function Body_section($request){
        $view= new View("helper.BodySection",array());
        $view->get_template(array());
    }
    public function make_pdf($request){
        dnd(json_decode($request->data, true));
    }
}