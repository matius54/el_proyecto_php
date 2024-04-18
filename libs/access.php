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

        public static function set(array $data) : bool {
            $id = $data["id"] ?? null;//int > 1
            if(!VALIDATE::id($id)) throw new HTTPException("id $id is not valid or was not provided", 422);
            if(!array_key_exists("key",$data)) throw new HTTPException("key was not provided", 422);
            if(!array_key_exists("value",$data)) throw new HTTPException("value was not provided", 422);
            
            $key = $data["key"]; //string
            if(!VALIDATE::string($key) or !(Node::getAll()[$key] ?? null))
                throw new HTTPException("key $key is not valid", 422);

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
            $sql = "SELECT id, level, name, icon FROM role ORDER BY level DESC";
            $pag = new Paginator($sql, itemsPerPage: 5, pageKey: "p");
            return $pag->toArray();
        }

        public static function get($id) : array {
            if(!VALIDATE::id($id)) throw new HTTPException("id $id is not valid", 422);

            $db = DB::getInstance();
            $role = $db->select("role",["name","description","icon"],"id = ?",[$id], htmlspecialchars: true);

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
    }

    class Access {

        public function __construct() {
            
        }

        public function TestNode(string $key, int $user_id) : void {
            //para obtener si el permiso es valido necesitas tener el (key del permiso, y el usuario)
            //-> obtiene todos los roles asignados a ese usuario,
            //itera sobre todos los nodos de los roles usando el key especificado en el orden de los niveles
            //$db = DB::getInstance();
        }

        public function hasAccess(string $role ,string $key) : bool|null {
            //buscar una key especifica en la base de datos
            return null;
        }
    }

    //a --> access
    try{
        switch (URL::decode("a")){
            case "getRole":
                if(URL::isGet()){
                    if($id = URL::decode("id")){
                        JSON::sendJson(Role::get($id));
                    }else{
                        JSON::sendJson(Role::getAll());
                    }
                }
            break;
            case "getNodes":
                if(URL::isGet()) JSON::sendJson(Node::getAll());
            break;
            case "setRole":
                if(!URL::isPost()){
                    throw new HTTPException("unsupported method, you should use POST", 405);
                }elseif($json = JSON::getJson()){
                    http_response_code(Node::set($json) ? 200 : 304);
                }else{
                    throw new HTTPException("json was not provided", 422);
                }
            break;
            default:
                //var_dump(Node::set(["id"=>1,"key"=>"login","value"=>null]));
                //do nothing
            break;
        }
    }catch(HTTPException $e){
        http_response_code($e->getCode());
        JSON::sendJson(["error"=>$e->getMessage()]);
    }catch(Exception $e){
        http_response_code(500);
    }
?>