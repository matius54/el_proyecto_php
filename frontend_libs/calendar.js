class Calendar {
    #calendar;
    #date;
    #busy = false;
    
    constructor(element){
        if(!element) return null;
        this.#calendar = element;
        this.#date = new Date;

        const header = document.createElement("div");
        header.classList.add("header");
        const span = document.createElement("span");
        span.innerText = "Calendario de pruebas";
        header.appendChild(span);

        const substract = document.createElement("button");
        substract.innerText = "-";
        substract.addEventListener("click", () => this.substractMonth());

        const add = document.createElement("button");
        add.innerText = "+";
        add.addEventListener("click", () => this.addMonth());

        const reset = document.createElement("button");
        reset.innerText = "reset";
        reset.addEventListener("click", () => this.reset());

        header.appendChild(substract);
        header.appendChild(add);
        header.appendChild(reset);

        const body = document.createElement("div");
        body.classList.add("body");

        this.#calendar.appendChild(header);
        this.#calendar.appendChild(body);
        //this.#date.setMonth(2);
        this.draw();
    }

    clean(){
        this.#calendar.querySelectorAll(".body *").forEach(e => e.remove());
    }

    draw(){
        this.clean();
        const now = new Date;
        const rNow = {
            day : now.getDate(),
            month : now.getMonth(),
            year : now.getFullYear()
        }
        const week = ["D","L","M","M","J","V","S"];
        const months = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
        const head = this.#calendar.querySelector(".header span");
        const month = (this.getMonth() + 1).toString().padStart(2,"0");

        head.innerText = `(${months[this.getMonth()]}) ${month} / ${this.getYear()}`;

        const da = this.#calendar.querySelector(".body");
        const startDay = this.startDay();
        const getDays = this.getDays();
        week.forEach((wday)=>{
            const day = document.createElement("div");
            day.innerText = wday;
            da.appendChild(day);
        });
        for(let i = 0 - startDay; i < getDays; i++){
            const day = document.createElement("div");
            if(i >= 0){
                day.innerText = i + 1;
                day.id = i + 1;
                day.classList.add("day",i + 1);
                if(
                    i === rNow.day - 1 &&
                    this.getMonth() === rNow.month &&
                    this.getYear() === rNow.year
                ){
                    //the current day
                    day.classList.add("now");
                }
            }else{
                day.classList.add("padding");
            }
            da.appendChild(day);
        }
    }

    getDays(){
        let month = this.#date.getMonth();
        let year = this.#date.getFullYear();
        month = Math.abs(month);
        if (month >= 11){
            month = 0;
            year++;
        }
        const currentMonth = new Date(year, month);
        const nextMonth = new Date(year, month + 1);
        return (nextMonth.getTime() - currentMonth.getTime()) / 1000 / 60 / 60 / 24;
    }

    startDay(){
        const month = this.#date.getMonth();
        const year = this.#date.getFullYear();
        return new Date(year, month).getDay();
    }

    setMonth(month){
        this.#date.setMonth(month);
    }
    getMonth(){
        return this.#date.getMonth();
    }
    setYear(year){
        this.#date.setFullYear(year);
    }
    getYear(){
        return this.#date.getFullYear();
    }
    addMonth(){
        const month = this.getMonth();
        if(month !== 11){
            this.setMonth(month + 1);
        }else{
            this.setYear(this.getYear() + 1);
            this.setMonth(0);
        }
        this.draw();
    }
    substractMonth(){
        const month = this.getMonth();
        if(month !== 0){
            this.setMonth(month - 1);
        }else{
            this.setYear(this.getYear() - 1);
            this.setMonth(11);
        }
        this.draw();
    }
    reset(){
        this.#date = new Date;
        this.draw();
    }
}
