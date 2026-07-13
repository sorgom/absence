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


/*
 * Temporary confirmation handling.
 * A custom design dialog will replace this in a later UI release.
 */
document.querySelectorAll('form[data-confirm-message]').forEach((form) => {
  form.addEventListener('submit', (event) => {
    const message = form.getAttribute('data-confirm-message') || 'Wirklich fortfahren?';

    if (!window.confirm(message)) {
      event.preventDefault();
    }
  });
});






/*
 * v0.9.5.2: minimal JS for CSS dropdown menu.
 *
 * CSS controls positioning and visibility. JavaScript only toggles .is-open
 * for click/touch devices and keeps aria-expanded in sync.
 */
(() => {
  const menu = document.getElementById('drawer-menu');
  const toggle = document.querySelector('[data-menu-toggle]');

  if (!menu || !toggle) {
    return;
  }

  const setOpen = (open) => {
    menu.classList.toggle('is-open', open);
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
  };

  toggle.addEventListener('click', (event) => {
    event.preventDefault();
    event.stopImmediatePropagation();
    setOpen(!menu.classList.contains('is-open'));
  }, true);

  document.addEventListener('click', (event) => {
    if (!menu.contains(event.target) && !toggle.contains(event.target)) {
      setOpen(false);
    }
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      setOpen(false);
    }
  });

  menu.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => setOpen(false));
  });

  setOpen(false);
})();
