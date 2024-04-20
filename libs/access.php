<?php
    require_once "db.php";
    require_once "utils.php";
    require_once "paginator.php";

    class Node {
        
        private static string $filename = "nodes.json";

        private string $key;
        private string $name;
        private string $description;
        
        private array $all = [];

        public function __construct(string $key) {
            $this->all = self::getAll();
            $this->setKey($key);
        }

        public static function getAll() : array {
            return json_decode(
                json: file_get_contents(self::$filename) ?? "{}",
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
                if($db->select("role_node",condition: "role_id = ? AND node_key = ?", args: [$id, $key])){
                    //si el registro existe, se actualiza
                    return $db->update("role_node",["allow"=>$value],"role_id = ? AND node_key = ?",[$id, $key]);
                }
                //si no existe se crea
                return $db->insert("role_node",["role_id" => $id, "node_key" => $key, "allow" => $value]);
            }elseif($value === null){
                return $db->delete("role_node","role_id = ? AND node_key = ?",[$id, $key]);
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
            $pag = new Paginator($sql, itemsPerPage: 5, pageKey: "p");
            return $pag->toArray();
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
                return $db->getLastId("role");
            }
            throw new HTTPException("saving to database", 500);
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
            return $db->update("role", $filtered, "id = ?", [$id]);
        }
        public static function delete($id) : bool {
            if(!VALIDATE::id($id)) throw new HTTPException("id $id is not valid", 422);
            $db = DB::getInstance();
            $db->beginTransaction();
            $db->delete("role_node","role_id = ?",[$id]);
            $db->delete("user_role","role_id = ?",[$id]);
            $deleted = $db->delete("role","id = ?",[$id]);
            if($deleted){
                $db->commit();
            }else{
                $db->rollback();
            }
            return $deleted;
        }
    }

    class Access {

        public function __construct() {
            
        }

        public static function test(string $key, int $user_id) : bool {
            //para obtener si el permiso es valido necesitas tener el (key del permiso, y el usuario)
            //-> obtiene todos los roles asignados a ese usuario,
            //itera sobre todos los nodos de los roles usando el key especificado en el orden de los niveles

            //si el key no se encuentra en la lista de nodes.json no procede
            if(!Node::isValid($key)) return false;
            //puede que este en la base de datos pero sino f.
            
            $db = DB::getInstance();
            $sql = "SELECT r.id FROM user_role JOIN role AS r ON role_id = r.id WHERE user_id = ? ORDER BY level ASC";
            $db->execute($sql, $user_id);
            $roles = $db->fetchAll();
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
            return $remove ? $db->delete("role_node", $condition, $remove) : 0;
        }
    }

    //a --> access
    try{
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
        JSON::sendJson(["error"=>$e->getMessage()]);
    }catch(Exception $e){
        //throw $e;
        http_response_code(500);
    }
?>