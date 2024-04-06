const canvas = document.querySelector(".pattern");
const output = document.querySelector(".out");
const tileSizepx = 100;//100
const nodesX = 3;//3
const nodesY = 3;//3
const sizeX = tileSizepx * nodesX;//300
const sizeY = tileSizepx * nodesY;//300
canvas.height = sizeY;//300
canvas.width = sizeX;//300
const nodesSize = tileSizepx / 7;//7
const nodesBoxOffset = 0;
const lineWidth = tileSizepx / 20;//20
const paddingX = 0;//0
const paddingY = 0;//0
const minLength = (nodesX + nodesY)/2;//3
const color = "white";
const errorColor = "red";
const okColor = "white";
const ctx = canvas.getContext("2d");
let crrColor = color;
let startPos = undefined;
let lastPoint = undefined;
let dawing = false;
let segments = [];
function reDraw(){
    ctx.moveTo(0, 0);
    ctx.clearRect(0, 0, sizeX, sizeY);
    for(let i = 0; i < nodesX; i++){
        for(let j = 0; j < nodesY; j++){
            ctx.strokeStyle = crrColor;
            ctx.fillStyle = crrColor;
            ctx.lineWidth = 1;
            ctx.beginPath();
            const posX = sizeX / nodesX;
            const posY = sizeY / nodesY;
            ctx.arc((posX * i) + (posX / 2) + paddingX, (posY * j) + (posY / 2) + paddingY, nodesSize, 0, 2 * Math.PI);
            if(segments.includes([i,j].toString())){
                ctx.fill();
            }else{
                ctx.stroke();
            }
        }
    }
    lines();
}
function lines(){
    if(segments.length<=0)return;
    segments.reduce((seg_old,seg,indx)=>{
        [x,y] = seg.split(",");
        const crntSeg = {
            x:parseInt(x),
            y:parseInt(y)
        }
        if(seg_old){
            drawLineCords(seg_old.x,seg_old.y,crntSeg.x,crntSeg.y,indx);
        }
        return crntSeg;
    },undefined);
}
function drawLineCords(cx1,cy1,cx2,cy2,indx){
    const posX = sizeX / nodesX;
    const posY = sizeY / nodesY;
    const x1 = (posX * cx1) + (posX / 2) + paddingX;
    const y1 = (posY * cy1) + (posY / 2) + paddingY;
    const x2 = (posX * cx2) + (posX / 2) + paddingX;
    const y2 = (posY * cy2) + (posY / 2) + paddingY;
    drawLine(x1,y1,x2,y2,color);
}
reDraw();
function getXYevent(e){
    let box = canvas.getBoundingClientRect();
    let x;
    let y;
    if(event.type.startsWith("touch")){
        const touches = e.touches;
        const touchLen = touches.length;
        x = touches[touchLen-1].clientX;
        y = touches[touchLen-1].clientY;
    }else{
        x = e.clientX;
        y = e.clientY;
    }
    return [x - box.x, y - box.y]
}
const start = (e) => {
    crrColor = color;
    let [sX, sY] = getXYevent(e); 
    if(checkForPoint(sX,sY)){
        dawing = true;
        segments = [];
        startPos = {x:sX,y:sY};
        drawnPoint(e);
    }
}
const end = (e) => {
    if(!dawing)return;
    crrColor = segments.length <= minLength ? errorColor : okColor;
    reDraw();
    dawing = false;
    startPos = undefined;
    lastPoint = undefined;
    output.innerHTML=JSON.stringify(segments);
}
const update = (e) => {
    if(!dawing) return;
    reDraw();
    drawnPoint(e);
}
function drawLine(x1, y1, x2,y2) {
    ctx.beginPath();
    ctx.moveTo(x1, y1);
    ctx.lineTo(x2, y2);
    ctx.strokeStyle = crrColor;
    ctx.lineWidth = lineWidth;
    ctx.stroke();
    }
const drawnPoint = (e) => {
    let [x , y] = getXYevent(e);
    checkForPoint(x,y);
    drawLine(startPos.x, startPos.y,x , y)
}
function checkInterm(x,y){
    const ox = lastPoint.x;
    const oy = lastPoint.y;
    const diffX = Math.abs(x-ox);
    const diffY = Math.abs(y-oy);
    const reverseX = x > ox ? false : true;
    const reverseY = y > oy ? false : true;
    if(diffX>1 && diffY>1 && diffX===diffY){
        for(let i = 0; i < diffX -1 ;i++){
            if(!reverseX && !reverseY){
                payload = [ox + i + 1,oy + i + 1].toString();
            }
            if(reverseX && !reverseY){
                payload = [ox - i - 1,oy + i + 1].toString();
            }
            if(!reverseX && reverseY){
                payload = [ox + i + 1,oy - i - 1].toString();
            }
            if(reverseX && reverseY){
                payload = [ox - i - 1,oy - i - 1].toString();
            }
            if(!segments.includes(payload))segments.push(payload);
        }
    }else if(diffY>1 && diffX === 0){
        for(let i = 0; i < diffY -1 ;i++){
            if(reverseY){
                payload = [x,oy - i - 1].toString();
            }else{
                payload = [x,oy + i + 1].toString();
            }
            if(!segments.includes(payload))segments.push(payload);
        }
    }else if(diffX>1 && diffY === 0){
        for(let i = 0; i < diffX -1 ;i++){
            let payload;
            if(reverseX){
                payload = [ox - i - 1,y].toString();
            }else{
                payload = [ox + i + 1,y].toString();
            }
            if(!segments.includes(payload))segments.push(payload);
        }
    }
}
function checkForPoint(x,y){
    for(let i = 0; i < nodesX; i++){
        for(let j = 0; j < nodesY; j++){
            let posX = sizeX / nodesX;
            let posY = sizeY / nodesY;
            let cx = (posX * i) + (posX / 2) + paddingX;
            let cy = (posY * j) + (posY / 2) + paddingY;
            let hitbox = nodesSize + nodesBoxOffset;
            if((cx > x - hitbox && cx < x + hitbox) && (cy > y - hitbox && cy < y + hitbox)){
                let payload = [i,j].toString();
                if(!segments.includes(payload)){
                    startPos={x:cx,y:cy};
                    if(lastPoint)checkInterm(i,j);
                    segments.push(payload);
                    lastPoint = {x:i,y:j};
                    reDraw();
                    output.innerHTML=JSON.stringify(segments);
                    console.log(startPos);
                    console.log(lastPoint);
                }
                return true;
            }
        }
    }
}

canvas.addEventListener("mousedown",start);
canvas.addEventListener("touchstart",start);
document.addEventListener("mouseup",end);
document.addEventListener("touchend",end);
window.addEventListener("mousemove",update);
canvas.addEventListener("touchmove",update);