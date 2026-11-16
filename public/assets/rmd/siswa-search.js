const pencarianSiswa = document.getElementById('pencarian-siswa');
const kataSiswa = document.getElementById('kata-siswa');
const angkatanSiswa = document.getElementById('angkatan-siswa');
const panelSaran = document.getElementById('saran-siswa');
const opsiSiswa = document.getElementById('opsi-siswa');
const pesanSaran = document.getElementById('pesan-saran-siswa');
const statusSaran = document.getElementById('status-saran-siswa');

let jadwalSaran;
let permintaanSaran;
let tautanSiswa = [];
let opsiAktif = -1;

kataSiswa.setAttribute('role', 'combobox');
kataSiswa.setAttribute('aria-autocomplete', 'list');
kataSiswa.setAttribute('aria-controls', opsiSiswa.id);
kataSiswa.setAttribute('aria-expanded', 'false');

function tutupSaran() {
  clearTimeout(jadwalSaran);
  permintaanSaran?.abort();
  panelSaran.hidden = true;
  opsiSiswa.hidden = true;
  opsiSiswa.replaceChildren();
  pesanSaran.hidden = true;
  statusSaran.textContent = '';
  kataSiswa.setAttribute('aria-expanded', 'false');
  kataSiswa.removeAttribute('aria-activedescendant');
  tautanSiswa = [];
  opsiAktif = -1;
}

function tampilkanPesan(pesan) {
  pesanSaran.textContent = pesan;
  pesanSaran.hidden = false;
  panelSaran.hidden = false;
  statusSaran.textContent = pesan;
}

function tampilkanSaran(saran) {
  tautanSiswa = saran.map((siswa, i) => {
    const opsi = document.createElement('li');
    opsi.id = 'opsi-siswa-' + i;
    opsi.className = 'list-group-item';
    opsi.setAttribute('role', 'option');
    opsi.setAttribute('aria-selected', 'false');

    const nama = document.createElement('span');
    nama.className = 'd-block fw-semibold';
    nama.textContent = siswa.nama;

    const angkatan = document.createElement('small');
    angkatan.textContent = 'Angkatan ' + siswa.angkatan;
    opsi.append(nama, angkatan);
    opsiSiswa.append(opsi);

    return siswa.url;
  });

  opsiSiswa.hidden = false;
  panelSaran.hidden = false;
  kataSiswa.setAttribute('aria-expanded', 'true');
  statusSaran.textContent = saran.length + ' saran siswa tersedia.';
}

async function ambilSaran(kata, angkatan) {
  const kontrol = new AbortController();
  permintaanSaran = kontrol;
  const url = new URL(pencarianSiswa.dataset.urlSaran);
  url.searchParams.set('q', kata);
  url.searchParams.set('angkatan', angkatan);

  try {
    const respons = await fetch(url, { signal: kontrol.signal });
    if (!respons.ok) {
      throw new Error('Gagal mengambil saran.');
    }

    const saran = await respons.json();
    if (kontrol.signal.aborted) {
      return;
    }

    if (saran.length === 0) {
      tampilkanPesan('Tidak ada siswa yang cocok.');
    } else {
      tampilkanSaran(saran);
    }
  } catch {
    if (!kontrol.signal.aborted) {
      tampilkanPesan('Saran gagal dimuat. Gunakan tombol Cari.');
    }
  }
}

function jadwalkanSaran() {
  tutupSaran();
  const kata = kataSiswa.value.trim();
  if ([...kata].length >= 2) {
    jadwalSaran = setTimeout(() => ambilSaran(kata, angkatanSiswa.value), 250);
  }
}

kataSiswa.addEventListener('input', jadwalkanSaran);
kataSiswa.addEventListener('focus', () => {
  if (panelSaran.hidden) {
    jadwalkanSaran();
  }
});
angkatanSiswa.addEventListener('change', jadwalkanSaran);

kataSiswa.addEventListener('keydown', (event) => {
  if (event.isComposing) {
    return;
  }

  if (event.key === 'Escape') {
    if (!panelSaran.hidden) {
      event.preventDefault();
    }
    tutupSaran();
    return;
  }

  if (event.key === 'Enter' && opsiAktif >= 0) {
    event.preventDefault();
    window.location.assign(tautanSiswa[opsiAktif]);
    return;
  }

  if (!tautanSiswa.length || !['ArrowDown', 'ArrowUp'].includes(event.key)) {
    return;
  }

  event.preventDefault();
  opsiSiswa.children[opsiAktif]?.setAttribute('aria-selected', 'false');
  const terakhir = tautanSiswa.length - 1;
  opsiAktif = event.key === 'ArrowDown'
    ? Math.min(opsiAktif + 1, terakhir)
    : (opsiAktif < 0 ? terakhir : Math.max(0, opsiAktif - 1));

  const opsi = opsiSiswa.children[opsiAktif];
  opsi.setAttribute('aria-selected', 'true');
  kataSiswa.setAttribute('aria-activedescendant', opsi.id);
  opsi.scrollIntoView({ block: 'nearest' });
});

opsiSiswa.addEventListener('click', (event) => {
  const opsi = event.target.closest('[role="option"]');
  if (opsi !== null) {
    window.location.assign(tautanSiswa[Array.from(opsiSiswa.children).indexOf(opsi)]);
  }
});

opsiSiswa.addEventListener('mousedown', (event) => event.preventDefault());

document.addEventListener('pointerdown', (event) => {
  if (!pencarianSiswa.contains(event.target)) {
    tutupSaran();
  }
});

document.addEventListener('focusin', (event) => {
  if (event.target !== angkatanSiswa && !pencarianSiswa.contains(event.target)) {
    tutupSaran();
  }
});
