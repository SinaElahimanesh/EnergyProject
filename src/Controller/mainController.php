<?php



header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");


//parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $queries);
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );
$requestedMethod=$_SERVER["REQUEST_METHOD"];


//$authenticate=new loginController(null,$queries["client"]);
//
//$authenticate->validateUser();








