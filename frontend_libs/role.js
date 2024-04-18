const BASE = "./libs/access.php";
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
        sessionStorage.clear();
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
        //TODO
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
        inn.appendChild(svg);
        inn.appendChild(span);
        inn.title = `Editar el rol ${json.name}`;
        li.appendChild(inn);
        li.setAttribute("name",json.name);
        this.#lastElement.insertAdjacentElement("afterend", li);
        this.#lastElement = li;
        li.addEventListener("click", e => NODE.update(json.id));
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
        for(let node in this.#allNodes)
        list.appendChild(
            this.li({
                key: node,
                ...this.#allNodes[node]
            })
        );
        main.appendChild(list);
    }
    li(json){
        const updateNode = (event) => {
            const element = event.target;
            if(element.classList.contains("selected") || this.#busy) return;
            this.#busy = true;
            const li = element.closest("li");

            const id = this.#currId;
            const key = li.id;
            const value = element.id === "1" ? true : element.id === "2" ? null : false;

            element.classList.toggle("selected",true);
            ajax(`${BASE}?a=setRole`,{
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
            }).finally(()=> this.#busy = false);
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
        title.innerText = "Roles";
        if(id){
            document.querySelectorAll("ul.list li").forEach(element => {
                element.querySelectorAll(".options a.selected")
                .forEach(e => e.classList.toggle("selected", false));
                element.querySelector(".options a:nth-child(2)").classList.toggle("selected",true);
            });
            const listItem = document.querySelector(`.navbar li[id=\"${id}\"].options`);
            
            ajax(`${BASE}?a=getRole&id=${id}`)
            .then(json => {
                if(typeof json === "number") return;
                list.removeAttribute("style");
                this.#currId = json.id;
                title.innerText = `Nodos en el rol ${json.name}`;
                for(const node in json.nodes){
                    const list = document.querySelector(`ul.list li[id=\"${node}\"]`) ?? null;
                    if(!list) continue;

                    list.querySelectorAll(".options a.selected")
                    .forEach(e => e.classList.toggle("selected", false));
                    
                    if(json.nodes[node]){
                        list.querySelector(".options a:nth-child(1)").classList.toggle("selected",true);
                    }else{
                        list.querySelector(".options a:nth-child(3)").classList.toggle("selected",true);
                    }
                }
            }).finally(()=>{
                if(listItem) listItem.classList.toggle("selected", true);
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
    ICONS = [
        "edit",
        "delete",
        "new",
        "check",
        "check-on",
        "drawerIcon",
        "accordion",
        "adjustments-pause",
        "profile",
        "login"
    ];
    INPUTS = ["name","description","level"];
    newDialog;
    editDialog;
    name;
    description;
    level;
    icon;
    constructor (){
        //boton para cerrar los dialogos
        document.querySelectorAll("dialog button.close")
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

        newIcons = document.querySelector("dialog.role.new div.icons");
        editIcons = document.querySelector("dialog.role.edit div.icons");
        this.newDialog = newIcons.closest("dialog");
        this.editDialog = editIcons.closest("dialog");

        this.ICONS.forEach((value, i) => {
            addIcon(value, newIcons, !i);
            addIcon(value, editIcons, !i);
        });
        }
    set(name, description, level, icon){
        const dialog = document.querySelector("dialog.role.edit");
        const elName = dialog.querySelector("*[name=\"name\"]");
        const elDescription = dialog.querySelector("*[name=\"description\"]");
        const elLevel = dialog.querySelector("*[name=\"level\"]");
        const elIcon = dialog.querySelector("div.icons");
        console.log(elName);
        console.log(elDescription);
        console.log(elLevel);
        console.log(elIcon);
    }
    unset(){
        this.set("","","","edit");
    }
}
const DIALOG = new Dialog;
const NODE = new Node;
const ROLE = new Role(document.querySelector(".navbar li:last-child"));
window.addEventListener('popstate', e => NODE.update());