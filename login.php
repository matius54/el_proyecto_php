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
        <svg viewBox="0 0 1010 655" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMinYMin slice">
            <g clip-path="url(#clip0_1_15)">
                <rect x="127.022" y="-182" width="355.901" height="355.901" rx="43" transform="rotate(45.3109 127.022 -182)" fill="url(#paint0_linear_1_15)" />
                <rect x="-95" y="449.546" width="203.005" height="203.005" rx="20" transform="rotate(-45 -95 449.546)" fill="url(#paint1_linear_1_15)" />
                <rect x="832.497" y="588.4" width="381.976" height="91.0749" rx="45.5374" transform="rotate(135 832.497 588.4)" fill="url(#paint2_linear_1_15)" />
                <rect x="791" y="399.391" width="430.474" height="102.638" rx="51.3192" transform="rotate(-45 791 399.391)" fill="url(#paint3_linear_1_15)" />
                <path d="M133.212 64.7119C113.17 44.6705 113.17 12.177 133.212 -7.86437L365.027 -239.679C385.068 -259.72 417.562 -259.721 437.603 -239.679V-239.679C457.644 -219.638 457.644 -187.144 437.603 -167.103L205.788 64.7119C185.747 84.7532 153.253 84.7533 133.212 64.7119V64.7119Z" fill="url(#paint4_linear_1_15)" />
            </g>
            <defs>
                <linearGradient id="paint0_linear_1_15" x1="467.106" y1="-121.25" x2="113.552" y2="140.18" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#FA00FF" />
                    <stop offset="1" stop-color="#00F0FF" />
                </linearGradient>
                <linearGradient id="paint1_linear_1_15" x1="98.9828" y1="484.198" x2="-102.683" y2="633.317" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#FA00FF" />
                    <stop offset="1" stop-color="#00F0FF" />
                </linearGradient>
                <linearGradient id="paint2_linear_1_15" x1="830.547" y1="628.624" x2="1222.07" y2="633.016" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#FA00FF" />
                    <stop offset="1" stop-color="#00F0FF" />
                </linearGradient>
                <linearGradient id="paint3_linear_1_15" x1="788.802" y1="444.723" x2="1230.04" y2="449.673" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#FA00FF" />
                    <stop offset="1" stop-color="#00F0FF" />
                </linearGradient>
                <linearGradient id="paint4_linear_1_15" x1="103.5" y1="111" x2="419" y2="-197.5" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#FA00FF" />
                    <stop offset="1" stop-color="#00F0FF" />
                </linearGradient>
            </defs>
        </svg>
    </div>
    <?php
    require_once "libs/tablex.php";
    require_once "libs/database.php";
    require_once "libs/google2fa.php";
    /*
    $db = connectDB();
    $result = $db->query("select * from user");

    echo "<table>";
    foreach ($result as $asd => $value) {
        echo "<tr>";
        $asd += 1;
            echo "<th>".$asd.")</th>";
            for ($y = 0; $y < 7; $y++) {
                echo "<th>".$value[$y]."</th>";
            }
        echo "</tr>";
    }
    echo "</table><br><br>";
    */
    if (isset($_POST["user"]) && isset($_POST["key"])) {
        $token = DB::getSecretFromUsername($_POST["user"]);
        $key = $_POST["key"];
        if (!empty($token) && !empty($key) && Google2FA::verify_key($token, $key)) {
            echo "totp verificado";
        }
    }
    if(isset($_POST)){
        echo "Informacion recibida<br>{<br>";
        foreach($_POST as $k => $v){
            echo "$k : \"$v\"<br>";
        }
        echo "<br>}";
    }
    
    //$status = "ok";
    //$statusI18n = "LOGIN_STATUS_ALL_OK";
    //echo $_POST["admin"];
    //connectDB();
    //DB::getSecretFromUsername("Admin");

    ?>
    <div class="card login hidden">
        <h1 data-i18n="LOGIN_TITLE">LOGIN</h1>
        <div class="form">
            <?php if(isset($status))echo "<div class=\"status\" data-i18n=\"$statusI18n\">$status</div>"; ?>
            <form action="" id="form" method="post">
                <input minlength="1" maxlength="64" data-i18n="USERNAME_OR_CI" type="text" name="user" oninput="userHandler(this)" onblur="userHandler(this)" onfocus="removeError(this)" autocomplete="off"><br>
                <input minlength="6" maxlength="7" class="totp" type="text" placeholder="000-000" name="key" oninput="totpHandler(this)" onblur="totpHandler(this)" onfocus="removeError(this)" autocomplete="off"><br>
                <input minlength="16" maxlength="16" hidden data-i18n="TOTP_TOKEN" type="text" class="token" name="token" oninput="tokenHandler(this)" onfocus="removeError(this)" autocomplete="off"><br>
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
    const inputs = document.querySelectorAll("form input[type=text]");
    inputs.forEach(element => {
        element.addEventListener("input",event=>verify(event));
        element.addEventListener("blur",event=>verify(event));
        element.addEventListener("focus",event=>verify(event));
    });
    document.querySelector("form").addEventListener("submit",(e)=>{e.preventDefault()});
    const verify = (event) => {
        let element = event.target;
        let name = element.name;
        console.log(event);
    };
    const hiddenCheck = document.getElementsByName("isNotToken")[0];
    const loginButton = document.getElementById("loginButton");

    if(document.querySelector(".status"))document.querySelectorAll("input").forEach(element => addError(element));

    const addError = element => element.classList.add("error");
    const removeError = element => element.classList.remove("error");

    document.getElementById("form").addEventListener("submit", event => {
        event.preventDefault();
        verifyAndSubmit(event.target);
    });
    function togleForms(element){
        inputs.forEach(element => {
            element.toggleAttribute("hidden");
            element.value = "";
            removeError(element);
        });
        loginButton.toggleAttribute("hidden");
        formButton.setAttribute("data-i18n",hiddenCheck.checked ? "LOGIN_WITH_USER_CI" : "LOGIN_WITH_TOKEN");
        hiddenCheck.checked = !hiddenCheck.checked;
        i18n.updateDOM(element);
        //console.log(hiddenCheck.checked);
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