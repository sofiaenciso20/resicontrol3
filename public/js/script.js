/**
 * Scripts para el navbar
 *
 * Este script maneja la funcionalidad del sidebar (barra lateral) de la aplicación:
 *
 * 1. Sidebar Toggle:
 *    - Permite mostrar/ocultar el sidebar mediante un botón
 *    - Cuando se hace clic en el botón con id 'sidebarToggle':
 *      * Alterna la clase 'sb-sidenav-toggled' en el body
 *      * Guarda el estado del sidebar (abierto/cerrado) en localStorage
 *    - El estado se mantiene entre recargas de página gracias al localStorage
 *
 * 2. Persistencia:
 *    - El estado del sidebar se guarda en localStorage con la clave 'sb|sidebar-toggle'
 *    - Esto permite mantener la preferencia del usuario sobre si el sidebar debe estar
 *      visible u oculto cuando regresa a la página
 */
 
window.addEventListener('DOMContentLoaded', event => {
    // Inicializar todos los dropdowns
    const dropdownElementList = document.querySelectorAll('.dropdown-toggle');
    const dropdownList = [...dropdownElementList].map(dropdownToggleEl => {
        return new bootstrap.Dropdown(dropdownToggleEl);
    });
 
    // Toggle the side navigation
    const sidebarToggle = document.body.querySelector('#sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', event => {
            event.preventDefault();
            document.body.classList.toggle('sb-sidenav-toggled');
            localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
        });
    }
});