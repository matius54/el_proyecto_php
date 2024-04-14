<?php
    require_once "db.php";
    require_once "utils.php";

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

        public function __toString() : string {
            return json_encode($this->get());
        }
    }

    class Role {
        public function __construct(int $role_id) {
            $db = DB::getInstance();
            $result = $db->select(table: "role", condition: "id = ?", args: [$role_id]);
        }

        public static function getAll(){

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

    //var_dump(Node::getAll());
    $db = DB::getInstance();
    //var_dump($db->update(table: "user", data: ["username"=>"XD"], condition: "id = ?", args: [4]));
    /*
    var_dump(
    $db->insert("user",[
        "username" => SC::randomHexStr(),
        "hash" => new Bytes(SC::randomByteStr(32)),
        "salt" => new Bytes(SC::randomByteStr(16)),
        "email" => SC::randomHexStr(),
        "created_at" => 4512323
    ]));
    */
?>