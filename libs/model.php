<?php
    require_once "database.php";
    require_once "google2fa.php";
    class main{
        public static function Login($username,$totp) : void {
            if(Google2FA::verify_key(DB::getSecretFromUsername($username),$totp)){
                echo "totp verificado";
            }
        }
    }
?>