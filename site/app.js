/*
 * Client-side helpers for the Abwesenheits-App.
 */

(() => {
  const showPasswords = document.querySelector('[data-show-passwords]');

  if (!showPasswords) {
    return;
  }

  const passwordFields = document.querySelectorAll('input[type="password"], input[data-password-field="true"]');

  showPasswords.addEventListener('change', () => {
    passwordFields.forEach((input) => {
      input.type = showPasswords.checked ? 'text' : 'password';
      input.dataset.passwordField = 'true';
    });
  });
})();

(() => {
  const formatter = new Intl.DateTimeFormat(undefined, {
    dateStyle: 'short',
    timeStyle: 'short',
  });

  document.querySelectorAll('[data-local-time]').forEach((element) => {
    const isoValue = element.getAttribute('datetime');

    if (!isoValue) {
      return;
    }

    const date = new Date(isoValue);

    if (Number.isNaN(date.getTime())) {
      return;
    }

    element.textContent = formatter.format(date);
  });
})();

/*
 * CSS dropdown menu.
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
    event.stopPropagation();
    setOpen(!menu.classList.contains('is-open'));
  });

  document.addEventListener('click', (event) => {
    const target = event.target;

    if (!(target instanceof Node)) {
      return;
    }

    if (!menu.contains(target) && !toggle.contains(target)) {
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

/*
 * Design confirmation modal for forms with data-confirm-message.
 */
(() => {
  const modal = document.getElementById('confirm-modal');

  if (!modal) {
    return;
  }

  const messageElement = document.getElementById('confirm-modal-message');
  const okButton = modal.querySelector('[data-confirm-ok]');
  const cancelButtons = modal.querySelectorAll('[data-confirm-cancel]');
  let pendingForm = null;
  let lastFocusedElement = null;

  const resolveMessage = (form) => {
    const fallback = form.getAttribute('data-confirm-message') || 'Möchten Sie fortfahren?';
    const template = form.getAttribute('data-confirm-template');

    if (!template) {
      return fallback;
    }

    let value = form.getAttribute('data-confirm-value') || '';
    const sourceSelector = form.getAttribute('data-confirm-value-source');

    if (sourceSelector) {
      const source = form.querySelector(sourceSelector) || document.querySelector(sourceSelector);

      if (source) {
        if ('selectedOptions' in source && source.selectedOptions.length > 0) {
          value = source.selectedOptions[0].textContent.trim();
        } else if ('value' in source) {
          value = String(source.value).trim();
        } else {
          value = source.textContent.trim();
        }
      }
    }

    value = value.trim();

    if (value === '') {
      return fallback;
    }

    return template.replaceAll('{value}', value);
  };

  const openModal = (form) => {
    pendingForm = form;
    lastFocusedElement = document.activeElement;
    messageElement.textContent = resolveMessage(form);
    modal.hidden = false;
    document.body.classList.add('confirm-modal-open');
    okButton.focus();
  };

  const closeModal = () => {
    modal.hidden = true;
    document.body.classList.remove('confirm-modal-open');
    pendingForm = null;

    if (lastFocusedElement && typeof lastFocusedElement.focus === 'function') {
      lastFocusedElement.focus();
    }
  };

  document.querySelectorAll('form[data-confirm-message]').forEach((form) => {
    form.addEventListener('submit', (event) => {
      if (form.dataset.confirmAccepted === '1') {
        delete form.dataset.confirmAccepted;
        return;
      }

      event.preventDefault();
      openModal(form);
    });
  });

  okButton.addEventListener('click', () => {
    if (!pendingForm) {
      closeModal();
      return;
    }

    const form = pendingForm;
    form.dataset.confirmAccepted = '1';
    closeModal();
    form.requestSubmit();
  });

  cancelButtons.forEach((button) => {
    button.addEventListener('click', closeModal);
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && !modal.hidden) {
      closeModal();
    }
  });
})();
