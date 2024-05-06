if (!window.jsPDF) window.jsPDF = window.jspdf.jsPDF;

document.querySelectorAll("table").forEach(table => {
    const downloadBtt = document.createElement("button");
    downloadBtt.innerText = "Descargar tabla";
    downloadBtt.addEventListener("click", ()=>{
        const doc = new jsPDF();
        doc.autoTable({ html: table });
        doc.save("table.pdf");
    });
    table.insertAdjacentElement("afterend", downloadBtt);
});