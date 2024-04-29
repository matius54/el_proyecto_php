<?php
    $base = $base ?? "./";
    require_once $base . "utils.php";
    require_once $base . "db.php";
    require_once $base . "access.php";
    require_once $base . "bytes.php";
    
    class User {
        private static string $login_key = "login_user";

        private static array $default_user = [
            "username" => "Admin",
            "password" => "123456",
            "first_name" => "The Lord",
            "last_name" => "Administrator"
        ];

        public static function initialize(){
            $db = DB::getInstance();
            if(!$db->getLastId("user")){
                //create new user
                //initialize access default
                //add admin access to user
            }
        }
        public static function register(array $data, bool $bypass_validation = false){
            $filtered = [];
            if($id = self::verify() or true){
                $password = $data["password"] ?? "";
                $hash = "";
                $salt = "";
                SC::password($password, $hash, $salt);
                $filtered["hash"] = new Bytes($hash);
                $filtered["salt"] = new Bytes($salt);
                $filtered["private"] = SC::randomHexStr(16);
                var_dump($filtered);
            }
            //$db = DB::getInstance();
            $all = [
                "private",
                "hash",
                "salt",
                "user",
                "first_name",
                "last_name",
                "ci",
                "birthday",
                "color",
                "created_at"
            ];
            array_merge();

            //$db->insert("user",["private"=>"","user"=>"","first_name"=>"","last_name"=>"","ci"=>"","birthday"=>"","color"=>"","created_at"=>""]);
        }
        public static function delete(int $id) : bool {
            return false;
        }
        public static function login(array $data){
            self::initialize();
        }
        public static function verify() : int {
            SESSION::start();
            return $_SESSION[self::$login_key] ?? 0;
        }
    }
    //User::initialize();
    //User::register([]);
?>