import './bootstrap';

import Alpine from 'alpinejs';

// Hacer Alpine disponible globalmente ANTES de iniciar
window.Alpine = Alpine;

// Optimización: solo inicializar Alpine cuando DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        Alpine.start();
    });
} else {
    Alpine.start();
}
