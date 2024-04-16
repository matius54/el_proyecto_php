//si json existe, se hace un POST y si no se hace un GET
//respuesta puede ser Array(json respuesta decodificado) o int(codigo de estado http)

async function ajax(url,json){
    const response = json ? await fetch(url,{
        method: "post",
        headers: {
            "Content-Type": "application/json; charset=utf-8",
            "Accept": "application/json"
        },
        body: JSON.stringify(json)
    }) : await fetch(url);
    try {
        return await response.json();
    } catch {
        return response.status;
    }
}