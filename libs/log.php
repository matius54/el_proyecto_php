<?php 
    require_once "db.php";
    require_once "user.php";
    require_once "utils.php";
    
    enum LoggerType: int {
        case ADD = 0;
        case DELETE = 1;
        case EDIT = 2;
    }

    enum LoggerLevel: int {
        case INFO = 0;
        case LOG = 1;
        case WARNING = 2;
        case ERROR = 3;
    }

    class Logger {
        static function log(
                string $action, 
                ?LoggerType $type = null, 
                LoggerLevel $level = LoggerLevel::LOG, 
                array $extra = []
            ) : void {
            $types = ["add","delete","edit"];
            $levels = ["info","log","warning","error"];
            $log = [];
            $user_id = User::verify();
            if($user_id) $log["user_id"] = $user_id;

            $log["ip"] = $_SERVER["REMOTE_ADDR"] ?? "ip not found";
            $log["agent"] = $_SERVER["HTTP_USER_AGENT"] ?? "agent not found";
            $log["uri"] = URL::URI();
            $log["action"] = $action;
            if($type !== null) $log["type"] = $types[$type->value];
            $log["level"] = $levels[$level->value];
            $log["extra"] = json_encode($extra);
            
            $db = DB::getInstance();
            $db->insert("log", $log);
        }
    }
    //Logger::log("test");
?>