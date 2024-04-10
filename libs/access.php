<?php
    require_once "db.php";

    class Access {

        public function __construct() {
            
        }

        public function toHtml() : string {
            return "";
        }

        public function unsetRoleKey(string $role ,string $key) : void {

        }

        public function hasAccess(string $role ,string $key) : bool|null {
            //buscar una key especifica en la base de datos
            return null;
        }
    }

    class Node {
        private static string $filename = "nodes.json";

        private ?string $key = null;
        private ?string $name = null;
        private ?string $description = null;

        private array $all = [];

        public function __construct(string $key = "") {
            $this->all = self::getAll();
            $this->setKey($key);
        }

        public static function getAll() : array {
            return json_decode(
                json: file_get_contents(self::$filename),
                associative: true
            ) ?? [];
        }

        public function get() : array|null {
            if(isset($this->key)){
                return [
                    "key" => $this->key,
                    "name" => $this->name,
                    "description" => $this->description
                ];
            }
            return null;
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

        public function setKey(string $key = "") : void {
            if($node = $this->all[$key] ?? ""){
                $this->key = $key;
                $this->name = $node["name"];
                $this->description = $node["description"]; 
            }
        }
    }

    class Role {
        public function __construct(int $role_id) {
            $sql = "SELECT ";
        }

        public static function getAll(){

        }
    }

    var_dump(Node::getAll());
?>