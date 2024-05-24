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
                case "setRole":
                    $role_id = URL::decode("role_id") ?? "0";
                    $user_id = URL::decode("user_id") ?? "0";
                    $set = URL::decode("set") ?? "0";
                    Access::setRole(intval($role_id), intval($user_id), $set);
                    URL::redirect("../../userRole.php?id=$user_id");
                break;
                default:
                    //var_dump(Access::test("login",1));
                    //var_dump(Access::purge());
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
                case "getInputsForm":
                    $id = $json["id"] ?? 1;
                    JSON::sendJson(Access::getInputsForm($id));
                break;
            }
        }else{
            throw new HTTPException("unsupported method", 405);
        }
    }catch(HTTPException $e){
        http_response_code($e->getCode());
        SESSION::start();
        $_SESSION["error"] = $e->getMessage();
        Logger::log("Access controll: Error in $access ".$e->getMessage()." (HTTP ".$e->getCode().")", null, LoggerLevel::ERROR);
        URL::redirect("../../");
    }catch(Exception $e){
        //throw $e;
        SESSION::start();
        $_SESSION["error"] = $e->getMessage();
        Logger::log("Access controll: Severe error in $access".$e->getMessage(), null, LoggerLevel::ERROR);
        URL::redirect("../../");
    }
?>