const BASE = "./php/controller/access.php";

class Role {
    #pageKey = "p";
    #InterObs;
    #page = 0;
    #lastElement;

    constructor(element){
        this.#InterObs = new IntersectionObserver(e => {
            if(e[0]?.isIntersecting) this.load();
        });

        this.#lastElement = element;
        this.#InterObs.observe(element);
    }
    load(){
        this.#InterObs.unobserve(this.#lastElement);
        ajax(`${BASE}?a=getRole&${this.#pageKey}=${this.#page + 1}`)
        .then(res => {
            if(typeof res === "number") return;
            const roles = res.items;
            for(let rol in roles) this.add(roles[rol]);
            this.#page++;
            if(!res.isLast) this.#InterObs.observe(this.#lastElement);
            NODE.update();
        });
    }
    add(json){
        let icon = json.icon ?? "edit";

        const li = document.createElement("li");
        li.classList.add("options");
        li.id = json.id;

        const inn = document.createElement("a");
        const svg = document.createElement("img");
        svg.classList.add("icon");
        svg.id = icon;
        THEME.addIcon(svg, icon);
        const span = document.createElement("span");
        span.innerText = json.name;
        const level = document.createElement("span");
        level.innerText = json.level;
        level.id = json.level;
        level.classList.add("level");
        inn.appendChild(svg);
        inn.appendChild(span);
        inn.appendChild(level);
        inn.title = `Editar el rol ${json.name}`;
        li.appendChild(inn);
        li.setAttribute("name",json.name);
        this.#lastElement.insertAdjacentElement("afterend", li);
        this.#lastElement = li;
        li.addEventListener("click", e => NODE.update(json.id));
    }
    reload(){
        this.#InterObs.unobserve(this.#lastElement);
        document.querySelectorAll("nav.navbar li.options").forEach(e => e.remove());
        this.#page = 0;
        this.#lastElement = document.querySelector("nav.navbar li");
        this.#InterObs.observe(this.#lastElement);
    }
}
class Node {
    #allNodes;
    #currId;
    #busy = false;
    constructor (){
        this.getAll();
    }
    getAll(){
        ajax(`${BASE}?a=getNodes`)
        .then(nodes => {
            this.#allNodes = typeof nodes === "object" ? nodes : {};
            this.generate();
        });
    }
    generate(){
        const main = document.querySelector("main.viewer");
        const list = document.createElement("ul");
        list.classList.add("list");
        list.setAttribute("style","display: none;");
//        const description = document.createElement("section");
//        description.classList.add("card");
//        description.innerText = "descripcion del rol";
//        list.appendChild(description);
        for(let node in this.#allNodes){
            let cathegory;
            if(cathegory = this.#allNodes[node].cathegory)
                list.appendChild(this.divider(cathegory));
            list.appendChild(
                this.li({
                    key: node,
                    ...this.#allNodes[node]
                })
            );
        }
        main.appendChild(list);
    }
    divider(name){
        const divider = document.createElement("h3");
        divider.classList.add("cathegory");
        divider.innerText = name;
        return divider;
    }
    li(json){
        const updateNode = (event) => {
            const element = event.target;
            if(element.classList.contains("selected") || this.#busy) return;
            this.#busy = true;
            const li = element.closest("li");

            const id = this.#currId;
            const key = li.id;
            const value = element.id === "3" ? true : element.id === "2" ? null : false;

            element.classList.toggle("selected",true);
            ajax(`${BASE}?a=setRoleState`,{
                id : id,
                key : key,
                value : value
            })
            .then(status => {
                if(status === 200 || status === 304){
                    li.querySelectorAll(".options a.selected")
                    .forEach(e => e.classList.toggle("selected", false));
                    element.classList.toggle("selected",true);
                }else{
                    element.classList.toggle("selected",false);
                }
            }).catch(()=>element.classList.toggle("selected",false))
            .finally(()=> this.#busy = false);
        }
        const open = document.createElement("a");
        open.title = "Expandir";
        const openIcon = document.createElement("img");
        openIcon.classList.add("icon","button","accordion");
        openIcon.id = "accordion";
        THEME.addIcon(openIcon, "accordion");
        List.addListener(openIcon);
        open.appendChild(openIcon);

        const title = document.createElement("h2");
        title.innerText = json.name;
        
        const options = document.createElement("div");
        options.classList.add("options");

        const no = document.createElement("a");
        no.innerText = "X";
        no.title = "Denegado";
        no.id = 1;
        no.addEventListener("click",updateNode);
        options.appendChild(no);

        const maybe = document.createElement("a");
        maybe.innerText = "/";
        maybe.title = "Indefinido";
        maybe.id = 2;
        maybe.classList.add("selected");
        maybe.addEventListener("click",updateNode);
        options.appendChild(maybe);

        const yes = document.createElement("a");
        yes.innerText = "√";
        yes.title = "Permitido";
        yes.id = 3;
        yes.addEventListener("click",updateNode);
        options.appendChild(yes);

        const head = document.createElement("div");
        head.classList.add("head");
        head.appendChild(open);
        head.appendChild(title);
        head.appendChild(options); 

        const li = document.createElement("li");
        li.id = json.key;
        li.appendChild(head);
        
        const body = document.createElement("div");
        body.classList.add("body");

        const description = document.createElement("span");
        description.innerText = json.description;

        body.appendChild(description);
        li.appendChild(body);
        return li;
    }
    update(id){
        //TODO: a esta funcion le falta terrible refactorizacion
        if(this.#busy) return;
        const urlParams = new URLSearchParams(window.location.search);
        if(urlParams.get("id") == id) return;
        id = id ? id : parseInt(urlParams.get("id"));
        this.#busy = true;
        history.pushState(null, null, `?id=${id}`);
        //modificar los nodos dependiendo del rol
        const list = document.querySelector("main.viewer ul");
        document.querySelectorAll(".navbar li.options.selected").forEach(e => e.classList.toggle("selected", false));
        const title = document.querySelector("header.nav h1");
        title.innerText = "";
        if(id){
            document.querySelectorAll("ul.list li").forEach(element => {
                element.querySelectorAll(".options a.selected")
                .forEach(e => e.classList.toggle("selected", false));
                element.querySelector(".options a:nth-child(2)").classList.toggle("selected",true);
            });
            const listItem = document.querySelector(`.navbar li[id=\"${id}\"].options`);
            DIALOG.unset();
            
            ajax(`${BASE}?a=getRole&id=${id}`)
            .then(json => {
                if(typeof json === "number" || json.error) throw new Error(json);
                list.removeAttribute("style");
                this.#currId = json.id;
                title.innerText = json.name;
                for(const node in json.nodes){
                    const list = document.querySelector(`ul.list li[id=\"${node}\"]`) ?? null;
                    if(!list) continue;

                    list.querySelectorAll(".options a.selected")
                    .forEach(e => e.classList.toggle("selected", false));
                    
                    if(json.nodes[node]){
                        list.querySelector(".options a:nth-child(3)").classList.toggle("selected",true);
                    }else{
                        list.querySelector(".options a:nth-child(1)").classList.toggle("selected",true);
                    }
                }
                if(listItem) listItem.classList.toggle("selected", true);
                DIALOG.set(
                    id,
                    json.name,
                    json.description,
                    json.level,
                    json.icon
                );
            }).catch(()=>{
                DIALOG.unset();
                title.innerText = "Roles";
                history.replaceState({}, '', window.location.origin + window.location.pathname);
                list.setAttribute("style","display: none;");
            })
            .finally(()=>{
                this.#busy = false;
            });

        }else{
            history.replaceState({}, '', window.location.origin + window.location.pathname);
            list.setAttribute("style","display: none;");
            this.#busy = false;
        }
    }
}
class Dialog {
    #busy = false;
    ICONS = [
        "edit",
        "delete",
        "record",
        "new",
        "check",
        "check-on",
        "drawerIcon",
        "accordion",
        "adjustments-pause",
        "profile",
        "profile2",
        "login",
        "logout",
        "eye",
        "light",
        "dark"
    ];
    INPUTS = ["name","description","level"];
    newDialog;
    editDialog;
    id = 0;
    submit = element => console.log(element);
    constructor (){
        //boton para cerrar los dialogos
        document.querySelectorAll("dialog button:first-child")
        .forEach(e => e.addEventListener("click", e =>
            e.target.closest("dialog").close()
        ));

        const addIcon = (value, element, selected) => {
            const wrapper = document.createElement("a");
            wrapper.title = value;
            wrapper.id = value;
            const icon = document.createElement("img");
            icon.classList.add("icon",value);
            icon.id = value;
            THEME.addIcon(icon, value);
            wrapper.addEventListener("mousedown",e => {
                if(!wrapper.classList.contains("selected")){
                    element.querySelector("a.selected")?.classList.remove("selected");
                    wrapper.classList.add("selected");
                }
            });
            if(selected) wrapper.classList.add("selected");
            wrapper.appendChild(icon);
            element.appendChild(wrapper);
        }

        const newIcons = document.querySelector("dialog.role.new div.icons");
        const editIcons = document.querySelector("dialog.role.edit div.icons");

        this.newDialog = newIcons.closest("dialog");
        this.editDialog = editIcons.closest("dialog");

        this.ICONS.forEach((value, i) => {
            addIcon(value, newIcons, !i);
            addIcon(value, editIcons, !i);
        });
        }
    getValues(dialog){
        const elName = dialog.querySelector("*[name=\"name\"]").value;
        const elDescription = dialog.querySelector("*[name=\"description\"]").value;
        const elLevel = dialog.querySelector("*[name=\"level\"]").value;
        const icon = this.getIcon(dialog);
        return {
            name : elName,
            description : elDescription,
            level : elLevel,
            icon : icon
        };
    }
    set(id, name, description, level, icon, dialog){
        if(!dialog) dialog = document.querySelector("dialog.role.edit");
        const elName = dialog.querySelector("*[name=\"name\"]");
        const elDescription = dialog.querySelector("*[name=\"description\"]");
        const elLevel = dialog.querySelector("*[name=\"level\"]");
        let elIcon = dialog.querySelector("div.icons");

        elName.value = name;
        elDescription.value = description;
        elLevel.value = level;
        
        if(elIcon = dialog.querySelector(`a#${icon}`)){
            dialog.querySelector("a.selected").classList.remove("selected");
            elIcon.classList.add("selected");
        }

        this.id = id;
    }
    unset(dialog){
        this.id = 0;
        this.set("","","","","edit",dialog);
    }
    getIcon(element){
        return element.querySelector(".icons a.selected").title;
    }
    new(){
        const dialog = document.querySelector('dialog.new.role');
        dialog.showModal();

        this.submit = () => {
            let error;
            if(this.#busy)return;
            const values = this.getValues(dialog);
            ajax(`${BASE}?a=newRole`, values)
            .then(status => {
                let newId;
                if(newId = status.id){
                    ROLE.reload();
                    NODE.update(newId);
                    dialog.close();
                    this.unset(dialog);
                }else if(error = status.error) alert(error);
            })
            .finally(()=>{
                this.#busy = false;
            });
        }
    }
    edit(){
        if(!this.id){
            alert("Selecciona un rol primero");
            return;
        }
        const dialog = document.querySelector('dialog.edit.role');
        dialog.showModal();
        this.submit = () => {
            if(this.#busy)return;
            const values = this.getValues(dialog);
            ajax(`${BASE}?a=editRole`,{
                id : this.id,
                ...values
            })
            .then(status => {
                if(status === 200){
                    ROLE.reload();
                    dialog.close();
                }
            })
            .finally(()=>{
                this.#busy = false;
            });
        }
    }
    delete(){
        if(!this.id){
            alert("Selecciona un rol primero");
            return;
        }
        const dialog = document.querySelector('dialog.delete.role');
        dialog.showModal();
        this.submit = () => {
            if(this.#busy)return;
            ajax(`${BASE}?a=deleteRole`,{id : this.id})
            .then(status => {
                if(status === 200){
                    NODE.update(0);
                    ROLE.reload();
                    dialog.close();
                }
            })
            .finally(()=>{
                this.#busy = false;
            });
        }
    }
}
const DIALOG = new Dialog;
const NODE = new Node;
const ROLE = new Role(document.querySelector(".navbar li:last-child"));
window.addEventListener('popstate', e => NODE.update());