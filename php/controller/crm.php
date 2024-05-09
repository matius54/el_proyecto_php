<?php
    $base = $base ?? "../";  
    
    require_once $base . "libs/utils.php";

    require_once $base . "model/access.php";
    require_once $base . "model/crm.php";

    //a --> access
    try{
        User::initialize();
        $access = URL::decode("a");
        if(URL::isGet()){
            switch($access){
                case "enter":
                    $id = URL::decode("id");
                    $exit = URL::decode("exit");
                    Event::register($id, $exit);
                    URL::redirect("../../enter.php?id=$id");
                break;
            }
        }else{
            throw new HTTPException("unsupported method", 405);
        }
    }catch(HTTPException $e) {
        throw $e;
        http_response_code($e->getCode());
        Logger::log("Access controll: Error in $access ".$e->getMessage()." (HTTP ".$e->getCode().")", null, LoggerLevel::ERROR);
        SESSION::start();
        $_SESSION["error"] = $e->getMessage();
        URL::redirect("../../");
    }catch(Exception $e) {
        //throw $e;
        $_SESSION["error"] = "Ha ocurrido un error interno";
        Logger::log("Access controll: Severe error in $access".$e->getMessage(), null, LoggerLevel::ERROR);
        //http_response_code(500);
        URL::redirect("../../");
    }
?>