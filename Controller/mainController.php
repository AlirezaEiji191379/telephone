<?php
require_once ("loginController.php");
require_once ("UserController.php");
require_once ("authHandler.php");
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );
$requestedMethod=$_SERVER["REQUEST_METHOD"];


if(isset($uri[4])){
    $controller=null;
    if($uri[4]=="login"){
        $controller=new loginController($requestedMethod);
        $controller->requestProcess();
    }
    if($uri[4]=="User"){
        $controller=new UserController($requestedMethod);
        $controller->requestProcess();
    }
    if($uri[4]=="refresh"){
        $controller=new authHandler("GET",null,"wer");
        $controller->requestProcess();
    }
}else{

}








