<?php
    $base = $base ?? "../";
    require_once $base . "libs/utils.php";
    require_once $base . "libs/db.php";
    require_once $base . "libs/paginator.php";

    class Report {
        static private array $reports = [
            "test (roles)" => "SELECT * FROM role"
        ];

        public static function getAll() : array {
            return array_keys(self::$reports);
        }

        public static function get(string $report) : array {
            if(!in_array($report, self::getAll())) return [];
            $db = DB::getInstance();
            $db->execute(self::$reports[$report]);
            return $db->fetchAll(true);
        }
    }
?>