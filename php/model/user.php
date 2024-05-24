<?php
    $base = $base ?? "../";
    require_once $base . "model/access.php";
    
    require_once $base . "libs/utils.php";
    require_once $base . "libs/db.php";
    require_once $base . "libs/bytes.php";
    require_once $base . "libs/paginator.php";
    
    class User {
        private static string $select_user = "SELECT `id`, `user`, `ci`, `first_name`, `last_name`, `birthday` FROM `user`";
        private static string $login_key = "login_user";

        private static array $default_user = [
            "user" => "admin",
            "password" => "12345678",
            "first_name" => "The Lord",
            "last_name" => "Administrator",
            "role_id" => 1
        ];

        private static array $all = [
            "user",
            "first_name",
            "last_name",
            "ci",
            "birthday",
            "color",
            "address"
        ];

        public static function initialize(){
            $db = DB::getInstance();
            if(!$db->getLastId("user")){
                self::logout();
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
            //si esta $bypassVerify se autoasigna password2
            if($bypassVerify)$data["password2"] = $password;

            if($password !== $data["password2"] ?? "") throw new HTTPException("Las contrase;as no coinciden", 401);
            $hash = "";
            $salt = "";
            SC::password($password, $hash, $salt);


            $ci = ($data["ci-type"] ?? "V") . "-" . ($data["ci"] ?? "0");
            if(VALIDATE::ci($ci)){
                $filtered["ci"] = $ci;
            }else{
                if(!$bypassVerify) throw new HTTPException("Cedula '$ci' invalida", 309);
            }

            //a;adirlos al filtered listo para insertar
            if($password){
                $filtered["hash"] = new Bytes($hash);
                $filtered["salt"] = new Bytes($salt);
            }
            $filtered["private"] = SC::randomHexStr(16);
            $filtered["created_at"] = TIME::now();


            if(!($filtered["user"] ?? "")) $filtered["user"] = $filtered["first_name"];
            //insertar en la base de datos
            $db = DB::getInstance();
            if(!$db->insert("user", $filtered)) return null;
            $new_user_id = $db->getLastId("user");
            
            //si envias el rol = register + asignacion de rol
            if($role_id = $data["role_id"] ?? 0){
                //TODO asignarle el rol correspondiente al nuevo usuario
                Access::setRole($role_id, $new_user_id);
            }
            return $new_user_id;
        }

        public static function delete(int $id) : bool {
            return false;
        }

        public static function login(array $data) : int|null {
            //inicializar user
            self::initialize();

            if(self::verify()) throw new HTTPException("Ya has iniciado sesion", 401);

            //obtener los datos del $_POST
            $username = $data["username"] ?? "";
            $password = $data["password"] ?? "";
            $hash = null;
            
            $db = DB::getInstance();
            $response = $db->select("user", ["id", "salt"], "`user` = BINARY ?",[$username]);
            if(!$response) throw new HTTPException("Usuario no encontrado", 401);
            list($user_id, $salt) = array_values($response);
            if(!Access::test("user.login", $user_id)) throw new HTTPException("No tienes acceso", 401);
            SC::password($password, $hash, $salt);
            $db->execute("SELECT `hash` = ? AS `v` FROM `user` WHERE `id` = ?", [new Bytes($hash), $user_id]);

            if($db->fetch()["v"] ?? null){
                //esta validado
                self::log($user_id);
                return $user_id;
            }
            throw new HTTPException("Contrasena incorrecta", 401);
            return null;
        }

        public static function verify() : int {
            SESSION::start();
            $user_id = $_SESSION[self::$login_key] ?? 0;
            $db = DB::getInstance();
            $validation = $db->select("user",[],"`id` = ?",[$user_id], return1: true);
            return $validation ? $user_id : 0;
        }

        public static function log(int $id){
            SESSION::start();
            if(!isset($_SESSION[self::$login_key]))
            $_SESSION[self::$login_key] = $id;
        }

        public static function logout(){
            SESSION::start();
            unset($_SESSION[self::$login_key]);
        }

        public static function get(int $id) : array {
            if(!VALIDATE::id($id)) return [];
            $db = DB::getInstance();
            $db->execute(self::$select_user . " WHERE `id` = ?", $id);
            $user = $db->fetch(true);
            if(!$user) return [];
            $db->execute("SELECT r.id, r.name FROM user_role AS ur JOIN role AS r ON role_id = r.id WHERE user_id = ? ORDER BY r.level DESC ", $id);
            $roles = $db->fetchAll(true);
            //$roles = array_map(function ($e){return $e["name"] ?? "";}, $roles);
            return [...$user, "roles" => $roles];
        }

        public static function getAll() : Paginator {
            $sql = self::$select_user;
            $count = "SELECT COUNT(*) FROM `user`";
            return new Paginator($sql, $count);
        }

        public static function search(string $string) : Paginator{
            //campos en los cuales se buscaran coincidencias
            $search = ["user","first_name","last_name","birthday","ci","color","private"];

            $search_instr = array_map(function ($v) {return "INSTR(`$v`,?) > 0 ";}
            , $search);
            $search_like = array_map(function ($v) {return "`$v` LIKE ?";}, $search);
            $sql = " WHERE " . implode(" OR ", array_merge($search_instr, $search_like));
            $args = array_fill(0, sizeof($search) * 2, $string);
            $count = "SELECT COUNT(*) FROM `user`$sql";
            var_dump($sql);
            return new Paginator(self::$select_user . $sql, $count, $args);
        }
    }
    //var_dump(User::login(["username"=>"Admin","password"=>"123456"]));
?>