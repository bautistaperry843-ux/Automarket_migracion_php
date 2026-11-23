window.addEventListener("load", () => {
    
    // 1. FECHA Y HORA
    const elFecha = document.getElementById("fecha-hora");
    if (elFecha) {
        const actualizar = () => {
            const ahora = new Date();
            const fecha = ahora.toLocaleDateString("es-AR", {
                weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
            });
            elFecha.textContent = `${fecha} | ⏰ ${ahora.toLocaleTimeString("es-AR")}`;
        };
        actualizar();
        setInterval(actualizar, 1000);
    }

    // 2. GALERÍA DE IMÁGENES
    // Selecciona absolutamente TODAS las imágenes presentes en el HTML
    const listaImagenes = Array.from(document.querySelectorAll("img"));

    if (listaImagenes.length > 0) {
        let indiceActual = 0;

        // Crear la ventana modal (fondo oscuro)
        const modal = document.createElement("div");
        modal.id = "modal-galeria";
        modal.style.cssText = `
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.88);
            z-index: 999999;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            box-sizing: border-box;
        `;

        // Imagen en el visor (limitada al 60% del alto para dejar lugar a los botones)
        const imgVisor = document.createElement("img");
        imgVisor.style.cssText = `
            max-width: 85vw;
            max-height: 60vh;
            object-fit: contain;
            border-radius: 8px;
            border: 2px solid #ffffff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.8);
        `;

        // Contenedor de los botones
        const contBotones = document.createElement("div");
        contBotones.style.cssText = `
            margin-top: 25px;
            display: flex;
            gap: 15px;
            z-index: 1000000;
        `;

        const estiloBtn = "padding: 10px 22px; cursor: pointer; border: none; border-radius: 6px; font-weight: bold; font-size: 1rem;";

        // Botón 1: Atrás
        const btnAtras = document.createElement("button");
        btnAtras.textContent = "◀ Atrás";
        btnAtras.style.cssText = estiloBtn + "background-color: #38bdf8; color: #0f172a;";

        // Botón 2: Salir
        const btnSalir = document.createElement("button");
        btnSalir.textContent = "✖ Salir";
        btnSalir.style.cssText = estiloBtn + "background-color: #dc2626; color: #ffffff;";

        // Botón 3: Siguiente
        const btnSiguiente = document.createElement("button");
        btnSiguiente.textContent = "Siguiente ▶";
        btnSiguiente.style.cssText = estiloBtn + "background-color: #38bdf8; color: #0f172a;";

        contBotones.appendChild(btnAtras);
        contBotones.appendChild(btnSalir);
        contBotones.appendChild(btnSiguiente);

        modal.appendChild(imgVisor);
        modal.appendChild(contBotones);
        document.body.appendChild(modal);

        const actualizarVisor = (index) => {
            indiceActual = index;
            imgVisor.src = listaImagenes[indiceActual].src;
        };

        // Hacer clickeables todas las imágenes
        listaImagenes.forEach((img, idx) => {
            img.style.cursor = "pointer";
            img.addEventListener("click", (e) => {
                e.stopPropagation();
                actualizarVisor(idx);
                modal.style.display = "flex";
            });
        });

        // Eventos de los 3 botones
        btnAtras.addEventListener("click", (e) => {
            e.stopPropagation();
            const nuevoIndex = (indiceActual - 1 + listaImagenes.length) % listaImagenes.length;
            actualizarVisor(nuevoIndex);
        });

        btnSiguiente.addEventListener("click", (e) => {
            e.stopPropagation();
            const nuevoIndex = (indiceActual + 1) % listaImagenes.length;
            actualizarVisor(nuevoIndex);
        });

        btnSalir.addEventListener("click", (e) => {
            e.stopPropagation();
            modal.style.display = "none";
        });

        modal.addEventListener("click", (e) => {
            if (e.target === modal) {
                modal.style.display = "none";
            }
        });
    }

    // 3. MODO OSCURO PERSISTENTE
    if (localStorage.getItem("modoOscuro") === "activado") {
        document.body.classList.add("dark-mode");
        const btnModo = document.getElementById("btn-modo-oscuro");
        if (btnModo) btnModo.textContent = "☀️ Modo Claro";
    }

    window.addEventListener("pointermove", (event) => {
        if (!document.body.classList.contains("dark-mode")) return;

        document.body.style.setProperty("--cursor-x", `${event.clientX}px`);
        document.body.style.setProperty("--cursor-y", `${event.clientY}px`);
    });
});

// Función global del botón de Modo Oscuro
function alternarModo() {
    document.body.classList.toggle("dark-mode");
    const esOscuro = document.body.classList.contains("dark-mode");
    const btnModo = document.getElementById("btn-modo-oscuro");

    if (btnModo) {
        btnModo.textContent = esOscuro ? "☀️ Modo Claro" : "🌙 Modo Oscuro";
    }
    localStorage.setItem("modoOscuro", esOscuro ? "activado" : "desactivado");
}