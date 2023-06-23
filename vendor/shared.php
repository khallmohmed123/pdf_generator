<?php
    function dnd(...$msg){
        if (is_array($msg))
        {
            foreach ($msg as $key=>$value)
            {
                echo "<pre>";
                var_dump($value);
                echo "</pre>";
            }
        }
        else
        {
            echo "<pre>";
            var_dump($msg);
            echo "</pre>";
        }


    }
    function asset($path)
    {
        return "http://".$_SERVER["SERVER_ADDR"]."/public/".$path;
    }
    function url($path){
        return "http://".$_SERVER["SERVER_ADDR"]."/$path";
    }
    class Routes
    {
        const POST="post";
        const GET="get";
        public static $route=array();
        public static function Post($path,$callabck)
        {
            self::$route[self::POST][$path]=$callabck;
        }
        public static function Get($path,$callabck)
        {
            self::$route[self::GET][$path]=$callabck;
        }
        public static function Resolve()
        {
            $path=$_SERVER["REQUEST_URI"] ?? "/";
            $postition=strpos($path,"?");
            if ($postition!==false) $path=substr($path, 0,$postition);
            $method=strtolower($_SERVER["REQUEST_METHOD"]);
            $callback=self::$route[$method][$path] ?? false;
           if ($callback)
           {
               $params=new stdClass();
               switch ($method)
                {
                    case self::POST:
                        foreach ($_POST as $key=>$value){
                            $params->{$key}=$value;
                        }
                        break;
                    case self::GET:
                        foreach ($_GET as $key=>$value){
                            $params->{$key}=$value;
                        }
                        break;
                }
               $params->path=$path;
               $params->origin=$_SERVER["SERVER_ADDR"];
               $callback[0]=new $callback[0]();
               $resposne= call_user_func($callback,$params);
           }else
           {
               dnd("error");
           }
        }
    }
