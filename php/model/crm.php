<?php
    $base = $base ?? "../";  
    require_once $base . "model/access.php";
    
    require_once $base . "libs/db.php";
    require_once $base . "libs/utils.php";

    class Event {
        public static function isOut(int|string $user_id) : bool {
            if(!VALIDATE::id($user_id)) return false;
            $db = DB::getInstance();
            $sql = "SELECT `is_exit` FROM `event` WHERE `user_id` = ? ORDER BY `id` DESC LIMIT 1";
            $db->execute($sql, $user_id);
            return $db->fetch()["is_exit"] ?? true;
        }
        public static function register(int|string $user_id, bool $exit = false) : bool {
            if(self::isOut($user_id) and $exit) throw new HTTPException("Ya esta fuera", 401);
            if(!self::isOut($user_id) and !$exit and self::access($user_id)) throw new HTTPException("Ya esta dentro", 401);
            if($exit and !self::access($user_id)) throw new HTTPException("No tiene acceso para salir", 401);
            if(!VALIDATE::id($user_id)) return false;
            $db = DB::getInstance();
            return $db->insert("event", ["created_at" => TIME::now(), "user_id" => $user_id, "is_exit" => $exit]);
        }
        public static function access($user_id) : bool {
            return Access::test("crm.exit", $user_id);
        }
    }
?>