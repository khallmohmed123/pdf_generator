<?php

function my_autoloader($class) {

    require_once Application_path.'/'.implode('/',explode('\\',$class)) . '.php';
}
spl_autoload_register("my_autoloader");