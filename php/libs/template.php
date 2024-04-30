<?php
    class Template {
        public static function render(string $template_file_name, array $template_file_data = []) : void {
            extract($template_file_data, EXTR_SKIP);
            include "components/$template_file_name.php";
        }
    }
?>