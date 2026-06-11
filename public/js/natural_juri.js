document.addEventListener("DOMContentLoaded", function () {
    const tipoPersonaSelect = document.getElementById("tipo_persona");
    const camposNatural = document.getElementById("campos_natural");
    const camposJuridica = document.getElementById("campos_juridica");

    function toggleFormulario() {
        if (tipoPersonaSelect.value === "natural") {
            camposNatural.style.display = "block";
            camposJuridica.style.display = "none";
        } else {
            camposNatural.style.display = "none";
            camposJuridica.style.display = "block";
        }
    }

    tipoPersonaSelect.addEventListener("change", toggleFormulario);

    // Ejecutar al cargar la página
    toggleFormulario();
});