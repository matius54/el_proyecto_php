class Auth {
    //la sesion actual se guardara en la cookie y en localstorage
    //las demas sesiones se guardaran en localstorage
    //en session storage se guardara un booleano que cerrara las cuentas temporales al inicio de la sesion
    storageKey = "_Auth";
    cookieKey = "__session";
    //in the future 3 months <*30> --> 90 days <*24> --> 2160 hours <*60> --> 129600 minutes <*60> --> 7776000 seconds <*1000> --> 7776000000 miliseconds (unix)
    expirationUnixTime = 7776000000;
    //debe tener foto de perfil, nombre de usuario y fecha de nacimiento
    constructor(){
        //aqui se cargara la informacion de localstorage sessionstorage y cookie
        let active = sessionStorage.getItem(`${this.storageKey}_active`);
        if(active!=="active"){
            //aqui se inicia la sesion por primera vez

            sessionStorage.setItem(`${this.storageKey}_active`,"active");
        }
        //document.cookie
    }
    login(PHPobj){
        //este metodo solo puede recibir un objeto desde php directamente iniciando sesion
    }
    logout(index){
        //debes enviar el indice en el cual se encuentra la cuenta y el metodo cerrara la sesion por ti
    }
    changeAccount(index){
        //debes enviar el indice en el cual se encuentra la cuenta y el metodo reemplazara la sesion actual por la nueva
    }
    getAccounts(){
        //devuelve una lista de objetos con todas las cuentas actuales
    }
    isLogged(){
        //devuelve un booleano de la sesion actual
    }
    isTemporal(){
        //devuelve un booleano de la sesion actual
    }
    isAdmin(){
        //devuelve un booleano de la sesion actual
    }
    save(){
        //guardara todo a localstorage
    }
    load(){
        //recuperara todo de localstorage
    }
    expirationUTC(){
        return new Date(new Date.getTime() + this.expirationUnixTime).toUTCString();
    }
}