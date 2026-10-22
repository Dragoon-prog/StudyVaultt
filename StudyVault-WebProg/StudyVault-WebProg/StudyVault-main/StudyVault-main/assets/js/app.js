document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('form[data-validate]').forEach(form => {
    form.addEventListener('submit', e => {
      let valid = true;
      form.querySelectorAll('[required]').forEach(input => {
        if (!input.value.trim()) {
          input.style.borderColor = '#dc2626';
          valid = false;
        } else input.style.borderColor = '';
      });
      const file = form.querySelector('input[type=file]');
      if (file && file.files[0] && file.files[0].size > 10 * 1024 * 1024) {
        alert('Maximum file size is 10 MB.');
        valid = false;
      }
      if (!valid) e.preventDefault();
    });
  });
});
