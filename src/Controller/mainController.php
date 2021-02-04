<?php

require_once("UserController.php");
require_once ("loginController.php");
require_once ("../Model/User.php");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");



parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $queries);
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );
$requestedMethod=$_SERVER["REQUEST_METHOD"];

$user=loginController::sessionBasedLogin();
if($user==false) {
    if((($uri[5]=="User" || $uri[5]=="auth") && $requestedMethod=="POST")==false) {
        //header("Location:");///// url to login page!!!
        echo "hoghe!";
        die();
    }
}

/// the type of user now is in the $user->type!
/// in each if you must compare the authorized user type with $user->type!

if($uri[5]=="User"){
    $userController=null;
    if($requestedMethod=="POST"){
        $userController=new UserController("POST" );
    }
    elseif ($requestedMethod=="GET"){
        if(isset($uri[6])){
            $userController=new UserController("GET",$uri[6],$user);
        }else{
            $userController=new UserController("GET",$user);
        }

    }elseif ($requestedMethod=="PUT" && isset($uri[2])){
            $userController=new UserController("PUT",$uri[2]);
    }elseif ($requestedMethod=="DELETE" && isset($uri[2])){
            $userController=new UserController("DELETE",$uri[2],$user);
    }else{
        die();////
    }
    $userController->processRequest();
}
elseif($uri[5]=="Idea"){
    $ideaController=null;
    if($requestedMethod=="POST"){
        $ideaController=new IdeaController("POST");

    }elseif($requestedMethod=="GET" && isset($queries["type"]) && isset($uri[2]) ){
        if($queries["type"]=="owner"){
            $ideaController=new IdeaController("GET",$uri[2],$user);

        }elseif($queries["type"]=="idea"){
            $ideaController=new IdeaController("GET",null,$uri[2],$user);
        }else{
            die();
        }

    }elseif ($requestedMethod=="PUT" && isset($queries["type"]) && isset($uri[2])){
            $ideaController=new IdeaController("PUT",null,$uri[2]);
    }elseif ($requestedMethod=="DELETE" && isset($queries["type"]) && isset($uri[2])) {
            $ideaController = new IdeaController("DELETE", null, $uri[2],$user);
    }else{
        die();
    }
    $ideaController->processRequest();

}
elseif ($uri[5]=="Patent"){
    $patentController=null;
    if($requestedMethod=="POST"){
        $patentController=new PatentController("POST");

    }elseif($requestedMethod=="GET" && isset($queries["type"]) && isset($uri[2])){

        if($queries["type"]=="owner"){
            $patentController=new PatentController("GET",null,$uri[2],$user);

        }elseif($queries["type"]=="patent"){
            $patentController=new PatentController("GET",$uri[2],null,$user);
        }else{
            die();
        }

    }elseif ($requestedMethod=="PUT" && isset($queries["type"]) && isset($uri[2])){

        $patentController=new PatentController("PUT",$uri[2],null);

    }elseif ($requestedMethod=="DELETE" && isset($queries["type"]) && isset($uri[2])){
        $patentController=new PatentController("DELETE",$uri[2],null,$user);
    }else{
        die();
    }
    $patentController->processRequest();

}elseif($uri[5]=="auth"){
    $loginController=null;
    if($requestedMethod=="POST"){
        $loginController=new loginController("POST");
    }elseif($requestedMethod="DELETE"){
        $loginController=new loginController("DELETE");
    }else{
        die();
    }

    $loginController->processRequest();

}else{
    die();
}







