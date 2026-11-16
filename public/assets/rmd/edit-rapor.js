const editRapor = document.getElementById('form-edit-rapor');

editRapor.querySelectorAll('details').forEach((details) => {
  if (!details.open) {
    details.querySelectorAll('input, textarea').forEach((input) => { input.disabled = true; });
  }
});

editRapor.addEventListener('toggle', (event) => {
  if (event.target.matches('details')) {
    event.target.querySelectorAll('input, textarea').forEach((input) => {
      if (event.target.open) input.disabled = false;
    });
  }
}, true);

editRapor.addEventListener('click', (event) => {
  const bagian = event.target.closest('[data-bagian-rapor]');
  if (!bagian) return;

  if (event.target.matches('[data-tambah-baris]')) {
    const index = Number(bagian.dataset.nextIndex);
    const isi = bagian.querySelector('[data-template-baris]').innerHTML.replaceAll('__index__', index);
    const daftar = bagian.querySelector('[data-daftar-baris]');
    daftar.insertAdjacentHTML('beforeend', isi);
    bagian.dataset.nextIndex = index + 1;
    daftar.lastElementChild.open = true;
    daftar.lastElementChild.querySelector('input:not([type="hidden"])').focus();
  }

  if (event.target.matches('[data-hapus-baris]')) {
    event.target.closest('[data-baris-rapor]').remove();
    bagian.querySelector('[data-tambah-baris]').focus();
  }
});
