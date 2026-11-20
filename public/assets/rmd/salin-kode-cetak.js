const tombolSalinKode = document.getElementById('salin-kode-cetak');
const kodeBaru = document.getElementById('kode-cetak-baru');
const statusSalinKode = document.getElementById('status-salin-kode');

tombolSalinKode?.addEventListener('click', async () => {
  try {
    if (navigator.clipboard?.writeText) {
      await navigator.clipboard.writeText(kodeBaru.value);
    } else {
      kodeBaru.select();
      if (!document.execCommand('copy')) throw new Error();
    }

    statusSalinKode.textContent = 'Kode disalin.';
  } catch {
    kodeBaru.select();
    statusSalinKode.textContent = 'Kode sudah dipilih. Salin dengan Ctrl+C atau menu perangkat.';
  }
});
