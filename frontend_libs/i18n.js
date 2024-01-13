class I18n {
    #I18N_CONFIG_FILE_PATH = "./i18n.json";
    #allLangs; //all languages in i18n config file
    #lang; //current language
    #dict; //current language's dictionary
    #busy; //true || false (async concurency control)
    #url; //langsDirURL
    #LSKey; //localStorageKey
    #htmlAKey; //html atribute to lookup
    #jsonOptionalKey; //key to lookup when json anidated is found
    #retryFails = 3;
    #retryCount = 0;
    constructor(callback){
        this.#initialize(callback);
    }
    #initialize(callback){
        this.#busy = true;
        this.#getJSON(this.#I18N_CONFIG_FILE_PATH,json => {
            this.#allLangs = json["langs"];
            this.#url = json["langsDirURL"];
            this.#LSKey = json["localStorageKey"];
            this.#htmlAKey = json["htmlAtributeName"];
            this.#jsonOptionalKey = json["optionalLangKey"];
            this.#busy = false;
            this.setLang(this.#fromLS(),callback);
        },() => console.error("Error loading i18n config file"));
    }
    #getJSON(url,callback,error=()=>{console.error("Error Getting the JSON")}){
        fetch(url)
        .then(response => response.json())
        .then(json => {
            callback(json);
            this.#retryCount = 0;
        })
        .catch((err) => {
                if(this.#retryCount >= this.#retryFails){
                    console.error(`Max retry cycle of '${this.#retryFails}' reached`);
                    this.#retryCount = 0;
                    this.#busy = false;
                    throw err;
                }else{
                    this.#retryCount ++;
                    error();
                }
            });
    }
    setLang(lang,callback){
        let oldLang = this.#lang;
        if(typeof(lang)==="string" && this.#allLangs.includes(lang)){
            this.#lang = lang;
        }else{
            if(lang !== undefined)console.warn(`Language '${lang}' is not valid`);
        }
        if(!this.#lang)this.#lang = this.#allLangs[0];
        this.#toLS();
        this.#updateDict(callback,oldLang);
    }
    #updateDict(callback,oldLang){
        if(this.#busy) return;
        //console.info(`Uptading Dictionary for '${this.#lang}'`);
        this.#busy = true;
        this.#getJSON(`${this.#url}${this.#lang}.json`,lang => {
            this.#dict = lang;
            this.#busy = false;
            //console.info("Update done");
            this.updateDOM();
            if(typeof(callback)==="function")callback();
        },()=>{
            console.error(`Error Updating dictionary for '${this.#lang}'`);
            if(this.#lang === oldLang)oldLang = undefined;
            let fallbackLang = oldLang? oldLang : this.#allLangs[0];
            console.warn(`Setting back to fallback language '${fallbackLang}'`);
            this.#busy = false;
            this.setLang(fallbackLang);
        });
    }
    updateDOM(baseElement){
        let all;
        if(baseElement && baseElement.nodeType === 1){
            all = baseElement.parentElement.querySelectorAll('*');
        }else{
            all = document.getRootNode().all;
        }
        for (let i = 0; i < all.length; i ++){
            let element = all[i];
            let atr = element.getAttribute(this.#htmlAKey);
            //let langAtr = element.getAttribute("lang");
            //if(typeof(langAtr)==="string")element.setAttribute("lang",this.#lang);
            if(typeof(atr)!=="string")continue;
            let translatedKey = this.#dict[atr];
            if(typeof(translatedKey)==="object" && !(translatedKey instanceof Array)){
                for(let key in translatedKey){
                    if(key===this.#jsonOptionalKey)continue;
                    element.setAttribute(key,translatedKey[key])
                }
                translatedKey = translatedKey[this.#jsonOptionalKey] || "";
            }
            if(typeof(translatedKey)!=="string"){
                console.warn(`Missing or malformed translation key for '${atr}' in language '${this.#lang}'`);
                continue;
            }
            //if translated key is empty doesnt replace
            if(translatedKey==="")continue;
            let htmlString = element.innerHTML;
            let htmlStarts = htmlString.indexOf("<");
            let htmlEnds = htmlString.indexOf("</");
            if(htmlStarts === -1){
                //if the element doesnt have html
                element.innerText = translatedKey;
                continue;
            }
            if(htmlStarts > 0){
                //if the text if BEFORE the html
                let next = htmlString.slice(htmlStarts);
                element.innerHTML = `${translatedKey}${next}`;
            }else{
                //if the text if AFTER the html
                htmlEnds = htmlString.indexOf(">",htmlEnds) + 1;
                let startHTML = htmlString.slice(0,htmlEnds);
                htmlEnds = htmlString.indexOf("<",htmlEnds);
                let endHTML = htmlEnds !== -1? htmlString.slice(htmlEnds) : "";
                element.innerHTML = `${startHTML}${translatedKey}${endHTML}`;
            }
        }
        document.html
        //console.info("DOM Updated")
    }
    #toLS(){
        localStorage.setItem(this.#LSKey,this.#lang);
    }
    #fromLS(){
        this.#lang = localStorage.getItem(this.#LSKey);
    }
    clearLS(){
        localStorage.removeItem(this.#LSKey);
    }
    getAllLangs(){
        return this.#allLangs;
    }
    getDictionary(){
        return this.#dict;
    }
    isBusy(){
        return this.#busy;
    }
    getLang(){
        return this.#lang;
    }
}
