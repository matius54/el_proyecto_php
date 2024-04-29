<?php
    $base = $base ?? "../";
    require_once $base . "libs/utils.php";
    
    class Event {

    }
    class Chronogram {
        public static function getCalendar(int $month, int $year) : array {
            $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $firstDay = date('Y-m-01', mktime(0, 0, 0, $month, 1, $year));
            $startDay = date('w', strtotime($firstDay));

            return ["days" => $days, "start" => intval($startDay)];
        }
    }
    //var_dump(Chronogram::getCalendar(3, 2024));
?>