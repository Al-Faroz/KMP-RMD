const modalCetak = document.getElementById('modal-kode-cetak');
const formCetak = document.getElementById('form-kode-cetak');
const inputKode = document.getElementById('kode-cetak');
const pesanCetak = document.getElementById('pesan-kode-cetak');
const tombolCetak = formCetak.querySelector('[type="submit"]');
let permintaanCetak;

modalCetak.addEventListener('show.bs.modal', (event) => {
  formCetak.action = event.relatedTarget.dataset.urlCetak;
  formCetak.reset();
  pesanCetak.hidden = true;
});

modalCetak.addEventListener('shown.bs.modal', () => inputKode.focus());
modalCetak.addEventListener('hide.bs.modal', () => {
  permintaanCetak?.abort();
  permintaanCetak = null;
  tombolCetak.disabled = false;
  formCetak.reset();
});

formCetak.addEventListener('submit', async (event) => {
  event.preventDefault();
  if (permintaanCetak) return;

  const tabPdf = window.open('', '_blank');
  if (tabPdf) tabPdf.opener = null;
  const kontrol = new AbortController();
  permintaanCetak = kontrol;
  pesanCetak.hidden = true;
  tombolCetak.disabled = true;

  try {
    const respons = await fetch(formCetak.action, {
      method: 'POST',
      body: new URLSearchParams(new FormData(formCetak)),
      signal: kontrol.signal
    });

    if (!respons.ok || !respons.headers.get('content-type')?.startsWith('application/pdf')) {
      const pesan = [403, 429, 503].includes(respons.status) && respons.headers.get('content-type')?.startsWith('text/plain')
        ? await respons.text()
        : 'PDF tidak dapat dibuka. Coba lagi.';
      throw new Error(pesan);
    }

    const pdf = await respons.blob();
    if (kontrol.signal.aborted) return;
    const url = URL.createObjectURL(pdf);
    if (tabPdf && !tabPdf.closed) {
      tabPdf.location.replace(url);
    } else {
      const tautan = document.createElement('a');
      const nama = respons.headers.get('content-disposition')?.match(/filename="([^"]+)"/);
      tautan.href = url;
      tautan.download = nama?.[1] ?? 'Dokumen-RMD.pdf';
      document.body.append(tautan);
      tautan.click();
      tautan.remove();
    }
    setTimeout(() => URL.revokeObjectURL(url), 60000);
    permintaanCetak = null;
    bootstrap.Modal.getInstance(modalCetak).hide();
  } catch (galat) {
    if (!kontrol.signal.aborted) {
      pesanCetak.textContent = galat instanceof TypeError ? 'PDF gagal dimuat. Coba lagi.' : galat.message;
      pesanCetak.hidden = false;
      inputKode.focus();
    }
  } finally {
    if (kontrol.signal.aborted || permintaanCetak === kontrol) {
      tabPdf?.close();
    }
    if (permintaanCetak === kontrol) {
      permintaanCetak = null;
      tombolCetak.disabled = false;
    }
  }
});
