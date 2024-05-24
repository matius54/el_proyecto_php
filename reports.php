<?php
    $title = "Reportes";

    $base = "php/";
    require_once "php/model/user.php";
    require_once "php/model/reports.php";
    require_once "php/libs/template.php";

    Template::render("header",["title" => $title]);
    $report = URL::decode("report") ?? "";
    if($report){
        Template::render("nav",["title" => $title, "href" => "./reports.php"]);
        $rep = Report::get($report);
        echo HTML::matrix2table($rep);
    }else{
        Template::render("nav",["title" => $title]);
        $reports = Report::getAll();
        $reports = array_map(function ($r){return ["href" => "./reports.php?report=$r", "name" => $r];}, $reports);
        Template::render("card", [
            "title" => "Reportes",
            "items" => $reports
        ]);
    }
    //var_dump(User::verify());
    Template::render("footer");
?>