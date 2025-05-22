document.addEventListener('DOMContentLoaded', function() {
    let mesaSeleccionada = null;
    
    // Manejo de selección de mesas
    document.querySelectorAll('.mesa-btn').forEach(mesa => {
        mesa.addEventListener('click', function() {
            // Remover selección previa
            document.querySelectorAll('.mesa-btn').forEach(m => m.classList.remove('selected'));
            
            // Seleccionar la mesa actual
            this.classList.add('selected');
            mesaSeleccionada = this.dataset.mesa;
            
            console.log('Mesa seleccionada:', mesaSeleccionada);
        });
    });
    
    // Manejo del botón comanda - BUSCAR POR TEXTO
    document.addEventListener('click', function(e) {
        const menuButton = e.target.closest('.menu-icon-btn');
        
        if (menuButton) {
            const buttonText = menuButton.querySelector('span')?.textContent?.trim();
            console.log('Botón clickeado:', buttonText);
            
            if (buttonText === 'Comanda') {
                e.preventDefault();
                
                if (mesaSeleccionada) {
                    console.log('Redirigiendo a comanda para mesa:', mesaSeleccionada);
                    window.location.href = BASE_URL + '/mozo/comanda/' + mesaSeleccionada;
                } else {
                    alert('Por favor, selecciona una mesa primero');
                }
            }
        }
    });
    
    // Debug: Verificar que BASE_URL esté definido
    console.log('BASE_URL:', BASE_URL);
});