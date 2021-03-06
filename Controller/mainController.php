<?php
require_once ("loginController.php");
require_once ("UserController.php");
require_once ("authHandler.php");
require_once ("contactController.php");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );
$requestedMethod=$_SERVER["REQUEST_METHOD"];
parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $queries);

if(isset($uri[4])){
    $controller=null;
    if($uri[4]=="login"){
        $controller=new loginController($requestedMethod);
        $controller->requestProcess();
    }
    if($uri[4]=="User" && !isset($queries["user"])){
        $controller=new UserController($requestedMethod);
        $controller->requestProcess();
    }
    if($uri[4]=="User" && isset($queries["user"])){
        $controller=new UserController($requestedMethod,$queries["user"]);
        $controller->requestProcess();
    }
    if($uri[4]=="refresh"){
        $controller=new authHandler("GET",null,"zdf");
        $controller->requestProcess();
    }
    if($uri[4]=="authUser"){
        $controller=new authHandler("GET","User",null);
        $controller->requestProcess();
    }
    if($uri[4]=="authAdmin"){
        $controller=new authHandler("GET","Admin",null);
        $controller->requestProcess();
    }
    if($uri[4]=="contact"){
        $controller=null;
        if(isset($uri[5])){
            $controller=new contactController($requestedMethod,$uri[5]);
        }else{
            $controller=new contactController($requestedMethod,null);
        }
        $controller->requestProcess();
    }
    if($uri[4]=="logout"){
        $controller=new loginController($requestedMethod);
        $controller->requestProcess();
    }

}else{
    die("unproccessable request!");
}








