<?php
    $base = $base ?? "../";  
    
    require_once $base . "libs/utils.php";
    require_once $base . "model/access.php";
    require_once $base . "model/user.php";

    //a --> access
    try{
        User::initialize();
        $access = URL::decode("a");
        if(URL::isPost()){
            switch($access){
                case "login":
                    user::login($_POST);
                break;
                case "register":
                    user::register($_POST);
                break;
                default:
                    //XD
                break;
            }
            URL::redirect("../../");
        }elseif(URL::isGet()){
            switch($access){
                case "logout":
                    user::logout();
                    URL::redirect("../../login.php");
                break;
            }
        }else{
            throw new HTTPException("unsupported method", 405);
        }
    }catch(HTTPException $e) {
        http_response_code($e->getCode());
        Logger::log("Access controll: Error in $access ".$e->getMessage()." (HTTP ".$e->getCode().")", null, LoggerLevel::ERROR);
        SESSION::start();
        echo $e->getMessage();
        $_SESSION["error"] = $e->getMessage();
        //URL::redirect("../../");
    }catch(Exception $e) {
        //throw $e;
        echo "Ha ocurrido un error interno";
        $_SESSION["error"] = "Ha ocurrido un error interno";
        Logger::log("Access controll: Severe error in $access".$e->getMessage(), null, LoggerLevel::ERROR);
        //http_response_code(500);
        //URL::redirect("../../");
    }
?>