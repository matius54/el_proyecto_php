<?php
    require_once "db.php";
    class Access {
        private static string $access = "access.json";

        private static string $key = "k";
        private static string $name = "n";
        private static string $description = "d";

        private array $data;

        public function __construct() {
            $this->data = json_decode(
                json: file_get_contents(self::$access),
                associative: true
            );
        }
        public function getAllAccess() : array {
            return $this->data;
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
    class Role {
        private string $key;
        public function __construct(int $role_id) {
        }
    }
    class Node {
        public function __construct(string $key) {
            $sql = "SELECT ";
        
        }
    }
    $acc = new Access;
    var_dump($acc->getAllAccess());
?>