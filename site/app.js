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


document.addEventListener('submit', (event) => {
  const form = event.target;
  if (!(form instanceof HTMLFormElement)) {
    return;
  }

  const message = form.dataset.confirm;
  if (message && !window.confirm(message)) {
    event.preventDefault();
  }
});


document.addEventListener('click', (event) => {
  const target = event.target;
  if (!(target instanceof HTMLElement)) {
    return;
  }

  const toggle = target.closest('[data-menu-toggle]');
  if (!toggle) {
    return;
  }

  const menu = document.getElementById('drawer-menu');
  if (menu) {
    menu.classList.toggle('is-open');
  }
});


document.addEventListener('change', (event) => {
  const target = event.target;
  if (!(target instanceof HTMLInputElement)) {
    return;
  }

  if (!target.matches('[data-toggle-passwords]')) {
    return;
  }

  const form = target.closest('form');
  if (!form) {
    return;
  }

  form.querySelectorAll('input[type="password"], input[data-password-visible="true"]').forEach((input) => {
    if (!(input instanceof HTMLInputElement)) {
      return;
    }

    if (target.checked) {
      input.dataset.passwordVisible = 'true';
      input.type = 'text';
    } else {
      input.type = 'password';
      delete input.dataset.passwordVisible;
    }
  });
});


/*
 * Convert UTC timestamps rendered by PHP to the browser's local date/time.
 * The browser decides both timezone and locale, which is ideal for PC and
 * smartphone clients in different local settings.
 */
document.querySelectorAll('[data-local-time]').forEach((element) => {
  if (!(element instanceof HTMLTimeElement) || !element.dateTime) {
    return;
  }

  const date = new Date(element.dateTime);

  if (Number.isNaN(date.getTime())) {
    return;
  }

  element.textContent = new Intl.DateTimeFormat(undefined, {
    dateStyle: 'short',
    timeStyle: 'short'
  }).format(date);

  element.title = element.dateTime;
});
