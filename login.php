<!DOCTYPE html>
<html data-i18n="HTML_LANG" lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-i18n="LOGIN_PAGE_TITLE">Login</title>
    <link rel="stylesheet" href="style.css">
    <script src="./frontend_libs/i18n.js"></script>
    <script src="./frontend_libs/theme.js"></script>
    <script defer>
        const i18n = new I18n(() => {
            //esto se ejecutara despues de terminar de cargar el idioma
            document.querySelector(".card").classList.remove("hidden");
        });
    </script>
</head>

<body class="login-page">
    <div class="svg-container">
    <?php
    require_once "libs/tablex.php";
    require_once "libs/database.php";
    require_once "libs/google2fa.php";

    if (isset($_POST["user"]) && isset($_POST["key"])) {
        $token = DB::getSecretFromUsername($_POST["user"]);
        $key = $_POST["key"];
        if (!empty($token) && !empty($key) && Google2FA::verify_key($token, $key)) {
            echo "totp verificado";
        }
    }
    if(isset($_POST)){
        echo "Informacion recibida por PHP<br>{<br>";
        foreach($_POST as $k => $v){
            echo "$k : \"$v\"<br>";
        }
        echo "<br>}";
    }


    ?>
    <div class="card login hidden">
        <h1 data-i18n="LOGIN_TITLE">LOGIN</h1>
        <div class="form">
            <?php if(isset($status))echo "<div class=\"status\" data-i18n=\"$statusI18n\">$status</div>"; ?>
            <form action="" id="form" method="post" autocomplete="off">
                <input minlength="1" maxlength="64" data-i18n="USERNAME_OR_CI" type="text" name="user"><br>
                <input minlength="6" maxlength="7" class="totp" type="text" placeholder="000-000" name="key"><br>
                <input minlength="16" maxlength="16" hidden data-i18n="TOTP_TOKEN" type="text" class="token" name="token"><br>
                <input hidden type="checkbox" name="isNotToken">
                <button hidden id="loginButton" data-i18n="LOGIN_SUBMIT">Login</button><br>
                <label data-i18n="LOGIN_AS_ADMINISTRATOR">¿Iniciar sesion como administrador?<input type="checkbox" name="admin"></label>
            </form>
        </div>
        <div class="options">
            <button onclick="togleForms(this)" data-i18n="LOGIN_WITH_TOKEN" id="formButton">Token</button>
            <button onclick="showQrReader()" data-i18n="SCAN_QR">Escanear QR</button>
        </div>
    </div>
</body>
<script>
    const user = document.getElementsByName("user")[0];
    const key = document.getElementsByName("key")[0];
    const token = document.getElementsByName("token")[0];
    const hiddenCheck = document.getElementsByName("isNotToken")[0];
    const formButton = document.getElementById("formButton");
    const loginButton = document.getElementById("loginButton");
    const currentForm = document.getElementById("form");
    var isTotpToken = true;
    hiddenCheck.checked = !isTotpToken;
    if(document.querySelector(".status"))document.querySelectorAll("input").forEach(element => addError(element));

    const addError = element => element.classList.add("error");
    const removeError = element => {
        let status = document.querySelector(".status");
        if(status)status.setAttribute("hidden","hidden");
        element.classList.remove("error");
    };
    document.getElementById("form").addEventListener("submit", event => {
        event.preventDefault();
        verifyAndSubmit(event.target);
    });
    const i18n_Magic = {
        true:"LOGIN_WITH_USER_CI",
        false:"LOGIN_WITH_TOKEN"
    }
    function togleForms(element){
        if(isTotpToken){
            hiddenCheck.checked = false;
            user.setAttribute("hidden","hidden");
            key.setAttribute("hidden","hidden");
            user.value = "";
            key.value = "";
            removeError(user);
            removeError(key);
            loginButton.removeAttribute("hidden");
            token.removeAttribute("hidden");
        }else{
            hiddenCheck.checked = true;
            loginButton.setAttribute("hidden","hidden");
            token.setAttribute("hidden","hidden");
            token.value = "";
            removeError(key);
            user.removeAttribute("hidden");
            key.removeAttribute("hidden");
        }
        formButton.setAttribute("data-i18n",i18n_Magic[isTotpToken]);
        isTotpToken = !isTotpToken;
        i18n.updateDOM(element);
    }
    function showQrReader(){
        alert("no implementado aun");
    }
    function verifyAndSubmit(element){
        console.log("a");

        if(token.validated)element.submit();
    }
    key.addEventListener("input",event=>{
        if(event.inputType.startsWith("delete")){
            event.target.value = "";
            totpHandler(event.target);
        }
    });
    function userHandler(element){
        let value = element.value;
        if(value===""){
            addError(element);
        }else{
            removeError(element);
        }
    }
    function totpHandler(element){
        let value = element.value;
        let dividerIndex = 3;
        if(value.length >= dividerIndex && value[3]!== "-"){
            element.value = `${value}-`
        }
        value = value.slice(0, dividerIndex) + value.slice(dividerIndex+1);
        if(/^\d+$/.test(value) && value!==""){
            removeError(element)
            if(value.length===6 && user.value!==""){
                element.value = value;
                currentForm.submit();
            }
        }else{
            addError(element)
        }
    }
    function tokenHandler(element){
        element.value =  element.value.toUpperCase();
        let value = element.value;
        if(value.length === 16 && /^([A-Z2-7]{16})/.test(value)){
            removeError(element);
            element.validated = true;
        }else{
            element.validated = false;
            addError(element);
        }
        
    }
    togleForms();
    togleForms();
    <?php if(!isset($_POST["isNotToken"]))echo "\ntogleForms();"; ?>
</script>
</html>