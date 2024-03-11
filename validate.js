class Validate {
    constructor(){
        document.querySelectorAll("form").forEach(form => {
            form.querySelectorAll("input").forEach((e) => this.addListeners(e));
            form.querySelectorAll("textarea").forEach((e) => this.addListeners(e));
        });
    }
    addListeners(element){
        element.addEventListener("click",this.validate);
        element.addEventListener("input",this.validate);
        element.addEventListener("blur",this.validate);
    }
    same(elem1, elem2){
        validate.updateDOM(elem2,(elem1.value === elem2.value))
    }
    validate(event){
        //valores de configuracion
        const MATCH = "match";
        const DIVIDER = "_";
        const regex = {
            text : /[A-Za-z| ]+/g,
            username : /^\w{1,64}/,
            name : /^\w+( \w+)?$/,
            email : /^[^@]+@[^@]+\.[a-zA-Z]{2,}$/,
            password : /^(\w| )+$/,
            date : /^(20|19)\d{2,2}-[0-1]\d-[0-3]\d$/
        };
        //constantes y variables
        const element = event.target;
        const value = element.value;
        let same;
        if(typeof (same = element.getAttribute("same")) === "string"){
            const form = element.closest("form");
            const el2 = form.querySelector(`input[name="${same}"]`);
            if(el2){
                validate.updateDOM(element,validate.same(el2,element));
                return;
            }
        }
        let key = element.type;
        let valid;
        let test;
        //validacion
        if(element.id) key = element.id.split(DIVIDER)[0];
        if(element.name) key = element.name.split(DIVIDER)[0];
        if((test = regex[key]) && value){
            if(typeof element.getAttribute(MATCH) === "string"){
                const val = value.match(test);
                if(val){
                    element.value = val.join("");
                    valid = true;
                }else{
                    element.value = "";
                }
            }else{
                valid = test.test(value);
            }
        }
        //actualizacion del dom
        validate.updateDOM(element,valid);
    }
    updateDOM = (element, result) => {
        const VALID = "valid";
        const INVALID = "invalid";
        const classL = element.classList;
        console.log(element);
        console.log(result);
        if(result === true){
            classL.add(VALID);
            classL.remove(INVALID);
        }else if(result === false){
            classL.remove(VALID);
            classL.add(INVALID);
        }else{
            classL.remove(VALID);
            classL.remove(INVALID);
        }
    }
}

//TODO: validar formulario y preventDefault()
const validate = new Validate;