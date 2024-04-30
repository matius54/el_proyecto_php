<?php
    $base = $base ?? "../";  
    require_once $base . "libs/utils.php";
    require_once $base . "model/access.php";

    //a --> access
    try{
        Access::initialize();
        $access = URL::decode("a");
        if(URL::isGet()){
            switch ($access){
                case "getNodes":
                    JSON::sendJson(Node::getAll());
                break;
                case "getRole":
                    if($id = URL::decode("id")){
                        JSON::sendJson(Role::get($id));
                    }else{
                        JSON::sendJson(Role::getAll());
                    }
                break;
                default:
                    //var_dump(Access::test("login",1));
                    //var_dump(Access::purge());
                    var_dump(dirname(__DIR__));
                break;
            }
        }elseif(URL::isPost()){
            if(!$json = JSON::getJson()) throw new HTTPException("json was not provided", 422);
            switch($access){
                case "setRoleState":
                    http_response_code(Node::set($json) ? 200 : 304);
                break;
                case "newRole":
                    JSON::sendJson(["id" => Role::new($json)]);
                break;
                case "editRole":
                    http_response_code(Role::edit($json) ? 200 : 304);
                break;
                case "deleteRole":
                    $id = $json["id"] ?? null;
                    http_response_code(Role::delete($id) ? 200 : 304);
                break;
            }
        }else{
            throw new HTTPException("unsupported method", 405);
        }
    }catch(HTTPException $e){
        http_response_code($e->getCode());
        Logger::log("Access controll: Error in $access ".$e->getMessage()." (HTTP ".$e->getCode().")", null, LoggerLevel::ERROR);
        JSON::sendJson(["error"=>$e->getMessage()]);
    }catch(Exception $e){
        //throw $e;
        Logger::log("Access controll: Severe error in $access".$e->getMessage(), null, LoggerLevel::ERROR);
        http_response_code(500);
    }
?>