<?php
    $base = $base ?? "../";  
    require_once $base . "model/log.php";
    require_once $base . "model/user.php";
    
    require_once $base . "libs/db.php";
    require_once $base . "libs/utils.php";
    require_once $base . "libs/paginator.php";

    class Node {
        private static string $filename = "config/nodes.json";

        private string $key;
        private string $name;
        private string $description;
        
        private array $all = [];

        public function __construct(string $key) {
            $this->all = self::getAll();
            $this->setKey($key);
        }

        public static function getAll() : array {
            global $base;
            return json_decode(
                json: file_get_contents($base . self::$filename) ?? "{}",
                associative: true
            ) ?? [];
        }

        public static function getByKey(string $key) : array {
            $all = self::getAll();
            return $all[$key];
        }

        public function get() : array {
            return [
                "key" => $this->key,
                "name" => $this->name,
                "description" => $this->description
            ];
        }

        public function getKey() : string {
            return $this->key;
        }

        public function getName() : string {
            return $this->name;
        }

        public function getDescription() : string {
            return $this->description;
        }

        public function setKey(string $key) : void {
            $node = $this->all[$key];
            $this->key = $key;
            $this->name = $node["name"];
            $this->description = $node["description"]; 
        }

        public static function isValid(string $key) : bool {
            return (bool) (self::getAll()[$key] ?? false);
        }

        public static function set(array $data) : bool {
            $id = $data["id"] ?? null;//int > 1
            if(!VALIDATE::id($id)) throw new HTTPException("id $id is not valid or was not provided", 422);
            if(!array_key_exists("key",$data)) throw new HTTPException("key was not provided", 422);
            if(!array_key_exists("value",$data)) throw new HTTPException("value was not provided", 422);
            
            $key = $data["key"]; //string
            if(!self::isValid($key)) throw new HTTPException("key $key is not valid", 422);

            $value = $data["value"]; //bool|null

            $db = DB::getInstance();
            if(is_bool($value)){
                $value_str = $value ? "true" : "false";
                if($db->select("role_node",condition: "role_id = ? AND node_key = ?", args: [$id, $key])){
                    //si el registro existe, se actualiza
                    if($result = $db->update("role_node",["allow"=>$value],"role_id = ? AND node_key = ?",[$id, $key])){
                        Logger::log("Role[$id]: Node['$key'] actualizado a '$value_str'", LoggerType::EDIT, LoggerLevel::LOG);
                    }else{
                        Logger::log("Role[$id]: Node['$key'] no actualizado a '$value_str', update error", LoggerType::EDIT, LoggerLevel::WARNING);
                    }
                    return $result;
                }
                //si no existe se crea
                if($result = $db->insert("role_node",["role_id" => $id, "node_key" => $key, "allow" => $value])){
                    Logger::log("Role[$id]: Node['$key'] actualizado a '$value_str'", LoggerType::ADD, LoggerLevel::LOG);
                }else{
                    Logger::log("Role[$id]: Node['$key'] no actualizado a '$value_str', insert error", LoggerType::ADD, LoggerLevel::WARNING);
                }
                return $result;
            }elseif($value === null){
                if($result = $db->delete("role_node","role_id = ? AND node_key = ?",[$id, $key])){
                    Logger::log("Role[$id]: Node['$key'] actualizado a 'unset'", LoggerType::DELETE, LoggerLevel::LOG);
                }else{
                    Logger::log("Role[$id]: Node['$key'] no actualizado a 'unset', not found", LoggerType::DELETE, LoggerLevel::WARNING);
                }
                return $result;
            }
            throw new HTTPException("value $value is not valid", 422);
        }

        public function __toString() : string {
            return json_encode($this->get());
        }
    }

    class Role {
        public function __construct(int $role_id) {
            /*
            $db = DB::getInstance();
            $result = $db->select(table: "role", condition: "id = ?", args: [$role_id]);
            */
        }

        public static function getAll() : array {
            $sql = "SELECT id, level, name, icon FROM role ORDER BY level ASC";
            $count = "SELECT COUNT(*) FROM role";
            $pag = new Paginator($sql, $count, itemsPerPage: 5, pageKey: "p");
            return $pag->toArray();
        }

        public static function getAllkv() : array {
            $db = DB::getInstance();
            $result = $db->select("role", ["id", "name"], all: true, htmlspecialchars: true);
            $kvList = [];
            foreach($result as $row){
                $kvList[$row["id"]] = $row["name"];
            }
            return $kvList;
        }

        public static function get($id) : array {
            if(!VALIDATE::id($id)) throw new HTTPException("id $id is not valid", 422);

            $db = DB::getInstance();
            $role = $db->select("role",["name","description","icon","level"],"id = ?",[$id], htmlspecialchars: true);

            if(!$role) throw new HTTPException("Role with id $id not found", 404);

            $nodes = $db->select("role_node",["node_key","allow"],"role_id = ?",[$id], all: true, htmlspecialchars: true);

            $sql = "SELECT user.id, user FROM user_role JOIN user ON user_id = user.id WHERE role_id = ?";
            $db->execute($sql,$id);
            $users = $db->fetchAll(htmlspecialchars: true);

            foreach($nodes as $key => $node){
                $nodes[$node["node_key"]] = VALIDATE::int2bool($node["allow"]);
                unset($nodes[$key]);
            }

            return [
                "id" => $id,
                ...$role,
                "nodes" => $nodes,
                "users" => $users
            ];
        }
        public static function new(array $data) : int {
            $values = ["name","description","level","icon"];
            $filtered = [];

            foreach ($values as $value) {
                if(!isset($data[$value]))
                throw new HTTPException("$value is not valid", 422);
                $exValue = $data[$value];
                if($value === "level")if(!VALIDATE::float($exValue))
                throw new HTTPException("$value $exValue is not valid", 422);
                $filtered[$value] = $exValue;
            }

            $db = DB::getInstance();
            if($db->insert("role",$filtered)){
                $newId = $db->getLastId("role");
                Logger::log("Role: nuevo rol '".$filtered["name"]."' con id '$newId'", LoggerType::ADD, LoggerLevel::LOG);
                return $newId;
            }
            throw new HTTPException("nothing was saved to database", 500);
        }
        public static function edit(array $data) : bool {
            $values = ["name","description","level","icon"];
            $filtered = [];
            
            $id = $data["id"] ?? null;//int > 1
            if(!VALIDATE::id($id)) throw new HTTPException("id $id is not valid or was not provided", 422);
            
            foreach ($values as $value) {
                if(!isset($data[$value]))
                throw new HTTPException("$value is not valid", 422);
                $exValue = $data[$value];
                if($value === "level")if(!VALIDATE::float($exValue))
                throw new HTTPException("$value $exValue is not valid", 422);;
                $filtered[$value] = $exValue;
            }

            $db = DB::getInstance();
            $update = $db->update("role", $filtered, "id = ?", [$id]);
            Logger::log("Role[$id]: rol ".($update? "" : "no ")."modificado ".json_encode($filtered, JSON_UNESCAPED_UNICODE), LoggerType::EDIT, LoggerLevel::LOG);
            return $update;
        }
        public static function delete($id) : bool {
            if(!VALIDATE::id($id)) throw new HTTPException("id $id is not valid", 422);
            $db = DB::getInstance();
            $role_name = $db->select("role",["name"],"id = ?",[$id]);
            $db->beginTransaction();
            $db->delete("role_node","role_id = ?",[$id]);
            $db->delete("user_role","role_id = ?",[$id]);
            $deleted = $db->delete("role","id = ?",[$id]);
            if($deleted){
                $db->commit();
                Logger::log("Role[$id]: rol '$role_name' eliminado", LoggerType::EDIT, LoggerLevel::LOG);
            }else{
                $db->rollback();
            }
            return $deleted;
        }
    }

    class Access {

        public function __construct() {
            
        }

        public static function test(string $key, int $user_id, int $role_id = 0) : bool {
            //para obtener si el permiso es valido necesitas tener el (key del permiso, y el usuario)
            //-> obtiene todos los roles asignados a ese usuario,
            //itera sobre todos los nodos de los roles usando el key especificado en el orden de los niveles

            //si no haz iniciado sesion o el id del usuario es invalido todos los permisos se evaluaran como false

            if(!$user_id and !$role_id) return false;

            //si el key no se encuentra en la lista de nodes.json no procede
            if(!Node::isValid($key)) return false;
            //puede que este en la base de datos pero sino f.
            
            $db = DB::getInstance();
            if($role_id){
                //si el role id es suministrado se salta el paso de obtenerlo a travez del user_id
                $roles = [["id" => $role_id]];
            }else{
                $sql = "SELECT r.id FROM user_role JOIN role AS r ON role_id = r.id WHERE user_id = ? ORDER BY level DESC";
                $db->execute($sql, $user_id);
                $roles = $db->fetchAll();
            }
            //por defecto si no tiene ningun permiso este sera negado
            $access = false;
            foreach ($roles as $value) {
                $role = $value["id"];
                $response = $db->select("role_node",["allow"],"role_id = ? AND node_key = ?",[$role, $key]);
                if(isset($response["allow"])){
                    $access = $response["allow"];
                }
            }
            return $access;
        }

        //usar esta funcion para eliminar todos los key que no existen en
        //nodes.json pero si existen en la base de datos
        //en otras palabras, "purgar" los nodos sin uso
        //devuelve el numero de registros afectados
        public static function purge() : int {
            Logger::log("--- Purge access begins ---",LoggerType::DELETE,LoggerLevel::WARNING);
            $allNodes = array_keys(Node::getAll());
            $db = DB::getInstance();
            $sql = "SELECT DISTINCT `node_key` FROM role_node";
            $db->execute($sql);
            $nodeKeys = $db->fetchAll();
            $remove = [];
            foreach ($nodeKeys as $value) {
                $node_key = $value["node_key"];
                if(!in_array($node_key, $allNodes, true)) $remove[] = $node_key;
            }
            $condition = implode(" OR ",array_fill(0, sizeof($remove), "`node_key` = ?"));
            Logger::log("nothing to purge",LoggerType::DELETE,LoggerLevel::LOG);
            Logger::log("--- Purge access ends ---",LoggerType::DELETE,LoggerLevel::WARNING);
            return $remove ? $db->delete("role_node", $condition, $remove) : 0;
        }

        public static function initialize() : void {
            $db = DB::getInstance();
            //si existe algun rol en la base de datos, termina
            if($db->getLastId("role")) return;
            Logger::log("--- Initialize access begins ---",LoggerType::ADD,LoggerLevel::WARNING);

            global $base;
            $defaultRoles = json_decode(
                json: file_get_contents($base . "config/default_roles.json") ?? "{}",
                associative: true
            ) ?? [];
            
            foreach ($defaultRoles as $value) {
                //inicializa rol
                $role_id = Role::new([
                    "name" => $value["name"],
                    "description" => $value["description"],
                    "level" => $value["level"],
                    "icon" => $value["icon"]
                ]);

                //le da acceso a todos los nodos configurados
                foreach ($value["nodes"] ?? [] as $key => $value) {
                    Node::set([
                        "id" => $role_id,
                        "key" => $key,
                        "value" => $value
                    ]);
                }
            }
            
            
            Logger::log("--- Initialize access ends ---",LoggerType::ADD,LoggerLevel::WARNING);
        }
        
        public static function setRole(int $role_id, int $user_id = 0){
            $curr_user_id = User::verify();
            if(!$curr_user_id and !$user_id) return;
            if(!$user_id) $user_id = $curr_user_id;
            if($user_id === $curr_user_id){
                //
            }else{
                //
            }
            $db = DB::getInstance();
            $db->insert("user_role",
                [
                    "user_id" => $user_id,
                    "role_id" => $role_id
                ]
            );
        }

        public static function getInputsForm(int $role_id) : array {
            $allNodes = array_keys(Node::getAll());
            $allNodes = array_filter($allNodes, function ($value) {return str_starts_with($value, "form.");});
            $allNodes = array_values($allNodes);
            $nodeAccess = [];
            foreach ($allNodes as $node) {
                $access = self::test($node, 0, $role_id);
                $values = explode(".", $node);
                unset($values[0]);
                $values = array_values($values);
                $node = implode(".",$values);
                $nodeAccess[$node] = $access;
            }
            return $nodeAccess;
        }
    }
?>