// SIGRA - JS global da aplicação

document.addEventListener('DOMContentLoaded', function () {
    var toggle = document.getElementById('sigraSidebarToggle');
    var sidebar = document.querySelector('.sigra-sidebar');
    if (toggle && sidebar) {
        toggle.addEventListener('click', function () {
            sidebar.classList.toggle('show');
        });
    }

    // Confirmação para acções destrutivas
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('submit', function (e) {
            if (!confirm(el.getAttribute('data-confirm'))) {
                e.preventDefault();
            }
        });
    });
});

/**
 * Toast flutuante global. Uso: kfToast('Mensagem', 'success'|'danger'|'warning'|'info')
 */
function kfToast(mensagem, tipo) {
    tipo = tipo || 'success';
    var container = document.getElementById('sigraToastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'sigraToastContainer';
        container.style.position = 'fixed';
        container.style.top = '1rem';
        container.style.right = '1rem';
        container.style.zIndex = '2000';
        document.body.appendChild(container);
    }

    var toast = document.createElement('div');
    toast.className = 'alert alert-' + tipo + ' shadow-sm mb-2';
    toast.style.minWidth = '260px';
    toast.textContent = mensagem;
    container.appendChild(toast);

    setTimeout(function () {
        toast.style.transition = 'opacity 0.4s';
        toast.style.opacity = '0';
        setTimeout(function () { toast.remove(); }, 400);
    }, 3500);
}
