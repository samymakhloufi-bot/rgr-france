document.addEventListener('DOMContentLoaded', () => {
    const passwordInput = document.querySelector('#password');
    const toggleButton = document.querySelector('[data-password-toggle]');

    if (!(passwordInput instanceof HTMLInputElement) || !(toggleButton instanceof HTMLButtonElement)) {
        return;
    }

    toggleButton.addEventListener('click', () => {
        const passwordIsVisible = passwordInput.type === 'text';

        passwordInput.type = passwordIsVisible ? 'password' : 'text';
        toggleButton.setAttribute('aria-pressed', String(!passwordIsVisible));
        toggleButton.setAttribute(
            'aria-label',
            passwordIsVisible ? 'Afficher le mot de passe' : 'Masquer le mot de passe'
        );

        passwordInput.focus();
    });
});