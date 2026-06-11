document.addEventListener('DOMContentLoaded', function() {
    // Manejar clic en botones Editar
    const editButtons = document.querySelectorAll('.edit-btn');
    
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const formato = this.parentElement.querySelector('span').textContent;
            console.log('Editar formato:', formato);
            
            // Aquí puedes agregar la lógica para editar
            alert(`Editar formato: ${formato}`);
            
            // Ejemplo: Redirigir a pantalla de edición
            // window.location.href = `/formatos/editar?tipo=${encodeURIComponent(formato)}`;
        });
    });
});