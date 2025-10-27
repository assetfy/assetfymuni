// resources/js/soporte.js
import axios from 'axios';
import Swal  from 'sweetalert2';

axios.defaults.withCredentials    = true;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.xsrfCookieName = 'XSRF-TOKEN';
axios.defaults.xsrfHeaderName = 'X-XSRF-TOKEN';

document.addEventListener('DOMContentLoaded', () => {

  const tokenMeta = document.querySelector('meta[name="csrf-token"]');
  if (tokenMeta) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = tokenMeta.content;
  }

  const form = document.getElementById('soporte-form');
  if (!form) return;

  form.addEventListener('submit', async e => {
    e.preventDefault();

    const fd = new FormData(form);

    const target = form.action;

    try {
      const { data } = await axios.post(target, fd, {
        withCredentials: true,
        headers: { 'Accept': 'application/json' }
      });

      Swal.fire({
        title: data.title || '¡Listo!',
        text:  data.message,
        icon:  'success'
      });
      form.reset();
    } catch (err) {
      Swal.fire({
        title: 'Error',
        text:  err.response?.data?.message || err.message,
        icon:  'error'
      });
    }
  });
});
