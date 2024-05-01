<?php 
    $base = $base ?? "../";
    require_once $base . "model/user.php";
    
    require_once $base . "libs/db.php";
    require_once $base . "libs/paginator.php";
    require_once $base . "libs/utils.php";
    
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
        public static array $types = ["add","delete","edit"];
        public static array $levels = ["info","log","warning","error"];

        public static function log(
                string $action, 
                ?LoggerType $type = null, 
                LoggerLevel $level = LoggerLevel::LOG, 
                array $extra = []
            ) : void {
            $log = [];
            $user_id = User::verify();
            if($user_id) $log["user_id"] = $user_id;

            $log["ip"] = $_SERVER["REMOTE_ADDR"] ?? "ip not found";
            $log["agent"] = $_SERVER["HTTP_USER_AGENT"] ?? "agent not found";
            $log["uri"] = URL::URI();
            $log["action"] = $action;
            if($type !== null) $log["type"] = self::$types[$type->value];
            $log["level"] = self::$levels[$level->value];
            $log["extra"] = json_encode($extra);
            $log["created_at"] = TIME::now();
            
            $db = DB::getInstance();
            $db->insert("log", $log);
            /*
            global $base;
            $dir = str_replace("/", DIRECTORY_SEPARATOR, $base) . $levels[$level->value] . ".txt";
            $handle = fopen($dir, "a");
            fwrite($handle, gmdate(DateTime::ATOM, time()).": ".json_encode($log, JSON_UNESCAPED_UNICODE)."\n");
            fclose($handle);
            */
        }
        public static function getAll(string $level, string $type) : Paginator {
            $filter = [];
            if(in_array($level, self::$levels)) array_push($filter, "`level` = \"$level\"");
            if(in_array($type, self::$types)) array_push($filter, "`type` = \"$type\"");
            return new Paginator(
                "SELECT `l`.`id`, `user`, `action`, `level`, `type` FROM `log` as `l` LEFT JOIN `user` as `u` ON `user_id` = `u`.`id`".($filter ? " WHERE ".implode(" AND ", $filter) : "")." ORDER BY `l`.`id` DESC",
                itemsPerPage: 25
            );
        }
    }
    
    //Logger::log("test");
?>