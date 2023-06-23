<?php
namespace vendor\khallaf\views;
class View
{
    public $path;
    const EXTENTION=".khallaf.php";
    public $current_view;
    public function __construct($view,...$params)
    {
        $this->current_view=$view;
        $this->path=explode(".",implode(".",explode("/",$view)));

        $this->path=Application_path."/views/".implode("/",$this->path).self::EXTENTION;
    }
    public function render(){
        if(is_file($this->path))
        {
            include $this->path;
        }
        else
        {
            dnd("there is no view with {$this->current_view}");
        }
    }
    public function get_template(...$args){

        try {
            for ($i=0;$i<count($args);$i++){
                foreach ($args[$i] as $key => $val){
                    $$key=$val;
                }
            }
            $myfile = fopen($this->path,"r") or die("Unable to open file!");
            $file=fread($myfile,filesize($this->path));
            ob_start();
            eval("?>$file<?php");
            $data=ob_get_clean();
            fclose($myfile);
            echo $data;

        }catch (\Throwable $e){
            dnd("you must close all oppening tags in php {$e->getMessage()}");
        }

    }

}