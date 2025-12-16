// Sistema de Toast Notifications com AlpineJS
window.toast = {
    show(message, type = 'info') {
        const event = new CustomEvent('toast', {
            detail: { message, type }
        });
        window.dispatchEvent(event);
    },
    success(message) {
        this.show(message, 'success');
    },
    error(message) {
        this.show(message, 'error');
    },
    info(message) {
        this.show(message, 'info');
    },
    warning(message) {
        this.show(message, 'warning');
    }
};

