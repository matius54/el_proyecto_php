<?php
    $base = $base ?? "../";
    require_once $base . "model/access.php";
    
    require_once $base . "libs/utils.php";
    require_once $base . "libs/db.php";
    require_once $base . "libs/bytes.php";
    
    class User {
        private static string $login_key = "login_user";

        private static array $default_user = [
            "user" => "Admin",
            "password" => "123456",
            "first_name" => "The Lord",
            "last_name" => "Administrator",
            "role_id" => 1
        ];

        private static array $all = [
            "pasword",
            "user",
            "first_name",
            "last_name",
            "ci",
            "birthday",
            "color"
        ];

        public static function initialize(){
            $db = DB::getInstance();
            if(!$db->getLastId("user")){
                //necesita que los accesos esten inicializados
                Access::initialize();
                //registrar el usuario por defecto
                self::register(self::$default_user, true);
            }
        }
        public static function register(array $data, bool $bypassVerify = false){

            //obtener el id del usuario logeado
            if(!$bypassVerify and !($user_id = self::verify())) return null;
            
            $filtered = [];
            //obtener toda la informacion del post
            foreach (self::$all as $value) {
                if(isset($data[$value])) $filtered[$value] = $data[$value];
            }
            //obtener la contrase;a y generar el hash y salt
            $password = $data["password"] ?? "";
            $hash = "";
            $salt = "";
            SC::password($password, $hash, $salt);

            //a;adirlos al filtered listo para insertar
            $filtered["hash"] = new Bytes($hash);
            $filtered["salt"] = new Bytes($salt);
            $filtered["private"] = SC::randomHexStr(16);
            $filtered["created_at"] = TIME::now();

            //insertar en la base de datos
            $db = DB::getInstance();
            if(!$db->insert("user", $filtered)) return null;
            $new_user_id = $db->getLastId("user");
            
            //si envias el rol = register + asignacion de rol
            if($role_id = $data["role_id"] ?? 0){
                //TODO asignarle el rol correspondiente al nuevo usuario

                //var_dump($user_id);
                //var_dump($new_user_id);
                //var_dump($role_id);
            }

            return $new_user_id;
        }

        public static function delete(int $id) : bool {
            return false;
        }
        public static function login(array $data) : int {
            self::initialize();
            User::login([]);
            $db = DB::getInstance();
            $username ["username"]??"";
            $salt = $db->select("user", ["salt"], "user = ?", [], return1: true);
            var_dump($salt);
            return 1;
        }
        public static function verify() : int {
            SESSION::start();
            return $_SESSION[self::$login_key] ?? 0;
        }
        public static function log(int $id){
            SESSION::start();
            if(!isset($_SESSION[self::$login_key]))
            $_SESSION[self::$login_key] = $id;
        }
    }
    User::login([]);
?>