for (const tombol of document.querySelectorAll('[data-salin-kode]')) {
  const kode = document.getElementById(tombol.dataset.salinKode);
  const status = document.getElementById(tombol.getAttribute('aria-describedby'));

  tombol.addEventListener('click', async () => {
    try {
      if (navigator.clipboard?.writeText) {
        await navigator.clipboard.writeText(kode.value);
      } else {
        kode.select();
        if (!document.execCommand('copy')) throw new Error();
      }

      status.textContent = 'Kode disalin.';
    } catch {
      kode.select();
      status.textContent = 'Kode sudah dipilih. Salin dengan Ctrl+C atau menu perangkat.';
    }
  });
}
