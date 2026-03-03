/**
 * JAVASCRIPT PRINCIPAL
 * Instituto Padres de la Patria
 * Funcionalidades globales y utilidades
 */

// Esperar a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    
    // ===============================================
    // INICIALIZAR TOOLTIPS DE BOOTSTRAP 5
    // ===============================================
    const tooltipTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="tooltip"]')
    );
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            trigger: 'hover focus'
        });
    });

    // ===============================================
    // INICIALIZAR POPOVERS DE BOOTSTRAP 5
    // ===============================================
    const popoverTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="popover"]')
    );
    const popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // ===============================================
    // AUTO-CERRAR ALERTAS DESPUÉS DE 5 SEGUNDOS
    // ===============================================
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // ===============================================
    // CONFIRMACIÓN DE ELIMINACIÓN
    // ===============================================
    const deleteButtons = document.querySelectorAll('.btn-delete-confirm');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            if (!confirm('¿Estás seguro de eliminar este elemento? Esta acción no se puede deshacer.')) {
                e.preventDefault();
            }
        });
    });

    // ===============================================
    // VALIDACIÓN DE FORMULARIOS
    // ===============================================
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // ===============================================
    // VALIDACIÓN DE CONTRASEÑAS (si existe campo de confirmación)
    // ===============================================
    const passwordField = document.getElementById('password');
    const passwordConfirmField = document.getElementById('password_confirm');
    
    if (passwordField && passwordConfirmField) {
        passwordConfirmField.addEventListener('input', function() {
            if (passwordField.value !== passwordConfirmField.value) {
                passwordConfirmField.setCustomValidity('Las contraseñas no coinciden');
            } else {
                passwordConfirmField.setCustomValidity('');
            }
        });
        
        passwordField.addEventListener('input', function() {
            if (passwordConfirmField.value && passwordField.value !== passwordConfirmField.value) {
                passwordConfirmField.setCustomValidity('Las contraseñas no coinciden');
            } else {
                passwordConfirmField.setCustomValidity('');
            }
        });
    }

    // ===============================================
    // MOSTRAR/OCULTAR CONTRASEÑA
    // ===============================================
    const togglePasswordButtons = document.querySelectorAll('.toggle-password');
    togglePasswordButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // ===============================================
    // BÚSQUEDA EN TABLA (filtro rápido)
    // ===============================================
    const searchInput = document.getElementById('tableSearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const table = document.querySelector('table tbody');
            const rows = table.querySelectorAll('tr');
            
            rows.forEach(function(row) {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }

    // ===============================================
    // FORMATO DE NÚMEROS (para calificaciones)
    // ===============================================
    const numberInputs = document.querySelectorAll('input[type="number"].format-decimal');
    numberInputs.forEach(function(input) {
        input.addEventListener('blur', function() {
            if (this.value) {
                this.value = parseFloat(this.value).toFixed(1);
            }
        });
    });

    // ===============================================
    // ANIMACIÓN DE FADE IN AL CARGAR
    // ===============================================
    const fadeElements = document.querySelectorAll('.fade-in');
    fadeElements.forEach(function(element, index) {
        setTimeout(function() {
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // ===============================================
    // SCROLL SUAVE EN ENLACES DE ANCLA
    // ===============================================
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // ===============================================
    // COPIAR AL PORTAPAPELES
    // ===============================================
    const copyButtons = document.querySelectorAll('.btn-copy');
    copyButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-copy-target');
            const target = document.getElementById(targetId);
            
            if (target) {
                navigator.clipboard.writeText(target.textContent).then(function() {
                    // Mostrar feedback
                    const originalText = button.textContent;
                    button.textContent = '✓ Copiado';
                    setTimeout(function() {
                        button.textContent = originalText;
                    }, 2000);
                });
            }
        });
    });

    // ===============================================
    // MOSTRAR INDICADOR DE CARGA
    // ===============================================
    const loadingForms = document.querySelectorAll('form.show-loading');
    loadingForms.forEach(function(form) {
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';
            }
        });
    });

});

// ===============================================
// FUNCIONES GLOBALES ÚTILES
// ===============================================

/**
 * Mostrar notificación toast (requiere Bootstrap 5)
 */
function showToast(message, type = 'info') {
    // Implementación básica, puedes mejorarla según necesites
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
    alertDiv.style.zIndex = '9999';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alertDiv);
    
    setTimeout(function() {
        alertDiv.remove();
    }, 5000);
}

/**
 * Validar email
 */
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

/**
 * Formatear fecha
 */
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('es-ES', options);
}

/**
 * Debounce (útil para búsquedas)
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// ===============================================
// EXPORTAR FUNCIONES (si usas módulos)
// ===============================================
// export { showToast, validateEmail, formatDate, debounce };
