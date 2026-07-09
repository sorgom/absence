'use strict';

/*
 * Shared frontend bootstrap.
 * Handles the role-aware hamburger menu and password visibility toggles.
 */
document.documentElement.classList.add('js-enabled');

const menuToggle = document.querySelector('.menu-toggle');
const appMenu = document.querySelector('#app-menu');

if (menuToggle && appMenu) {
  menuToggle.addEventListener('click', () => {
    const isOpen = menuToggle.getAttribute('aria-expanded') === 'true';
    menuToggle.setAttribute('aria-expanded', String(!isOpen));
    appMenu.hidden = isOpen;
  });

  document.addEventListener('click', (event) => {
    if (!appMenu.hidden && !appMenu.contains(event.target) && !menuToggle.contains(event.target)) {
      menuToggle.setAttribute('aria-expanded', 'false');
      appMenu.hidden = true;
    }
  });
}

const showPasswords = document.querySelector('#show-passwords');
if (showPasswords) {
  showPasswords.addEventListener('change', () => {
    document.querySelectorAll('input[type="password"], input[data-password-field="true"]').forEach((input) => {
      input.type = showPasswords.checked ? 'text' : 'password';
      input.dataset.passwordField = 'true';
    });
  });
}
