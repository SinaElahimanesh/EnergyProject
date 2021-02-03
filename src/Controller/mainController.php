<?php



header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");


parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $queries);
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );
$requestedMethod=$_SERVER["REQUEST_METHOD"];

$user=sessionBasedLogin();
if($user==false) {
    header("Location: example.com/login ");///// url to login page!!!
    die();
}

/// the type of user now is in the $user->type!
/// in each if you must compare the authorized user type with $user->type!

if($uri[1]=="User"){
    $userController=null;
    if($requestedMethod=="POST"){
        $userController=new UserController("POST");
    }
    elseif ($requestedMethod=="GET"){
        if(isset($uri[2])){
            $userController=new UserController("GET",$uri[2]);
        }else{
            $userController=new UserController("GET");
        }

    }elseif ($requestedMethod=="PUT" && isset($uri[2])){
            $userController=new UserController("PUT",$uri[2]);
    }elseif ($requestedMethod=="DELETE" && isset($uri[2])){
            $userController=new UserController("DELETE",$uri[2]);
    }else{
        die();////
    }
    $userController->processRequest();
}
elseif($uri[1]=="Idea"){
    $ideaController=null;
    if($requestedMethod=="POST"){
        $ideaController=new IdeaController("POST");

    }elseif($requestedMethod=="GET" && isset($queries["type"]) && isset($uri[2]) ){
        if($queries["type"]=="owner"){
            $ideaController=new IdeaController("GET",$uri[2]);

        }elseif($queries["type"]=="idea"){
            $ideaController=new IdeaController("GET",null,$uri[2]);
        }else{
            die();
        }

    }elseif ($requestedMethod=="PUT" && isset($queries["type"]) && isset($uri[2])){
            $ideaController=new IdeaController("PUT",null,$uri[2]);
    }elseif ($requestedMethod=="DELETE" && isset($queries["type"]) && isset($uri[2])) {
            $ideaController = new IdeaController("DELETE", null, $uri[2]);
    }else{
        die();
    }
    $ideaController->processRequest();

}
elseif ($uri[1]=="Patent"){
    $patentController=null;
    if($requestedMethod=="POST"){
        $patentController=new PatentController("POST");

    }elseif($requestedMethod=="GET" && isset($queries["type"]) && isset($uri[2])){

        if($queries["type"]=="owner"){
            $patentController=new PatentController("GET",null,$uri[2]);

        }elseif($queries["type"]=="patent"){
            $patentController=new PatentController("GET",$uri[2],null);
        }else{
            die();
        }

    }elseif ($requestedMethod=="PUT" && isset($queries["type"]) && isset($uri[2])){

        $patentController=new PatentController("PUT",$uri[2],null);

    }elseif ($requestedMethod=="DELETE" && isset($queries["type"]) && isset($uri[2])){
        $patentController=new PatentController("DELETE",$uri[2],null);
    }else{
        die();
    }
    $patentController->processRequest();

}elseif ($uri[1]=="message"){
    $messageController=null;
    if($requestedMethod=="POST"){

    }elseif($requestedMethod=="GET"){

    }elseif ($requestedMethod=="PUT"){

    }elseif ($requestedMethod=="DELETE"){

    }

}elseif($uri[1]=="auth"){
    $loginController=null;
    if($requestedMethod=="POST"){
        $loginController=new loginController("POST");
    }elseif($requestedMethod="DELETE"){
        $loginController=new loginController("DELETE");
    }else{
        die();
    }
}else{
    die();
}


  function sessionBasedLogin(){ /// inja bayad bad az ye hafte user ru part kone biron va redirect kone safhe login!
    if(isset($_COOKIE[session_name()])==false){
        return false;
    }
    $sessionId=$_COOKIE[session_name()];
    $db=new databaseController();
    $statement=$db->getConnection()->prepare("SELECT `loginTime` FROM `users_sessions` WHERE `sessionId`=$sessionId");
    $statement->execute();
    $result = $statement->setFetchMode(PDO::FETCH_ASSOC);
    if(time()-$result["loginTime"]>604800){
        $statement="DELETE FROM `users_sessions` WHERE `sessionId`=$sessionId";
        $db->getConnection()->exec($statement);
        return false;
    }
    $userController=new UserController();
    $user=$userController->loadUserFromSession();
    $user->setAuthenticated(1);
    $userController->saveUserObjectInSession($user);
    return $user;
}




