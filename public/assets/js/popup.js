// assets/js/popup.js
class LoginModal {
    constructor() {
        this.modal = document.getElementById('loginModal');
        this.loginBtn = document.getElementById('loginBtn');
        this.closeBtn = document.querySelector('.close');
        this.loginFrame = document.getElementById('loginFrame');
        
        this.init();
    }
    
    init() {
        // Show modal
        this.loginBtn?.addEventListener('click', () => this.show());
        
        // Close modal
        this.closeBtn?.addEventListener('click', () => this.hide());
        
        // Close on outside click
        window.addEventListener('click', (e) => {
            if (e.target === this.modal) {
                this.hide();
            }
        });
        
        // Handle message from iframe
        window.addEventListener('message', (event) => {
            if (event.data.type === 'login_success') {
                this.hide();
                window.location.reload();
            }
        });
    }
    
    show() {
        if (this.modal) {
            this.modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
    }
    
    hide() {
        if (this.modal) {
            this.modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }
}

// Initialize modal
document.addEventListener('DOMContentLoaded', () => {
    const loginModal = new LoginModal();
});

// Form validation
function validateForm(formElement) {
    const username = formElement.querySelector('input[name="username"]');
    const password = formElement.querySelector('input[name="password"]');
    let isValid = true;
    
    if (!username.value.trim()) {
        showError(username, 'Username is required');
        isValid = false;
    }
    
    if (!password.value) {
        showError(password, 'Password is required');
        isValid = false;
    }
    
    return isValid;
}

function showError(input, message) {
    const formGroup = input.closest('.form-group');
    const error = formGroup.querySelector('.error-message') || 
                 createErrorElement(formGroup);
    error.textContent = message;
}

function createErrorElement(formGroup) {
    const error = document.createElement('div');
    error.className = 'error-message';
    formGroup.appendChild(error);
    return error;
}

function clearErrors() {
    document.querySelectorAll('.error-message').forEach(error => {
        error.textContent = '';
    });
}