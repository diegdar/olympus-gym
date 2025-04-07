document.addEventListener('DOMContentLoaded', () => {
    const setupTogglePassword = (inputId, toggleId) => {
        const input = document.getElementById(inputId);
        const toggle = document.getElementById(toggleId);

        if (!input || !toggle) return;

        toggle.addEventListener('click', () => {
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';

            toggle.classList.toggle('fa-eye', !isHidden);
            toggle.classList.toggle('fa-eye-slash', isHidden);
        });
    };

    setupTogglePassword('password', 'toggle-password');
    setupTogglePassword('password_confirmation', 'toggle-password-confirmation');
});
