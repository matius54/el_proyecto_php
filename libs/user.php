<?php
    require_once "utils.php";
    require_once "db.php";
    require_once "access.php";
    require_once "bytes.php";

    class User {
        static string $login_key = "login_user";

        static array $default_user = [
            "username" => "Admin",
            "password" => "123456",
            "first_name" => "The Lord",
            "last_name" => "Administrator"
        ];

        static function initialize(){
            $db = DB::getInstance();
            if(!$db->getLastId("user")){
                //create new user
                //initialize access default
                //add admin access to user
            }
        }
        static function register(array $data, bool $bypass_validation = false){
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
        static function login(array $data){
            self::initialize();
        }
        static function verify() : int {
            SESSION::start();
            return $_SESSION[self::$login_key] ?? 0;
        }
    }
    //User::initialize();
    //User::register([]);
?>