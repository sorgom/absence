'use strict';
document.documentElement.classList.add('js-enabled');
const toggle = document.querySelector('#show-passwords');
if (toggle) {
  toggle.addEventListener('change', () => {
    document.querySelectorAll('input[type="password"], input[data-password-field="true"]').forEach((field) => {
      field.dataset.passwordField = 'true';
      field.type = toggle.checked ? 'text' : 'password';
    });
  });
}
