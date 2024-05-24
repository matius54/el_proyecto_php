<?php
    $base = $base ?? "../";
    require_once $base . "model/access.php";
    require_once $base . "libs/utils.php";
    require_once $base . "libs/db.php";
    

    class Report {
        private $reports = [];

        public function __construct() {
            $roles = Role::getAll_u();
            foreach($roles as $role){
                $id = $role["id"];
                $name = $role["name"];
                $this->reports["Reporte de $name"] = "SELECT u.id, u.user, u.ci, r.name, e.is_exit, e.created_at FROM `user` AS u JOIN `user_role` AS ru ON u.id = user_id JOIN `role` AS r ON ru.role_id = r.id JOIN `event` AS e ON u.id = e.user_id WHERE r.id = $id";
            }
        }

        public function getAll() : array {
            return array_keys($this->reports);
        }

        public function get(string $report) : array {
            if(!in_array($report, self::getAll())) return [];
            $db = DB::getInstance();
            $db->execute($this->reports[$report]);
            return $db->fetchAll(true);
        }
    }
?>